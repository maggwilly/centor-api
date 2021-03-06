<?php

namespace Pwm\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom')
        ->add('starter')
        ->add('standard')
        ->add('premium')
        ->add('starterDesc')
        ->add('standardDesc')
        ->add('premiumDesc')
        ->add('starterDelay')
        ->add('standardDelay')
        ->add('premiumDelay');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Pwm\AdminBundle\Entity\Tarifaire'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pwm_adminbundle_price';
    }


}
