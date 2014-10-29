<?php
namespace Rudak\BlogBundle\Utils;


class Uploader
{

    const FILE_INDEX = 'index';
    const MAX_FILE_SIZE = 'max_file_size';
    const MIN_FILE_SIZE = 'min_file_size';
    const UPLOAD_DIR = 'upload_dir';
    const NEW_NAME = 'new_name';


    private $file;
    private $maxfilesize;
    private $minfilesize;
    private $destination;
    private $newname;
    private $extension;
    private $allowedMimetypes;
    private $upload_result;

    function __construct(array $config = null)
    {
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
        $this->allowedMimetypes = array();
        $this->destination      = realpath(__DIR__);
        $this->maxfilesize      = 6 * 1024 * 1024;
        $this->minfilesize      = 0;
        $this->upload_result    = 'Pending...';
    }

    public function process()
    {
        $this->isFilesizeValid();
        $this->setFileExtension();
        if ($this->moveUploadedFile()) {
            $this->upload_result = 'Success';
        }
    }

    private function moveUploadedFile()
    {
        return @move_uploaded_file($this->file['tmp_name'], $this->getAbsoluteDestination());
    }

    private function setFileExtension()
    {
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $this->file['tmp_name']);
        finfo_close($finfo);
        if (in_array($mimetype, $this->allowedMimetypes)) {
            switch ($mimetype) {
                case 'image/jpeg';
                    $this->extension = 'jpg';
                    break;
                case 'image/jpg';
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
            throw new \InvalidArgumentException('Le type mime "' . $mimetype . '" ne permet pas de récuperer l\'extension');
        }
    }

    /**
     * Ajouter un ou plusieurs (array) types mime autorisés.
     * @param $mimetype
     */
    public function addAllowedMimeType($mimetype)
    {
        if (is_array($mimetype)) {
            foreach ($mimetype as $mime) {
                if ($this->checkMimeType($mime)) {
                    $this->allowedMimetypes[] = $mime;
                } else {
                    throw new \InvalidArgumentException('Le type mime "' . $mime . '" est invalide.');
                }
            }
        } else {
            if ($this->checkMimeType($mimetype)) {
                $this->allowedMimetypes[] = $mimetype;
            } else {
                throw new \InvalidArgumentException('Le type mime "' . $mimetype . '" est invalide.');
            }
        }
    }

    /*
     * Vérifie si le mime type ajouté est connu (autorisé).
     */
    private function checkMimeType($mimetype)
    {
        return in_array($mimetype, $this->getImagesMimetypes());
    }

    /*
     * Liste des mime types images connus.
     */
    private function getImagesMimetypes()
    {
        return array('image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff');
    }

    /*
     * Crée le chemin absolu de destination
     */
    public function setDestination($dir)
    {
        if ($this->checkDestination($dir)) {
            $this->destination = realpath($dir);
        }
    }

    /*
     * Vérifie la destination donnée en parametres
     */
    private function checkDestination($dir)
    {
        if (!file_exists($dir)) {
            throw new \InvalidArgumentException(sprintf('Le répertoire "%s" n\'existe pas.', $dir));
        }
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('Le répertoire "%s" n\'est pas un dossier.', $dir));
        }
        if (!is_writable($dir)) {
            throw new \InvalidArgumentException(sprintf('Le répertoire "%s" n\'est pas writable.', $dir));
        }
        return true;
    }

    /*
     * Renvoie le chemin complet, incluant le nouveau fichier
     */
    private function getAbsoluteDestination()
    {
        return $this->destination . DIRECTORY_SEPARATOR . $this->newname . '.' . $this->extension;
    }

    #todo methode min file size
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
        if (is_numeric($int) && $int > 0) {
            return true;
        } else {
            throw new \InvalidArgumentException('La taille maximum doit etre spécifiée en Mo');
        }
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
                throw new \InvalidArgumentException('Taille du fichier inférieure au minimum requis.');
            }
        } else {
            throw new \InvalidArgumentException('Taille du fichier supérieure au maximum requis');
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
                throw new \InvalidArgumentException('La taille de la chaine générée doit etre supérieure à 3 caractères !');
            } elseif ($length > 26) {
                throw new \InvalidArgumentException('La taille de la chaine générée doit etre inférieure à 26 caractères !');
            }
            $alpha         = 'abcdefghijklmnopqrstuvwxyz';
            $newstr        = str_repeat(str_shuffle($alpha . strtoupper($alpha) . '0123456789'), 2);
            $this->newname = substr($newstr, rand(0, 26), $length);
            unset($alpha);
            unset($newstr);
        }
    }
} 