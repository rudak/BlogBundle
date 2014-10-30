<?php
namespace Rudak\BlogBundle\Utils\Uploader;

use Rudak\BlogBundle\Utils\Uploader\UploaderModel;
use Rudak\BlogBundle\Utils\Resizer;

class Uploader
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


    private $file;
    private $maxfilesize;
    private $minfilesize;
    private $allowedMimetypes;
    private $directory;
    private $destination;
    private $newname;
    private $extension;
    private $upload_result;
    private $error;
    private $resize;
    private $newSize = array();


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

    /*
     * LOance l'opération de redimensionement
     */
    private function resizeUploadedFile()
    {
        $Resizer = new Resizer($this->getAbsolutePath());
        $Resizer->resizeImage($this->newSize['width'], $this->newSize['height']);
        if ($Resizer->saveImage($this->getAbsolutePath())) {
            $this->upload_result .= ' & Resized';
        } else {
            $this->error++;
            $this->upload_result = 'resize error';
        }
    }

    /*
     * Envoi du fichier temporaire vers la destination adéquate
     */
    private function moveUploadedFile()
    {
        return @move_uploaded_file($this->file['tmp_name'], $this->getAbsolutePath());
    }

    /*
     * Récupération de l'extension du fichier
     */
    private function setFileExtension()
    {
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $this->file['tmp_name']);
        finfo_close($finfo);
        if (in_array($mimetype, $this->getAllowedMimetypes())) {
            switch ($mimetype) {
                case 'image/jpeg';
                    $this->extension = 'jpg';
                    break;
                case 'image/jpg';
                    $this->extension = 'jpg';
                    break;
                case 'image/pjpeg';
                    $this->extension = 'jpg';
                    break;
                case 'image/png';
                    $this->extension = 'png';
                    break;
                case 'image/gif';
                    $this->extension = 'gif';
                    break;
            }
        } else {
            $this->error++;
            $this->upload_result = 'Extension error';
            $this->throwExceptions('Le type mime "' . $mimetype . '" ne permet pas de récuperer l\'extension');
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
     * Liste des mime types images connus.
     */
    private function getAllowedMimetypes()
    {
        if (null == $this->allowedMimetypes) {
            return array('image/jpeg', 'image/pjpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff');
        } else {
            return $this->allowedMimetypes;
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
     * Vérifie la destination donnée en parametres
     */
    private function checkDestination($dir)
    {
        if (!file_exists($dir)) {
            $this->upload_result = 'Destination error';
            $this->throwExceptions(sprintf('Le répertoire "%s" n\'existe pas.', $dir));
            return false;
        }
        if (!is_dir($dir)) {
            $this->upload_result = 'Destination error';
            $this->throwExceptions(sprintf('Le répertoire "%s" n\'est pas un dossier.', $dir));
            return false;
        }
        if (!is_writable($dir)) {
            $this->upload_result = 'Destination error';
            $this->throwExceptions(sprintf('Le répertoire "%s" n\'est pas writable.', $dir));
            return false;
        }
        return true;
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

    private function getFileName()
    {
        return $this->newname . '.' . $this->extension;
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
     * Vérifie la taille fournie en parametre
     */
    private function checkMaxFilesize($int)
    {
        if (!(is_numeric($int) && $int > 0 && $int < 14)) {
            $this->error++;
            $this->upload_result = 'Size argument error';
            $this->throwExceptions('La taille maximum doit etre spécifiée en Mo');
        }
        return true;
    }

    /*
     * Vérifie si la taille du fichier est dans la tolérance définie par min et max
     */
    private function isFilesizeValid()
    {
        if ($this->maxfilesize >= $this->file['size']) {
            if ($this->minfilesize <= $this->file['size']) {
                return;
            } else {
                $this->error++;
                $this->upload_result = 'Filesize error';
                $this->throwExceptions('Taille du fichier inférieure au minimum requis.');
            }
        } else {
            $this->error++;
            $this->upload_result = 'Filesize error';
            $this->throwExceptions('Taille du fichier supérieure au maximum requis');
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

    private function throwExceptions($message)
    {
        if (Uploader::DEBUG_UPLOADER) {
            throw new \InvalidArgumentException($message);
        }
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


    private function checkUploadError()
    {

        switch ($this->file['error']) {
            case UPLOAD_ERR_OK:
                return;
                break;
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;
            default:
                $message = "Unknown upload error";
                break;
        }
        $this->error++;
        $this->upload_result = $message;
    }
} 