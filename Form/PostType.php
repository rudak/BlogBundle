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
            ->add('category', 'entity', array(
                'class'    => 'RudakBlogBundle:Category',
                'property' => 'name',
                'required' => true,
                'label'    => 'Catégorie'
            ))
            ->add('title', 'text', array(
                'label' => 'Titre'
            ))
            //->add('slug')
            ->add('content', 'textarea', array(
                'label' => 'Contenu'
            ))

            ->add('isPublic', 'checkbox', array(
                'required' => false,
                'label'    => 'Publication'
            ))
            ->add('publishDate', 'datetime', array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label'  => 'Date de publication'
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
