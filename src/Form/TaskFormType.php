<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TaskFormType extends AbstractType
{
    /**
     * The buildForm() function is used to create the form. 
     * 
     * The first argument is the FormBuilderInterface object. The second argument is an array of
     * options. 
     * 
     * The FormBuilderInterface object is used to add the fields to the form. 
     * 
     * The add() function is used to add a field to the form. 
     * 
     * The first argument is the name of the field. The second argument is the type of the field. The
     * third argument is an array of options. 
     * 
     * The TextType class is used to create a text field. The TextareaType class is used to create a
     * textarea field. 
     * 
     * The label option is used to set the label of the field.
     * 
     * @param FormBuilderInterface builder The FormBuilderInterface instance
     * @param array options An array of options (name => value) for the field type.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => "Titre"])
            ->add('content', TextareaType::class, ['label' => "Contenu"]);
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
