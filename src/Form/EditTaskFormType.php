<?php

namespace App\Form;

use App\Form\TaskFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EditTaskFormType extends TaskFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('isDone', CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom'],
                'required'   => false
            ]);
    }
}
