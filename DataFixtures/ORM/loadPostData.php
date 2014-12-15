<?php
namespace Rudak\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Rudak\BlogBundle\Entity\Post;

use Rudak\UtilsBundle\FakeContentGenerator;
use Rudak\UtilsBundle\Namer;


class loadPostData extends AbstractFixture implements OrderedFixtureInterface
{

    const NOMBRE_ARTICLES = 18;

    public function load(ObjectManager $manager)
    {
        $posts = array();

        echo "CREATION DES ARTICLES : \n";

        for ($i = 0; $i <= self::NOMBRE_ARTICLES; $i++) {
            $posts[$i] = new Post();
            $posts[$i]->setTitle(FakeContentGenerator::getSmallTitle());
            $posts[$i]->setHat(FakeContentGenerator::getTitle());
            $posts[$i]->setContent(FakeContentGenerator::getParagraph(5));
            $posts[$i]->setDate(new \DateTime('-' . (rand(10, 40000)) . 'hour'));
            $posts[$i]->setHit(rand(0, 480));
            $posts[$i]->setPublic(rand(0, 1));
            $posts[$i]->setLocked(false);
            $posts[$i]->setCreatorName(Namer::getFirstName() . ' ' . Namer::getLastName());
            $posts[$i]->setPicture($this->getReference($this->getPictureReference($i)));
            $manager->persist($posts[$i]);
            echo ' - [' . $i . '/' . self::NOMBRE_ARTICLES . '] ' . $posts[$i]->getTitle() . "\n";
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
        return 714;
    }
} 