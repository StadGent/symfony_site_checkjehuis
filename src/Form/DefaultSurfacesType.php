<?php

namespace App\Form;

use App\Entity\DefaultSurface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultSurfacesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('livingArea', TextType::class, array('label' => 'Bewoonbaar'))
            ->add('floor', TextType::class, array('label' => 'Grond'))
            ->add('facade', TextType::class, array('label' => 'Gevel'))
            ->add('window', TextType::class, array('label' => 'Ramen'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DefaultSurface::class,
            'csrf_protection' => false,
        ));
    }
}
