<?php
/**
 * Created by PhpStorm.
 * User: Rudak
 * Date: 11/11/2014
 * Time: 09:09
 */

namespace Rudak\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Rudak\BlogBundle\Entity\Picture;
use Rudak\BlogBundle\Entity\Post;
use Rudak\BlogBundle\Utils\BaconIpsum;
use Rudak\BlogBundle\Utils\Syllabeur;


class loadPostData implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $BaconIpsum = new BaconIpsum();
        $posts      = array();
        for ($i = 0; $i <= 45; $i++) {
            $posts[$i] = new Post();
            $posts[$i]->setTitle(Syllabeur::getMots(rand(2, 5)));
            $posts[$i]->setHat(Syllabeur::getMots(rand(15, 35)));
            $posts[$i]->setContent($BaconIpsum->get_content());
            $posts[$i]->setDate(new \DateTime('-' . (rand(10, 400)) . 'day'));
            $posts[$i]->setHit(rand(0, 480));
            $posts[$i]->setPublic(rand(0, 1));
            $posts[$i]->setLocked(false);
            $posts[$i]->setCreatorName('fixtureMan');
            $manager->persist($posts[$i]);
            echo '.';
        }
        echo "\n";
        $manager->flush();
    }
} 