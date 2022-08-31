<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => "Titre"])
            ->add('content', TextareaType::class, ['label' => "Contenu"])
            ->add('isDone', CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom'],
                'required'   => false
                ])
        ;
    }
}
