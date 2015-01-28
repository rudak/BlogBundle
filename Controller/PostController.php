<?php

namespace Rudak\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Rudak\BlogBundle\Entity\Post;
use Rudak\BlogBundle\Form\PostType;
use Symfony\Component\HttpFoundation\Response;
use Rudak\TwitterOauthBundle\Model\TwitterHandler;

/**
 * Post controller.
 *
 */
class PostController extends Controller
{

    const OWN_TOKEN_NAME = 'own_token';

    /**
     * Lists all Post entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('RudakBlogBundle:Post')->getAdminIndexList();

        return $this->render('RudakBlogBundle:Post:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new Post entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Post();
        $form   = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatorName($this->getUser()->getUsername());
            $em->persist($entity);
            $em->flush();

            if ($entity->getPublic()) {

                $patternTwitter = "Nouvel article sur notre site ! %s";
                $article_url    = $this->generateUrl('rudak_blog_post', array(
                    'id'   => $entity->getId(),
                    'slug' => $entity->getSlug()
                ), true);
                $this->twitterAction(sprintf($patternTwitter, $article_url));
            }

            $this->checkNonUsedImages($request);

            $request->getSession()->getFlashBag()->add(
                'success',
                'Article créé avec succès'
            );

            $this->logging($this->getUser()->getUsername(), sprintf('Création d\'un article [#%d]', $entity->getId()), 'Blog');

            return $this->redirect($this->generateUrl('admin_blog_post_show', array('id' => $entity->getId())));
        }

        return $this->render('RudakBlogBundle:Post:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    public function twitterAction($txt)
    {
        if (false === $this->container->getParameter('consumer_key')) {
            return false;
        }

        $consumer_key        = $this->container->getParameter('consumer_key');
        $consumer_secret     = $this->container->getParameter('consumer_secret');
        $access_token        = $this->container->getParameter('access_token');
        $access_token_secret = $this->container->getParameter('access_token_secret');

        $option = array(
            'consumer_key'        => $consumer_key,
            'consumer_secret'     => $consumer_secret,
            'access_token'        => $access_token,
            'access_token_secret' => $access_token_secret
        );

        $th = new TwitterHandler($option);
        return $th->postStatus($txt);
    }


    /**
     * Creates a form to create a Post entity.
     *
     * @param Post $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Post $entity)
    {
        $form = $this->createForm(new PostType(), $entity, array(
            'action' => $this->generateUrl('admin_blog_post_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Creer cet article',
            'attr'  => array(
                'class' => 'btn btn-success'
            )
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Post entity.
     *
     */
    public function newAction(Request $request)
    {
        $entity = new Post();

        $form = $this->createCreateForm($entity);

        return $this->render('RudakBlogBundle:Post:new.html.twig', array(
            'entity'    => $entity,
            'form'      => $form->createView(),
            'own_token' => $this->createOwnToken($request),
        ));
    }

    /**
     * Finds and displays a Post entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RudakBlogBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('RudakBlogBundle:Post:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RudakBlogBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }


        $editForm   = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('RudakBlogBundle:Post:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'own_token'   => $this->createOwnToken($request),
        ));
    }

    /**
     * Creates a form to edit a Post entity.
     *
     * @param Post $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Post $entity)
    {
        $form = $this->createForm(new PostType(), $entity, array(
            'action' => $this->generateUrl('admin_blog_post_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Mettre à jour',
            'attr'  => array(
                'class' => 'btn btn-success btn-lg'
            )
        ));

        return $form;
    }

    /**
     * Edits an existing Post entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RudakBlogBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $this->checkNonUsedImages($request);

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($entity->isLocked()) {
            // si le post est verrouillé
            if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
                // si on est pas super_admin
                return $this->render('RudakBlogBundle:Post:edit.html.twig', array(
                    'entity'      => $entity,
                    'edit_form'   => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
        }

        if ($editForm->isValid()) {
            // vire les images supprimées des articles du disque dur

            $request->getSession()->getFlashBag()->add(
                'success',
                'Article modifié avec succès !'
            );

            $em->flush();

            $this->logging($this->getUser()->getUsername(), sprintf('Modification d\'un article [#%d]', $entity->getId()), 'Blog');

            return $this->redirect($this->generateUrl('admin_blog_post_edit', array('id' => $id)));
        }

        return $this->render('RudakBlogBundle:Post:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Post entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em     = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RudakBlogBundle:Post')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Post entity.');
            }

            if (!$entity->isLocked()) {
                $em->remove($entity);
                $em->flush();

                $this->logging($this->getUser()->getUsername(), sprintf('Suppression d\'un article [#%d]', $entity->getId()), 'Blog');

            }
        }

        return $this->redirect($this->generateUrl('admin_blog_post'));
    }

    /**
     * Creates a form to delete a Post entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_blog_post_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'label' => 'Delete',
                'attr'  => array(
                    'class' => 'btn btn-danger'
                )
            ))
            ->getForm();
    }

    private function createOwnToken($request)
    {
        // token pouur le system de gestion d'images de post
        $own_token = substr(str_shuffle('azertyuiopqsdfghjklmwxcvbn123456789'), 0, 12);
        $request->getSession()->set(self::OWN_TOKEN_NAME, $own_token);
        return $own_token;
    }

    /**
     * Vérifie les images qui n'ont finalement été utilisées
     * mais qui ont été uploadées quand meme (expérimental)
     */
    private function checkNonUsedImages(Request $request)
    {
        $own_token = $request->getSession()->get(self::OWN_TOKEN_NAME);
        UploadController::checkNonUsedImages($own_token, $request);
    }

    private function logging($user, $action, $category)
    {
        try {
            $OwnLogger = $this->get('rudak.own.logger');
            $OwnLogger->addEntry($user, $action, $category, new \DateTime());
        } catch (\Exception $e) {
        }
    }

}
