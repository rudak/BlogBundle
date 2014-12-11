<?php

namespace Rudak\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Rudak\UtilsBundle\Uploader\Uploader;

class UploadController extends Controller
{
    public function uploadPictureAction()
    {
        $config = array(
            Uploader::DIR               => 'uploads/post_inserts',
            Uploader::FILE_INDEX        => 'file',
            Uploader::UPLOAD_MAX_SIZE   => '6Mo',
            Uploader::UPLOAD_MIN_SIZE   => '50ko',
            Uploader::UPLOAD_MIN_WIDTH  => 350,
            Uploader::UPLOAD_MIN_HEIGHT => 400,
            Uploader::RESIZE_NEW_WIDTH  => 600,
            Uploader::RESIZE_NEW_HEIGHT => 600,
            Uploader::RESIZE_QUALITY    => 65,
            Uploader::NEWNAME_PREFIX    => 'rcf_' // Redactor Content File
        );

        $response = array();
        $Uploader = new Uploader($config);

        if ($Uploader->is_a_file_uploaded()) {
            if ($Uploader->indexExists()) {
                if (!$Uploader->checkUploadError()) {
                    if ($Uploader->isSizeOk()) {
                        if ($Uploader->isWidthOk()) {
                            if ($Uploader->isDirExists()) {
                                if ($Uploader->moveTheFile()) {
                                    $response['filelink'] = $this->get_asset_url($Uploader->getWebPath(), null);
                                    if ($Uploader->checkResizeValues()) {
                                        $Uploader->resizeIt();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($Uploader->getDebugValue()) {
            $response['error'] = $Uploader->getDebugValue();
        }

        return new Response(stripslashes(json_encode($response)));
    }

    private function get_asset_url($path)
    {
        return $this->container->get('templating.helper.assets')->getUrl($path, null);
    }
} 