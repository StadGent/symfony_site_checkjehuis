<?php

namespace Digip\RenovationBundle\Form;

use Digip\RenovationBundle\Service\HouseService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DefaultSurfacesType extends AbstractType
{
    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'config_default_surfaces';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('livingArea', 'text', array('label' => 'Bewoonbaar'))
            ->add('floor', 'text', array('label' => 'Grond'))
            ->add('facade', 'text', array('label' => 'Gevel'))
            ->add('window', 'text', array('label' => 'Ramen'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Digip\RenovationBundle\Entity\DefaultSurface',
            'csrf_protection' => false,
        ));
    }
} 