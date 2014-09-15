<?php

namespace Rudak\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*
                ->add('date', 'datetime', array(
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                ))
            */
            ->add('content')
            ->add('isSignaled', 'checkbox', array(
                'label'    => 'Signalé',
                'required' => false
            ))
            ->add('post')
            ->add('creator');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rudak\BlogBundle\Entity\Comment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rudak_blogbundle_comment';
    }
}
