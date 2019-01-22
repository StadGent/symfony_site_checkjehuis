<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MailPlanType extends AbstractType
{
    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'mail_plan';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('email', EmailType::class, array(
                'label' => 'Mail mijn persoonlijk stappenplan naar',
            ))
            ->add('address', TextType::class, array(
                'label' => 'Mijn adres',
            ))
            ->add('newsletter', CheckboxType::class, array(
                'data' => false,
                'required' => false,
                'label' => 'Stad Gent ondersteunt energiezuinig renoveren met premies, bouwadvies en renovatiebegeleiding. Ik blijf graag op de hoogte van nieuwe initiatieven.',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\House',
            'csrf_protection' => true,
        ));
    }
}
