<?php

namespace Rudak\BlogBundle\Controller;

use Rudak\BlogBundle\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class DefaultController extends Controller
{

    /*
     * Renvoie la page $page de la liste des posts
     */
    public function indexAction($page)
    {
        $NB_PAR_PAGE    = 5;
        $NB_TOTAL_POSTS = $this->getRepo()->getNbTotalPosts();
        $posts          = $this->getRepo()->getPostsByPage($page, $NB_PAR_PAGE);

        if (!$posts) {
            throw $this->createNotFoundException('Impossible de trouver les posts');
        }
        return $this->render('RudakBlogBundle:Default:index.html.twig', array(
            'posts'      => $posts,
            'pagination' => $this->pagination($NB_TOTAL_POSTS, $NB_PAR_PAGE, $page, $this->getLinkPattern()),
        ));
    }

    /*
     * Renvoie un post
     */
    public function showAction($id, Request $request)
    {
        $post = $this->getRepo()->getPostById($id);

        if (!$post) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }
        $hitted = $request->cookies->has('hit_' . $post->getId());

        if ($hitted) {
            return $this->render('RudakBlogBundle:Default:show.html.twig', array(
                'post' => $post
            ));
        } else {
            $post->incrementHit();

            $this->getEm()->persist($post);
            $this->getEm()->flush();

            $cookie   = new Cookie('hit_' . $post->getId(), true, new \DateTime('+2 day'));
            $response = $this->render('RudakBlogBundle:Default:show.html.twig', array(
                'post' => $post
            ));
            $response->headers->setCookie($cookie);

            return $response->send();
        }
    }

    private function getRepo()
    {
        return $this->getEm()->getRepository('RudakBlogBundle:Post');
    }

    private function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    public function getLastPostForSliderAction($nb = 5)
    {
        $posts = $this->getRepo()->getLastPosts($nb);

        $html = $this->renderView('RudakBlogBundle:Inc:indexSlider.html.twig', array(
            'posts' => $posts
        ));

        return new Response($html);
    }

    public function getLastPostsAction($nb = 5)
    {
        $posts = $this->getRepo()->getLastPosts($nb);

        $html = $this->renderView('RudakBlogBundle:Inc:last-post.html.twig', array(
            'posts' => $posts
        ));

        return new Response($html);
    }


    public function getPopularPostsAction($nb = 5)
    {
        $posts = $this->getRepo()->getPopularPosts($nb);

        $html = $this->renderView('RudakBlogBundle:Inc:popular-post.html.twig', array(
            'posts' => $posts
        ));

        return new Response($html);
    }

    public function prevPostAction($id)
    {
        $post = $this->getRepo()->getPrevPost($id);
        if (!$post) {
            throw $this->createNotFoundException('Post impossible a trouver');
        }
        return $this->redirect($this->generateUrl('rudak_blog_post', array(
            'id'   => $post->getId(),
            'slug' => $post->getSlug(),
        )));
    }

    public function nextPostAction($id)
    {
        $post = $this->getRepo()->getNextPost($id);
        if (!$post) {
            throw $this->createNotFoundException('Post impossible a trouver');
        }
        return $this->redirect($this->generateUrl('rudak_blog_post', array(
            'id'   => $post->getId(),
            'slug' => $post->getSlug(),
        )));
    }

    /**
     *
     * pagination(
     *    total amount of item/rows/whatever,
     *    limit of items per page,
     *    current page number,
     *    url
     * );
     *
     */
    private function pagination($nb_total, $nb_par_page, $page_en_cours, $pattern_lien)
    {

        $pages          = '';
        $total_de_pages = ceil($nb_total / $nb_par_page);
        $page_min       = ($page_en_cours - 2 < 1 ? 1 : $page_en_cours - 2);
        $page_max       = ($page_en_cours + 2 > $total_de_pages ? $total_de_pages : $page_en_cours + 2);
        $class          = 'other';

        // Premiere et derniere page
        $premiere_page = $page_en_cours > 3 ? $this->url($class, '<<', 1, $pattern_lien, 'Première page') : null;
        $derniere_page = $page_en_cours < $total_de_pages - 2 ? $this->url($class, '>>', $total_de_pages, $pattern_lien, 'Dernière page') : null;

        // precedente et suivante
        $page_precedente = $page_en_cours > 1 ? $this->url($class, '<', ($page_en_cours - 1), $pattern_lien, 'Page précédente') : null;
        $page_suivante   = $page_en_cours < $total_de_pages ? $this->url($class, '>', ($page_en_cours + 1), $pattern_lien, 'Page suivante') : null;

        // pages qui sont dans la tranche
        for ($x = $page_min; $x <= $page_max; ++$x) {
            $pages .= $this->url(($x == $page_en_cours ? 'active' : 'other'), $x, $x, $pattern_lien, 'Page ' . $x);
        }

        if ($total_de_pages > 1) {
            return $premiere_page . $page_precedente . $pages . $page_suivante . $derniere_page;
        }
    }

    /**
     * renvoie une url pour la pagination
     *
     * @param type $class classe du lien a renvoyer
     * @param type $texte texte du lien
     * @param type $var variable a passer au generateur d'url
     * @param type $pattern_lien pattern html du lien
     * @return string lien tout propre
     */
    private function url($class, $texte, $var, $pattern_lien, $title)
    {
        $url = $this->generateUrl('rudak_blog_postlist', array('page' => $var));
        return sprintf($pattern_lien, $class, $url, $title, $texte);
    }

    private function getLinkPattern()
    {
        return "<li class='%s'><a href='%s' title='%s'>%s</a></li>";
    }
}
