<?php

namespace App\Form;

use App\Entity\House;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MailPlanType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => House::class,
            'csrf_protection' => true,
        ));
    }
}
