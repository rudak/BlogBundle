BlogBundle
==========

Un bundle qui permet de balancer un blog simple sur votre site très rapidement...

###Publication twitter

Pour que le compte twitter soit mis a jour a chaque publication d'articles il faut rentrer
ces quelques informations dans le fichier app/config.yml

        rudak_blog:
            twitter_publication: true   #false par defaut
            consumer_key:               #null par defaut
            consumer_secret:            #null par defaut
            access_token:               #null par defaut
            access_token_secret:        #null par defaut