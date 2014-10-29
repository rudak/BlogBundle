<?php

namespace Rudak\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Rudak\BlogBundle\Utils\Uploader;

class UploadController extends Controller
{
    public function uploadPictureAction()
    {

        $upload = new Uploader(array(
            'index'          => 'file',
            'new_width'      => 1100,
            'new_height'     => 800,
            'resize_quality' => 75
        ));

        $upload->setNewName();
        $upload->setDestination('uploads/test/');
        $upload->addAllowedMimeType(array('image/jpeg', 'image/gif', 'image/png'));
        $upload->useResizer();
        $upload->process();

        return new Response(var_dump($upload));

        //return new Response($upload->getUploadResult());
        //return new Response($this->getHtml());
    }

    private function getHtml()
    {
        return <<<EOF
<form method="post" enctype="multipart/form-data" action="http://localhost/rcm/admin/blog/post/upload-post-picture">
<p>
<input type="file" name="photo">
</p>
<p>
<input type="submit" value="Envoyer ce fichier">
</p>
</form>
EOF;

    }
} 