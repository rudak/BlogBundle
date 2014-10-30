<?php
/**
 * Created by PhpStorm.
 * User: Rudak
 * Date: 30/10/2014
 * Time: 18:17
 */

namespace Rudak\BlogBundle\Utils\Uploader;

use Rudak\BlogBundle\Utils\Resizer;

class UploaderModel
{
    protected $file;
    protected $maxfilesize;
    protected $minfilesize;
    protected $allowedMimetypes;
    protected $directory;
    protected $destination;
    protected $newname;
    protected $extension;
    protected $upload_result;
    protected $error;
    protected $resize;
    protected $newSize = array();

    /*
     * LOance l'opération de redimensionement
     */
    protected function resizeUploadedFile()
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

    protected function checkUploadError()
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

    /*
     * Envoi du fichier temporaire vers la destination adéquate
     */
    protected function moveUploadedFile()
    {
        return @move_uploaded_file($this->file['tmp_name'], $this->getAbsolutePath());
    }

    /*
     * Récupération de l'extension du fichier
     */
    protected function setFileExtension()
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

    /*
     * Liste des mime types images connus.
     */
    protected function getAllowedMimetypes()
    {
        if (null == $this->allowedMimetypes) {
            return array('image/jpeg', 'image/pjpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff');
        } else {
            return $this->allowedMimetypes;
        }
    }

    /*
     * Vérifie la destination donnée en parametres
     */
    protected function checkDestination($dir)
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

    protected function getFileName()
    {
        return $this->newname . '.' . $this->extension;
    }

    /*
     * Vérifie la taille fournie en parametre
     */
    protected function checkMaxFilesize($int)
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
    protected function isFilesizeValid()
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

    protected function throwExceptions($message)
    {
        if (Uploader::DEBUG_UPLOADER) {
            throw new \InvalidArgumentException($message);
        }
    }


} 