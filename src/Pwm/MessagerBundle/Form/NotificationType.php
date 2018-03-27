<?php

namespace Pwm\MessagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\ImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class NotificationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('titre')
        ->add('sousTitre', 'textarea' ,array('label'=>'Corps du message en texte simple'))
        ->add('text', 'textarea' ,array('label'=>'Corps du message en texte riche','attr'=>array('class'=>'cleditor')))
       // ->add('tag')
        ->add('format', ChoiceType::class, array(
                                 'choices'  => array(
                                 'ios-mail' => 'Message', 'notifications' => 'Notifications', 'paper' => 'Annonce', 'alarm' => 'Alerte temps','ios-bulb' => 'Astuce'),
                                   ))
        ->add('groupe', EntityType::class,
             array('class' => 'AdminBundle:Groupe', 
                   'choice_label' => 'getNom', 
                   'placeholder' => 'Tout le monde',
                   'empty_data'  => null,
                    'required' => false ,                  
                    'label'=>'Groupe')
             );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Pwm\MessagerBundle\Entity\Notification'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pwm_messagerbundle_notification';
    }


}
