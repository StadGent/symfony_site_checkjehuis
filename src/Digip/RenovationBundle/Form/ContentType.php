<?php

namespace Digip\RenovationBundle\Form;

use Digip\RenovationBundle\Service\HouseService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentType extends AbstractType
{
    protected $allowDeactivation;

    public function __construct($allowDeactivation = true)
    {
        $this->allowDeactivation = $allowDeactivation;
    }

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

        if ($this->allowDeactivation) {
            $builder->add('active', 'checkbox', array('label' => 'Actief', 'required' => false));
        }

        $builder
            ->add('value', 'ckeditor', array(
                'toolbar' => array('basicstyles', 'links', 'paragraph', 'styles'),
                'height' => 400
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Digip\RenovationBundle\Entity\Content',
            'csrf_protection' => false,
        ));
    }
} 