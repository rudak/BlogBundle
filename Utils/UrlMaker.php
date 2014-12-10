<?php
/**
 * Created by PhpStorm.
 * User: Rudak
 * Date: 10/12/2014
 * Time: 12:36
 */

namespace Rudak\BlogBundle\Utils;


class UrlMaker
{

    public static function getRandUrl()
    {
        return self::getRandProtocol() . '://' .
        self::getRandWww() .
        self::getRandOrganisationName() .
        self::getRandTld();
    }

    private static function getRandWww()
    {
        return rand(0, 1) == 1 ? 'www.' : null;
    }

    private static function getRandOrganisationName()
    {
        $orgs = array('nonabi-nominous', 'bitnik', 'vendetta-red', 'certifiedstorm',
            'xul-running', 'weekofcirkus', 'meninblue', 'lobotomik', 'kuku-friends',
            'july-december', 'howl', 'gnome-empower', 'flight-icarus', 'desert-storm',
            'select-one', 'quality-first', 'pullover', 'operation71', 'ignore-call',
            'union-free', 'yeswecan', 'tristal-chooper', 'roterdam', 'erikson', 'zlut',
            'artec', 'doomed', 'gunz', 'cooler-mistic', 'sticky-rope', 'greenlab',
            'emptyfire', 'font-work', 'blackscreen', '', 'button-group', 'nounours',
            'flowrida', 'xterm45', 'dompriot', 'flash-fuzz', 'generic', 'dorthmound',
            'calicuta', 'stis-ro', 'deura');
        return $orgs[array_rand($orgs)] . '.';
    }

    private static function getRandTld()
    {
        $tlds = array('com', 'fr', 'org', 'de', 'net', 'eu', 'co');
        return $tlds[array_rand($tlds)];
    }

    private static function getRandProtocol()
    {
        $protocols = array('http', 'https');
        return $protocols[array_rand($protocols)];
    }

} 