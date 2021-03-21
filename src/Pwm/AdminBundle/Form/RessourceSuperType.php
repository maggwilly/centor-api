<?php

namespace Pwm\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\ImageType;
class RessourceSuperType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
     
        ->add('nom','text', array(
           'label' => 'Nom du document',
         ))
        ->add('description','textarea', array(
           'label' => 'Une description commerciale du document',
         ))
        
        ->add('detail1','text', array(
           'label' => 'Autre détail 1','required' =>false
         ))
        ->add('detail2','text', array(
           'label' => 'Autre détail 2','required' =>false
         ))
        ->add('label','choice', array(
           'label' => 'Label','required' =>false,
           'choices'=>array(
                    'nouveau'=>'nouveau',
                    'important'=>'important',
                    'urgent'=>'urgent',
                    )
         ))
        ->add('price','integer', array(
          'label' => 'Prix de la ressource',
         ))       
    ->add('fileEntity',   new ImageType(), array('label'=>'Document','required'=>true))
    ->add('matieres', EntityType::class,
                       array('class' => 'AppBundle:Matiere', 
                            'choice_label' => 'getDisplayName',
                            'placeholder' => 'Aucune matière',
                            'empty_data'  => null,
                            'required' => false,
                            'label'=>'Selectionnez les matières',      
                            'multiple'=>true,
                            'expanded'=>false,                  
                            'attr'=>array('data-rel'=>'chosen')))
            ->add('sessions', EntityType::class,array('class' => 'AppBundle:SessionConcours',
                'choice_label' => 'nomConcours',
                'placeholder' => 'Toute les sessions',
                'empty_data'  => null,
                'required' => false,
                'label'=>'Selectionnez les concours',
                'multiple'=>true,
                'expanded'=>false,
                'attr'=>array('data-rel'=>'chosen')))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Pwm\AdminBundle\Entity\Ressource'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pwm_adminbundle_ressource';
    }


}
