<?php

namespace Rudak\BlogBundle\Utils;

/**
 * Renvoie des suites de syllables (mots) aléatoires
 *
 * @author Rudak
 */
class Syllabeur
{

    public static function getMots($nb = 2)
    {
        $out = '';
        for ($i = 0; $i < $nb; $i++) {
            $out .= self::getSyllabes(rand(1, 3)) . ' ';
        }
        return trim($out);
    }

    public static function getSyllabes($nb = 2)
    {
        $out = '';
        for ($i = 0; $i < $nb; $i++) {
            $out .= self::getSyllabe();
        }
        return $out;
    }

    private static function getSyllabe()
    {
        return self::getLettre() . self::getSon();
    }

    private static function getSon()
    {
        $son = array(
            'a', 'e', 'é', 'è', 'an', 'am', 'em', 'en', 'in',
            'im', 'un', 'ou', 'ia', 'ié', 'iu', 'ier', 'on',
            'ou', 'oui', 'ai', 'oi', 'eu', 'ar', 'ien', 'ion',
            'ieu', 'ac', 'or', 'eau', 'éon', 'éau'
        );
        return $son[array_rand($son)];
    }

    private static function getLettre()
    {
        $l = array(
            'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n',
            'p', 'q', 'r', 's', 't', 'v', 'w', 'x', 'z', 'gr',
            'ch', 'th', 'fl', 'gn', 'tb', 'tr', 'ts', 'vr', 'st'
        );
        return $l[array_rand($l)];
    }

}
