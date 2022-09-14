<?php

namespace App\Form;

use App\Entity\Task;
use App\Form\TaskFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EditTaskFormType extends TaskFormType
{
    /**
     * We're adding a new field to the form called 'isDone' and it's a checkbox. The label for the
     * checkbox is going to be a custom switch, and it's not required.
     * 
     * @param FormBuilderInterface builder The form builder object
     * @param array options An array of options for the field type.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('isDone', CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom'],
                'required'   => false
            ]);
    }

    /**
     * The configureOptions() method is used to set the default data class for the form.
     * 
     * @param OptionsResolver resolver The OptionsResolver object.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
