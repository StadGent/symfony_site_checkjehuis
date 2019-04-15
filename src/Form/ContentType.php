<?php

namespace App\Form;

use App\Entity\Content;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if ($options['allow_deactivation']) {
            $builder->add('active', CheckboxType::class, array('label' => 'Actief', 'required' => false));
        }
        $builder
            ->add('value', TextareaType::class/*, array(
                'toolbar' => array('basicstyles', 'links', 'paragraph', 'styles',)
                'height' => 400
            )*/)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Content::class,
            'csrf_protection' => false,
            'allow_deactivation' => true,
        ));
    }
}
