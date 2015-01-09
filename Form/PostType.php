<?php

namespace Rudak\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Titre de l\'article',
                'attr'  => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Titre tres court'
                )
            ))
            ->add('hat', 'text', array(
                'label' => 'Chapeau de l\'article',
                'attr'  => array(
                    'class' => 'form-control',
                    'placeholder' => 'Phrase de description d\'une ligne ou deux'
                )
            ))
            ->add('content', 'textarea', array(
                'label'    => false,
                'attr'     => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Ajouter votre contenu...'
                ),
                'required' => false
            ))
            ->add('picture', new PictureType(), array(
                'label'    => false,
                'required' => false
            ))
            ->add('public', 'checkbox', array(
                'required' => false,
                'label'    => 'Article public'
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rudak\BlogBundle\Entity\Post'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rudak_blogbundle_post';
    }
}
