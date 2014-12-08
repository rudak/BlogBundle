<?php
/**
 * Created by PhpStorm.
 * User: Rudak
 * Date: 06/12/2014
 * Time: 11:06
 */

namespace Rudak\BlogBundle\Utils;


class Namer
{

    public static function getFirstName()
    {
        $firstNames = self::getAvailableFirstNames();
        return strtolower($firstNames[array_rand($firstNames)]);
    }

    public static function getLastName()
    {
        $lastNames = self::getAvailableLastNames();
        return strtolower($lastNames[array_rand($lastNames)]);
    }

    private static function getAvailableFirstNames()
    {
        return array(
            'fréderic', 'cédric', 'paul', 'frank', 'bruno', 'arnaud', 'richard', 'romain', 'samuel',
            'Marc', 'Cyril', 'Luc', 'Lioris', 'Roman', 'Léandre',
            'Jonas', 'Alan', 'Max', 'Albin', 'Dimitri', 'Ronan', 'Gianni',
            'Andy', 'Guilhem', 'Sébastien', 'Roméo', 'Fabio', 'Andreas',
            'Luka', 'Bruno', 'Maximilien', 'Jordan', 'Matthias', 'Sofiane',
            'Donovan', 'Ludovic', 'Ange', 'Calvin', 'Nolann', 'Elio', 'Emmanuel',
            'Sam', 'Alex', 'Maxens', 'Noan', 'Ulysse', 'Stanislas', 'Bryan',
            'Emilien', 'Oscar', 'Arsene', 'Matisse', 'Cyprien', 'Elouan', 'Yoann',
            'Eloi', 'Lorenzo', 'Flavio', 'FranÇois', 'Jonathan', 'Elliot', 'Matthéo',
            'Joris', 'Xavier', 'Johan', 'Kyllian', 'Edouard', 'Gauthier', 'Emeric',
            'Pablo', 'Tanguy', 'Ilian', 'Tony', 'Ewan', 'Justin', 'Joan', 'Malo',
            'Jean', 'Melvin', 'Erwann', 'Anatole', 'Augustin', 'Eliot', 'Florent',
            'Flavien', 'Téo', 'Vincent', 'Gaspard', 'Adam', 'David', 'Alban', 'LoÏs',
            'Thibaut', 'Olivier', 'Yann', 'Charles', 'Amaury', 'Noam', 'Sasha', 'Jean',
            'Owen', 'Charly', 'Loan', 'Matéo', 'Léandre', 'Luca', 'Lilian', 'Matthieu',
            'Julian', 'Nino', 'Mathieu', 'Gaëtan', 'Ewen', 'Mae', 'Loris', 'Alban',
            'Gaspard', 'Charles', 'Oscar', 'Mahé', 'Louka', 'Antoine', 'Enzo', 'Axel',
            'Thomas', 'Nolan', 'Timeo', 'Sacha', 'Maxime', 'Mathis', 'Baptiste', 'Tom',
            'Maël', 'Théo', 'Ethan', 'Paul', 'Nathan', 'Léo', 'Raphaël', 'Arthur',
            'Gabriel', 'Clément', 'Jules', 'Lucas', 'Hugo');
    }

    private static function getAvailableLastNames()
    {
        return array(
            'HUET', 'LANGLOIS', 'LEMAIRE', 'CHARPENTIER', 'PREVOST', 'BARON', 'MEYER',
            'ROYER', 'RIVIERE', 'STEWART', 'DANIEL', 'DESCHAMPS', 'HUBERT', 'LEROUX',
            'MEUNIER', 'MARY', 'MULLER', 'GRAND', 'LACROIX', 'WRIGHT', 'MARIE', 'PHILIPPE',
            'BAILLY', 'MISS', 'LOUIS', 'ROLLAND', 'ALLEN', 'CAMPBELL', 'JOLY', 'DUFOUR',
            'CHARLES', 'LECLERC', 'RENARD', 'GAILLARD', 'DUMONT', 'PELLETIER', 'BERGER',
            'HILL', 'ADAM', 'WALKER', 'GUILLAUME', 'ROGER', 'RENAUD', 'OLIVIER', 'SCOTT',
            'BOUCHER', 'GEORGE', 'THOMPSON', 'PICARD', 'DUMAS', 'BRETON', 'BOURGEOIS',
            'GAUTHIER', 'ROCHE', 'LUCAS', 'COLIN', 'PARIS', 'JEAN', 'JAMES', 'LEFEVRE',
            'MOORE', 'HALL', 'DUVAL', 'BRUNET', 'LEGRAND', 'ADAMS', 'NOEL', 'ROUSSEL',
            'TAYLOR', 'AUBERT', 'PIERRE', 'DENIS', 'BOYER', 'WHITE', 'ARNAUD', 'FONTAINE',
            'FABRE', 'BARBIER', 'BLANCHARD', 'CLARK', 'WILSON', 'GERARD', 'FRANCOIS', 'BRUN',
            'ROBIN', 'NICOLAS', 'CLEMENT', 'MATHIEU', 'GIRAUD', 'WILLIAMS', 'PERRIN', 'JOHN',
            'MASSON', 'LEFEBVRE', 'MORIN', 'LEROY', 'FAURE', 'DUPONT', 'CHEVALIER', 'DAVIS',
            'MARCHAND', 'MILLER', 'MERCIER', 'BONNET', 'GAUTIER', 'VINCENT', 'ANDRE', 'MOREL',
            'GUERIN', 'JONES', 'ROUSSEAU', 'GARNIER', 'JOHNSON', 'FOURNIER', 'BLANC', 'LAMBERT',
            'GIRARD', 'LAURENT', 'BERTRAND', 'ROY', 'DAVID', 'BROWN', 'HENRY', 'SIMON',
            'MOREAU', 'DUBOIS', 'DURAND', 'RICHARD', 'MICHEL', 'ROBERT', 'PETIT', 'ROUX',
            'BERNARD', 'THOMAS', 'SMITH', 'MARTIN');
    }
}
