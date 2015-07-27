<?php

namespace Redmine\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pushToken', 'text', [
                'constraints' => [
                    new NotBlank(['message' => 'Push token is not blank'])
                ]
            ])
            ->add('deviceId', 'text', [
                new NotBlank(['message' => 'device Id is not blank'])
            ])
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "device";
    }
}
