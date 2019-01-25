<?php

namespace App\Form;

use App\Entity\Config;
use App\Entity\ConfigTransformation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigTransformationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('value', TextType::class, array('label' => 'Waarde'))
            ->add('unit', ChoiceType::class, array('label' => 'Eenheid', 'choices' => Config::getUnits()))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ConfigTransformation::class,
            'csrf_protection' => false,
        ));
    }
}
