<?php

namespace Rudak\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Rudak\UtilsBundle\Uploader\Uploader;

class UploadController extends Controller
{
    const NON_USED_IMAGES = 'non_used_images';
    const POST_INSERTS_DIRECTORY = 'uploads/post_inserts';

    private static $session;

    public function uploadPictureAction()
    {
        $config = array(
            Uploader::DIR                     => self::POST_INSERTS_DIRECTORY,
            Uploader::FILE_INDEX              => 'file',
            Uploader::UPLOAD_MAX_SIZE         => '6Mo',
            Uploader::UPLOAD_MIN_SIZE         => '30ko',
            Uploader::UPLOAD_MIN_WIDTH        => 250,
            Uploader::UPLOAD_MIN_HEIGHT       => 250,
            Uploader::RESIZE_NEW_WIDTH        => 800,
            Uploader::RESIZE_NEW_HEIGHT       => 800,
            Uploader::RESIZE_QUALITY          => 80,
            Uploader::RESIZE_THUMB_NEW_WIDTH  => 250,
            Uploader::RESIZE_THUMB_NEW_HEIGHT => 250,
            Uploader::RESIZE_THUMB_QUALITY    => 30,
            Uploader::THUMB_PREFIX            => 'thumb_',
            Uploader::NEWNAME_PREFIX          => 'rcf_'
        );

        $response = array();
        $Uploader = new Uploader($config);

        if ($Uploader->is_a_file_uploaded()) {
            if ($Uploader->indexExists()) {
                if (!$Uploader->checkUploadError()) {
                    if ($Uploader->isSizeOk()) {
                        if ($Uploader->isWidthOk()) {
                            if ($Uploader->isDirExists()) {
                                if ($Uploader->setNewName()->moveTheFile()) {
                                    if ($Uploader->checkResizeValues()) {
                                        $Uploader->resizeIt();
                                        $Uploader->resizeIt(true);
                                        $response['filelink'] = $this->get_asset_url($Uploader->getWebPath(), null);
                                        $response['id']       = 'img_' . rand(120, 999999);
                                        $response['thumb']    = $this->get_asset_url($Uploader->getWebPath(true), null);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($Uploader->getDebugValue()) {
            $response['error']   = true;
            $response['message'] = $Uploader->getDebugValue();

        }

        return new Response(json_encode($response));
    }

    private function get_asset_url($path)
    {
        return $this->container->get('templating.helper.assets')->getUrl($path, null);
    }

    public function addDeletedImageAction(Request $request)
    {
        $token    = $request->get('own_token');
        $webPath  = $request->get('webpath');
        $filename = basename($webPath);
        $session  = $request->getSession();

        if (NULL === $list = $session->get(self::NON_USED_IMAGES)) {
            $list = array(
                $token => array(),
            );
        }
        $list[$token][] = $filename;
        $session->set(self::NON_USED_IMAGES, $list);

        return new Response(stripslashes(json_encode($session->get(self::NON_USED_IMAGES))));
    }

    public static function checkNonUsedImages($token, Request $request)
    {
        self::$session = $request->getSession();
        $list          = self::getFullList();
        if (!$list || !isset($list[$token])) {
            return;
        }

        foreach ($list[$token] as $key => $non_used_image) {
            if ($realpath = realpath(self::POST_INSERTS_DIRECTORY . '/' . $non_used_image)) {
                unlink($realpath);
                unset($list[$token][$key]);
            }
        }
        if (count($list[$token]) == 0) {
            self::$session->set(self::NON_USED_IMAGES, null);
        }
        return;
    }

    private static function getFullList()
    {
        return self::$session->get(self::NON_USED_IMAGES);
    }
} 