<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\EmailIsNotRegistered;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
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
            ->add('password', RepeatedType::class, [
                'error_bubbling' => true,
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Password',
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir un mot de passe.',
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                            'message' => 'Votre mot de passe doit contenir au moins 8 caractères dont 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial.'
                        ]),
                    ],
                ],
                'second_options' => ['label' => 'Repeat Password'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
