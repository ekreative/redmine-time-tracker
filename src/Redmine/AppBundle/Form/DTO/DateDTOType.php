<?php

namespace Redmine\AppBundle\Form\DTO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DateDTOType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add("filterDate", "date", [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control date-picker-tt'],
                'format' => 'dd.MM.yyyy',
                'label' => 'Звіт за'
            ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "date_form";
    }
}
