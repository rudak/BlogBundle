<?php
/**
 * Created by PhpStorm.
 * User: Rudak
 * Date: 08/10/14
 * Time: 23:59
 */

namespace Rudak\BlogBundle\Interfaces;


interface PictureInterface
{
    /**
     * Sets file.
     */
    public function setFile();

    /**
     * Get file.
     */
    public function getFile();
    public function preUpload();

    public function upload();
    public function removeUpload();

    public function compressFile();

    public function getAbsolutePath();
    public function getWebPath();
    public function getUploadRootDir();
    public function getUploadDir();

    public function getId();
    public function setName($name);
    public function getName();
    public function setPath($path);
    public function getPath();
} 