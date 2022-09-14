<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserFormType extends AbstractType
{
    /**
     * The `buildForm()` function is used to create the form. It takes two parameters: the
     * `FormBuilderInterface` and an array of options
     * 
     * @param FormBuilderInterface builder The form builder object.
     * @param array options An array of options for the form type.
     * 
     * @return void The form is being returned.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'error_bubbling' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un pseudo.'
                    ]),
                    new Length([
                        'min'        => 3,
                        'minMessage' => 'Le pseudo doit contenir au minimum 3 caractères.',
                        'max'        => 50,
                        'maxMessage' => "Le pseudo ne peut pas excéder 50 caractères."
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'error_bubbling' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une adresse email.'
                    ]),
                    new Length([
                        'min'        => 5,
                        'minMessage' => "L'adresse email est trop courte.",
                        'max'        => 255,
                        'maxMessage' => "L'adresse email ne peut pas excéder 255 caractères."
                    ]),
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'Utilisateur'    => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN'
                ],
            ]);

        // Data transformer
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
    }

    /**
     * The configureOptions() method is used to set the default data class for the form.
     * 
     * @param OptionsResolver resolver The OptionsResolver object.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
