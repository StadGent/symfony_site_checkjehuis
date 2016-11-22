<?php

namespace Digip\RenovationBundle\Form;

use Digip\RenovationBundle\Service\HouseService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HouseEmailType extends AbstractType
{
    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'house_email';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('email', 'email', array(
                'required' => true
            ));
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Digip\RenovationBundle\Entity\House',
        ));
    }
} 