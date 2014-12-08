<?php
namespace Rudak\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Rudak\BlogBundle\Entity\Post;
use Rudak\BlogBundle\Utils\BaconIpsum;
use Rudak\BlogBundle\Utils\Syllabeur;
use Rudak\BlogBundle\Utils\Namer;


class loadPostData extends AbstractFixture implements OrderedFixtureInterface
{

    const NOMBRE_ARTICLES = 27;

    public function load(ObjectManager $manager)
    {
        $BaconIpsum = new BaconIpsum();
        $posts      = array();
        for ($i = 0; $i <= self::NOMBRE_ARTICLES; $i++) {
            $posts[$i] = new Post();
            $posts[$i]->setTitle(Syllabeur::getMots(rand(2, 5)));
            $posts[$i]->setHat(Syllabeur::getMots(rand(5, 25)));
            $posts[$i]->setContent($BaconIpsum->get_content());
            $posts[$i]->setDate(new \DateTime('-' . (rand(10, 40000)) . 'hour'));
            $posts[$i]->setHit(rand(0, 480));
            $posts[$i]->setPublic(rand(0, 1));
            $posts[$i]->setLocked(false);
            $posts[$i]->setCreatorName(Namer::getFirstName() . ' ' . Namer::getLastName());
            $posts[$i]->setPicture($this->getReference($this->getPictureReference($i)));
            $manager->persist($posts[$i]);
            echo '.';
        }
        echo "\n";
        $manager->flush();
    }

    private function getPictureReference($id)
    {
        return LoadPostPictures::getReferenceName($id);
    }

    public static function getNombre()
    {
        return self::NOMBRE_ARTICLES;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 14;
    }
} 