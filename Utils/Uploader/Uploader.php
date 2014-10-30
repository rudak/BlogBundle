<?php
namespace Rudak\BlogBundle\Utils\Uploader;

use Rudak\BlogBundle\Utils\Uploader\UploaderModel;
use Rudak\BlogBundle\Utils\Resizer;

class Uploader extends UploaderModel
{

    const FILE_INDEX = 'index';
    const MAX_FILE_SIZE = 'max_file_size';
    const MIN_FILE_SIZE = 'min_file_size';
    const UPLOAD_DIR = 'upload_dir';
    const NEW_NAME = 'new_name';
    const NEW_WIDTH = 'new_width';
    const NEW_HEIGHT = 'new_height';
    const RESIZE_QUALITY = 'resize_quality';

    const DEBUG_UPLOADER = false;

    function __construct(array $config = null)
    {
        $this->maxfilesize   = 6 * 1024 * 1024;
        $this->minfilesize   = 0;
        $this->destination   = realpath(__DIR__);
        $this->upload_result = 'Pending...';
        $this->resize        = false;

        if (isset($config[Uploader::FILE_INDEX])) {
            $this->file = $_FILES[$config[Uploader::FILE_INDEX]];
        }
        if (isset($config[Uploader::MAX_FILE_SIZE])) {
            $this->maxfilesize = $config[Uploader::MAX_FILE_SIZE];
        }
        if (isset($config[Uploader::MIN_FILE_SIZE])) {
            $this->minfilesize = $config[Uploader::MIN_FILE_SIZE];
        }
        if (isset($config[Uploader::UPLOAD_DIR])) {
            $this->destination = $config[Uploader::UPLOAD_DIR];
        }
        if (isset($config[Uploader::NEW_NAME])) {
            $this->newname = $config[Uploader::NEW_NAME];
        }
        if (isset($config[Uploader::NEW_WIDTH])) {
            $this->newSize['width'] = $config[Uploader::NEW_WIDTH];
        }
        if (isset($config[Uploader::NEW_HEIGHT])) {
            $this->newSize['height'] = $config[Uploader::NEW_HEIGHT];
        }
        if (isset($config[Uploader::RESIZE_QUALITY])) {
            $this->newSize['quality'] = $config[Uploader::RESIZE_QUALITY];
        }
    }

    /*
     * Lance l'opération d'upload
     */
    public function process()
    {
        $this->checkUploadError();

        if ($this->is_error()) {
            return false;
        }

        $this->isFilesizeValid();
        $this->setFileExtension();

        if ($this->moveUploadedFile()) {
            $this->upload_result = 'Success';
        }
        if ($this->resize) {
            $this->resizeUploadedFile();
        }
    }

    /**
     * Ajouter un ou plusieurs (array) types mime autorisés.
     * @param $mimetype
     */
    public function addAllowedMimeType($mimetype)
    {
        if (!in_array(strtolower($mimetype), $this->getAllowedMimetypes())) {
            $allowedMimeType        = $this->getAllowedMimetypes();
            $allowedMimeType[]      = $mimetype;
            $this->allowedMimetypes = $allowedMimeType;
        }
    }

    /*
     * Crée le chemin absolu de destination
     */
    public function setDestination($dir)
    {
        $dir = rtrim(rtrim($dir, '/'), '\\');

        if ($this->checkDestination($dir)) {
            $this->directory   = $dir;
            $this->destination = realpath($dir);
        } else {
            $this->error++;
            return false;
        }

        return $this;
    }

    /*
     * Renvoie le chemin complet, incluant le nouveau fichier
     */
    public function getAbsolutePath()
    {
        return $this->destination . '/' . $this->getFileName();
    }

    public function getWebPath()
    {
        return $this->directory . '/' . $this->getFileName();
    }

#TODO methode min file size
#TODO ajouter possibilité de prefixer les fichier

    /**
     * Set the max size in Mo
     * @param $int
     */
    public function setMaxFilesize($int = 0)
    {
        if ($this->checkMaxFilesize($int)) {
            $this->maxfilesize = $int * 1024 * 1024;
        }
    }

    /*
     * Définition d'un nouveau nom de fichier
     */
    public function setNewName($str = null, $length = 8)
    {
        if (null != $str) {
            $this->newname = $str;
        } else {
            if ($length < 3) {
                $this->error++;
                $this->upload_result = 'Newname error';
                $this->throwExceptions('La taille de la chaine générée doit etre supérieure à 3 caractères !');
            } elseif ($length > 26) {
                $this->error++;
                $this->upload_result = 'Newname error';
                $this->throwExceptions('La taille de la chaine générée doit etre inférieure à 26 caractères !');
            }
            $alpha         = 'abcdefghijklmnopqrstuvwxyz';
            $newstr        = str_repeat(str_shuffle($alpha . strtoupper($alpha) . '0123456789'), 2);
            $this->newname = substr($newstr, rand(0, 26), $length);
            unset($alpha);
            unset($newstr);
        }
        return $this;
    }

    public function useResizer()
    {
        $this->resize = true;
        return $this;
    }

    public function getUploadResult()
    {
        return $this->upload_result;
    }


    public function is_error()
    {
        return ($this->error > 0) ? true : false;
    }

    public static function is_file_uploaded()
    {
        return count($_FILES);
    }

    /**
     * Vérifie si un fichier est vraiment une image ou alors une saloperie
     *
     * @param type $image_path
     * @return boolean|string
     */
    public static function is_image($image_path)
    {
        if (!$f = fopen($image_path, 'rb')) {
            return false;
        }

        $data = fread($f, 8);
        fclose($f);

        $unpacked = unpack("H12", $data);
        if (array_pop($unpacked) == '474946383961' || array_pop($unpacked) == '474946383761') {
            return "gif";
        }
        $unpacked = unpack("H4", $data);
        if (array_pop($unpacked) == 'ffd8') {
            return "jpg";
        }
        $unpacked = unpack("H16", $data);
        if (array_pop($unpacked) == '89504e470d0a1a0a') {
            return "png";
        }
        return false;
    }


}