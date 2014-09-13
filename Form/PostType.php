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
            ->add('title')
            ->add('slug')
            ->add('content')
            ->add('date', 'datetime', array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label'  => 'Date de création'
            ))
            ->add('isPublic')
            ->add('publishDate', 'datetime',array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label'  => 'Date de publication'
            ))
            ->add('category')
            ->add('tags');
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
