<?php

namespace Rudak\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Rudak\BlogBundle\Utils\Uploader\Uploader;

class UploadController extends Controller
{
    public function uploadPictureAction()
    {



        if (Uploader::is_file_uploaded()) {

            $upload = new Uploader(array(
                'index'          => 'file',
                'new_width'      => 600,
                'new_height'     => 600,
                'resize_quality' => 65
            ));

            $upload->setNewName();
            $upload->setDestination('uploads/test');
            $upload->useResizer();
            $upload->process();

            $response = array(
                'filelink' => $this->get_asset_url($upload->getWebPath())
            );
            if ($upload->is_error()) {
                $response['error'] = $upload->getUploadResult();
            }
        } else {
            $response['error'] = 'No uploaded files.'; // (Check the size limit ?)
        }
        return new Response(stripslashes(json_encode($response)));
    }


    private function get_asset_url($path)
    {
        return $this->container->get('templating.helper.assets')->getUrl($path, null);
    }
} 