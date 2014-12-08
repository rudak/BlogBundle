<?php
namespace Rudak\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Rudak\BlogBundle\Entity\Picture;

class LoadPostPictures extends AbstractFixture implements OrderedFixtureInterface
{
    const REFERENCE_NAME = 'RudakBlogPost_picture_';

    private $url;
    private $fichier;
    private $largeur;
    private $hauteur;
    private $dir;

    public function __construct()
    {
        $this->dir     = '../../../../../../../../web/uploads/post_images/';
        $this->largeur = 800;
        $this->hauteur = 600;
        $this->url     = 'http://lorempixel.com/' . $this->largeur . '/' . $this->hauteur . '/';
    }

    public function load(ObjectManager $manager)
    {
        $nombre_pictures = LoadPostData::getNombre();
        $pictures        = array();

        echo "IMAGES D'ARTICLES\n";
        echo "Nombre total : {$nombre_pictures} \n";

        for ($i = 0; $i <= $nombre_pictures; $i++) {
            $this->choppe_image();

            $pictures[$i] = new Picture();
            $pictures[$i]->setPath($this->fichier);
            $this->setReference($this->getReferenceName($i), $pictures[$i]);
            $manager->persist($pictures[$i]);
        }
        echo "\n";
        $manager->flush();
    }

    public static function getReferenceName($id)
    {
        return self::REFERENCE_NAME . $id;
    }

    function choppe_image()
    {
        $this->setFichierName();
        $path = $this->getAbsoluteFichierPath();
        $this->createFichier($path);

        echo $this->fichier . ' ';

        $ch = curl_init($this->url);
        $fp = fopen($this->getAbsoluteFichierPath(), 'wb');

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }

    private function createFichier($path)
    {
        return fopen($path, 'w+');
    }

    private function getAbsoluteFichierPath($real = false)
    {
        if ($real) {
            return realpath(__DIR__ . $this->dir . $this->fichier);
        } else {
            return __DIR__ . $this->dir . $this->fichier;
        }
    }

    private function setFichierName()
    {
        $this->fichier = 'a_' . substr(str_shuffle('azertyuiopqsdfghjklmwxcvbn0123456789'), 0, 5) . '.jpg';
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 11;
    }
} 