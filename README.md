BlogBundle
==========

Un bundle qui permet de balancer un blog simple sur votre site très rapidement...

Status
======

in development alpha 0.0.0.0.0.01 ...

#  Installation

* 1/ composer.json : require : bundle
* 2/ creation du bundle blog
* 3/ étendre le rudakblogbundle
* 4/ faire pointer le userbundle dans Post

## Le rendu Twig
Les vue du back-office sont centralisées vers la vue ```layout.html.twig``` se trouvant dans le dossier views. Cette vue étend simplement une autre vue désignée par ```admin_layout``` qui est une variable globale de Twig contenue dans le fichier de configuration ```config/config.yml```. Plus d'informations ici : http://kadur-arnaud.fr/blog/24/placer-ses-layouts-dans-des-variables-globales-twig-sous-symfony2 . Cela permet de pouvoir modifier la vue a étendre sans modifier les fichiers.

