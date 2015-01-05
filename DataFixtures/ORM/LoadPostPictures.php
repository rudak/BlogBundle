<?php
namespace Rudak\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Rudak\BlogBundle\Entity\Picture;

use Rudak\PictureGrabber\Model\PictureGrabber;

class LoadPostPictures extends AbstractFixture implements OrderedFixtureInterface
{
    const REFERENCE_NAME = 'RudakBlogPost_picture_';
    const PREFIX = 'rcm_actu_';

    public function load(ObjectManager $manager)
    {
        $nombre_pictures = LoadPostData::getNombre();
        $pictures        = array();
        $url = "http://lorempixel.com/%s/%s/";

        echo "IMAGES D'ARTICLES\n";
        echo "Nombre total : {$nombre_pictures} \n";

        for ($i = 0; $i <= $nombre_pictures; $i++) {

            $url = sprintf($url, rand(760, 840), rand(550, 610));
            $pictures[$i] = new Picture();

            $pc    = new PictureGrabber($url, $pictures[$i]->getUploadDir(), self::PREFIX);
            $image = $pc->getImage() ? $pc->getFileName() : $pictures[$i]->getDefaultImagePath();

            $pictures[$i]->setPath($image);
            $this->setReference($this->getReferenceName($i), $pictures[$i]);

            echo ' - [' . $i . '/' . $nombre_pictures . '] ' . $image . "\n";
            $manager->persist($pictures[$i]);
        }
        echo "\n";
        $manager->flush();
        echo "TERMINE\n -------------------- \n";
    }

    public static function getReferenceName($id)
    {
        return self::REFERENCE_NAME . $id;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 711;
    }
} 