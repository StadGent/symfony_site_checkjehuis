<?php

namespace Digip\RenovationBundle\Form;

use Digip\RenovationBundle\Entity\Config;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConfigTransformationType extends AbstractType
{
    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'config_transformation';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('value', 'text', array('label' => 'Waarde'))
            ->add('unit', 'choice', array('label' => 'Eenheid', 'choices' => Config::getUnits()))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Digip\RenovationBundle\Entity\ConfigTransformation',
            'csrf_protection' => false,
        ));
    }
} 