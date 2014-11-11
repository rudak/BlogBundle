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
    public function indexAction($page = 0)
    {
        $posts = $this->getRepo()->getPostList(25);

        if (!$posts) {
            throw $this->createNotFoundException('Impossible de trouver les posts');
        }
        return $this->render('RudakBlogBundle:Default:index.html.twig', array(
            'posts' => $posts
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

            $cookie   = new Cookie('hit_' . $post->getId(), true, new \DateTime('+ 10 minute'));
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

    public function getLastPostsAction($nb = 5)
    {
        $posts = $this->getRepo()->getLastPosts($nb);

        $html = $this->renderView('RudakBlogBundle:Inc:last-post.html.twig', array(
            'posts' => $posts
        ));

        return new Response($html);
    }

    public function hitPost(Post $Post, $request)
    {

    }
}
