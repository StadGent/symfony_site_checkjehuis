<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentType extends AbstractType
{

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'content';
    }

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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Content',
            'csrf_protection' => false,
            'allow_deactivation' => true,
        ));
    }
}
