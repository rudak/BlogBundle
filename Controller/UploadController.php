<?php

namespace Rudak\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Rudak\UtilsBundle\Uploader\Uploader;

class UploadController extends Controller
{
    const TO_DELETE_LIST = 'post_inserts_to_delete';
    const POST_INSERTS_DIRECTORY = 'uploads/post_inserts';
    private $session;
    private $token;
    private $filename;
    private $list;

    public function uploadPictureAction()
    {
        $config = array(
            Uploader::DIR               => self::POST_INSERTS_DIRECTORY,
            Uploader::FILE_INDEX        => 'file',
            Uploader::UPLOAD_MAX_SIZE   => '6Mo',
            Uploader::UPLOAD_MIN_SIZE   => '50ko',
            Uploader::UPLOAD_MIN_WIDTH  => 350,
            Uploader::UPLOAD_MIN_HEIGHT => 400,
            Uploader::RESIZE_NEW_WIDTH  => 800,
            Uploader::RESIZE_NEW_HEIGHT => 800,
            Uploader::RESIZE_QUALITY    => 75,
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
                                    $response['id']       = 'img_' . rand(120, 999999);
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

    public function addPictureToDeleteAction(Request $request)
    {
        $this->session  = $request->getSession();
        $this->token    = $request->get('token');
        $webpath        = $request->get('webpath');
        $this->filename = $this->getFileNameFromWebPath($webpath);
        $this->list     = $this->getPictureList();

        $this->addFilenameToList();

        $t = array(
            'token'    => $this->token,
            'filename' => $this->filename,
            'list'     => $this->list
        );

        return new Response(stripslashes(json_encode($t)));
    }

    public function checkIfTokenExist($token, Request $request)
    {
        $this->token = $token;
        $this->list  = $request->getSession()->get(self::TO_DELETE_LIST);
        if (!$this->list) {
            return false;
        }
        return array_key_exists($token, $this->list);
    }

    public function removeImagesFromThisToken(Request $request)
    {
        $list   = $this->getListToken();
        $error  = array();
        $result = array();
        foreach ($list as $key => $image) {
            $path = realpath(self::POST_INSERTS_DIRECTORY . '/' . $image);
            if ($path) {
                if (!unlink($path)) {
                    $error[] = $image . ' Probleme de suppression';
                } else {
                    unset($list[$key]);
                    $result[] = $image . ' Supprimee';
                }
            } else {
                $error[] = $image . ' n\'existe pas';
            }
        }

        if (!count($error)) {
            $request->getSession()->set(self::TO_DELETE_LIST, null);
        } else {
            $request->getSession()->set(self::TO_DELETE_LIST, array(
                $this->token => $list
            ));
        }
        return array(
            'error'        => $error,
            'result'       => $result,
            'liste finale' => $list
        );
    }


    private function getListToken()
    {
        return $this->list[$this->token];
    }

    private function addFilenameToList()
    {
        $this->list[$this->token][] = $this->filename;
        $this->updateList();
    }

    private function updateList()
    {
        $this->session->set(self::TO_DELETE_LIST, $this->list);
    }

    private function getPictureList()
    {
        $list = $this->session->get(self::TO_DELETE_LIST);
        if ($list === null) {
            $list = array(
                $this->token => array()
            );
            $this->session->set(self::TO_DELETE_LIST, $list);
        }
        return $list;
    }

    private function getFileNameFromWebPath($webpath)
    {
        return basename($webpath);
    }

    private function get_asset_url($path)
    {
        return $this->container->get('templating.helper.assets')->getUrl($path, null);
    }

} 