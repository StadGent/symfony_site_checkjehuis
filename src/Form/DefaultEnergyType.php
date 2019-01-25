<?php

namespace App\Form;

use App\Entity\DefaultEnergy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultEnergyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('gas', TextType::class, array('label' => 'Gas'))
            ->add('electricity', TextType::class, array('label' => 'Elektriciteit'))
            ->add('electricHeating', TextType::class, array('label' => 'Electrisch verwarmen'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DefaultEnergy::class,
            'csrf_protection' => false,
        ));
    }
}
