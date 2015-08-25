<?php

namespace Redmine\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('sms', 'checkbox', [
                'attr' => ['class' => 'form-chkb'],
                'required' => false
            ])
            ->add('push', 'checkbox', [
                'attr' => ['class' => 'form-chkb'],
                'required' => false
            ])
            ->add('checkFirst', 'time', [
                'widget' => 'single_text',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('checkSecond', 'time', [
                'widget' => 'single_text',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('checkThird', 'time', [
                'widget' => 'single_text',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('phone', 'text', [
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => [
                    'class' => 'form-control bfh-phone',
                    'placeholder' => "(050) 123-45-67"
                ],
                'required' => false
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Redmine\AppBundle\Entity\Settings'
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "settings";
    }
}
