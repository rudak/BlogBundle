<?php
namespace Rudak\BlogBundle\Utils\Uploader;

use Rudak\ResizerBundle\Utils\Resizer;

class Uploader
{
    const DIR = 'dir';
    const FILE_INDEX = 'file';
    const UPLOAD_MAX_SIZE = 'max_size';
    const UPLOAD_MIN_SIZE = 'min_size';
    const UPLOAD_MIN_HEIGHT = 'min_height';
    const UPLOAD_MIN_WIDTH = 'min_width';
    const NEWNAME_PREFIX = 'prefix';
    const RESIZE_NEW_WIDTH = 'new_width';
    const RESIZE_NEW_HEIGHT = 'new_height';
    const RESIZE_QUALITY = 'resize_quality';

    private $file;
    private $config;
    private $newName;
    private $debug;
    private $extension;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function is_a_file_uploaded()
    {
        if (null != $_FILES) {
            return true;
        } else {
            $this->debug = 'No files uploaded.';
            return false;
        }

    }

    public function indexExists()
    {
        if (isset($_FILES[$this->config[self::FILE_INDEX]])) {
            $this->file = $_FILES[$this->config[self::FILE_INDEX]];
        } else {
            $this->debug = sprintf('Index "%s" doesn\'t exist in $_FILES array.', $this->config[self::FILE_INDEX]);
            return false;
        }
        return true;
    }

    public function checkUploadError()
    {

        switch ($this->file['error']) {
            case UPLOAD_ERR_OK:
                return false;
                break;
            case UPLOAD_ERR_INI_SIZE:
                $this->debug = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $this->debug = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $this->debug = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $this->debug = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $this->debug = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $this->debug = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $this->debug = "File upload stopped by extension";
                break;
            default:
                $this->debug = "Unknown upload error";
                break;
        }
        return true;
    }


    public function isSizeOk()
    {
        $minOctets = $this->getStringToOctets($this->config[self::UPLOAD_MIN_SIZE]);
        $maxOctets = $this->getStringToOctets($this->config[self::UPLOAD_MAX_SIZE]);
        if ($minOctets >= $this->file['size']) {
            $this->debug = sprintf('The file is not big enough (%s)', $this->file['size']);
            return false;
        }
        if ($maxOctets < $this->file['size']) {
            $this->debug = sprintf('The file is too big (%s)', $this->file['size']);
            return false;
        }
        return true;
    }

    public function moveTheFile()
    {
        $this->setNewName();
        $result = move_uploaded_file($this->file['tmp_name'], $this->getAbsoluteFile());
        if ($result) {
            return true;
        } else {
            $this->debug = sprintf('Impossible de deplacer le fichier temporaire vers "%s"', $this->getAbsoluteFile());
        }
    }

    public function isDirExists()
    {
        $dir = $this->getAbsoluteDir();

        if (!file_exists($dir)) {
            $this->debug = sprintf('Ce chemin "%s" n\'existe pas.', $dir);
            return false;
        }
        if (!is_dir($dir)) {
            $this->debug = sprintf('Ce chemin "%s" ne pointe pas vers un dossier.', $dir);
            return false;
        }
        if (!is_writable($dir)) {
            $this->debug = sprintf('Ce chemin "%s" n\'est pas writable.', $dir);
            return false;
        }
        return true;
    }

    public function setNewName($length = 8)
    {
        $this->setFileExtension();
        $alpha         = 'abcdefghijklmnopqrstuvwxyz';
        $newstr        = str_repeat(str_shuffle($alpha . strtoupper($alpha) . '0123456789'), 2);
        $this->newName = $this->getPrefix() . substr($newstr, rand(0, 26), $length) . '.' . $this->extension;
    }

    private function getPrefix()
    {
        return isset($this->config[self::NEWNAME_PREFIX]) ? $this->config[self::NEWNAME_PREFIX] : null;
    }


    private function setFileExtension()
    {
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $this->file['tmp_name']);
        finfo_close($finfo);

        switch ($mimetype) {
            case 'image/jpeg':
                $this->extension = 'jpg';
                break;
            case 'image/jpg':
                $this->extension = 'jpg';
                break;
            case 'image/pjpeg':
                $this->extension = 'jpg';
                break;
            case 'image/png':
                $this->extension = 'png';
                break;
            case 'image/gif':
                $this->extension = 'gif';
                break;
            default:
                $this->extension = 'xxx';
                break;
        }
    }

    private function getAbsoluteDir()
    {
        $path = './' . $this->config[self::DIR];
        return realpath($path);
    }

    public function getAbsoluteFile()
    {
        return $this->getAbsoluteDir() . '/' . $this->newName;
    }

    public function getWebPath()
    {
        return $this->config[self::DIR] . '/' . $this->newName;
    }

    public function isWidthOk()
    {
        $iz = getimagesize($this->file['tmp_name']);
        if ($this->config[self::UPLOAD_MIN_WIDTH] > $iz[0]) {
            $this->debug = sprintf('Width to small (%spx)', $iz[0]);
            return false;
        }
        if ($this->config[self::UPLOAD_MIN_HEIGHT] > $iz[1]) {
            $this->debug = sprintf('Height to small (%spx)', $iz[1]);
            return false;
        }
        return true;
    }

    private function getStringToOctets($str)
    {
        $kilo = 1024;
        $mega = $kilo * 1024;

        $str = strtolower($str);

        if (strpos($str, 'ko')) {
            return intval($str) * $kilo;
        } else if (strpos($str, 'mo')) {
            return intval($str) * $mega;
        } else {
            return intval($str);
        }
    }

    public function checkResizeValues()
    {
        return (isset($this->config[self::RESIZE_NEW_WIDTH]) && isset($this->config[self::RESIZE_NEW_HEIGHT]) && isset($this->config[self::RESIZE_QUALITY])) ? true : false;
    }

    public function resizeIt()
    {
        $Resizer = new Resizer($this->getAbsoluteFile());
        $Resizer->resizeImage($this->config[self::RESIZE_NEW_WIDTH], $this->config[self::RESIZE_NEW_HEIGHT]);

        return $Resizer->saveImage($this->getAbsoluteFile()) ? true : false;
    }

    public function getDebugValue()
    {
        return $this->debug;
    }

    public function getDefaultOptions()
    {
        return array(
            'dir'            => 'uploads/redactor_files',
            'index'          => 'file',
            'max_size'       => '6Mo',
            'min_size'       => '150ko',
            'min_width'      => 350,
            'min_height'     => 400,
            'new_width'      => 600,
            'new_height'     => 600,
            'resize_quality' => 65,
            'prefix'         => 'abcd_'
        );
    }
}