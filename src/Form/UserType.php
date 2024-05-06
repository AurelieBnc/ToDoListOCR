<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('username', TextType::class, [
            'required' => true,
            'label' => 'Nom d\'utilisateur', 
            'label_attr' =>['class' => 'fw-bold'],
            'attr' => [
                'placeholder' => 'Comment va-t-on t\'appeler?',
            ],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Merci de renseigner votre pseudo.',]) 
            ],
        ])
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'Choisir un rôle' => [
                    'Visiteur' => null,
                    'Utilisateur' => User::ROLE_USER,
                    'Administrateur' => User::ROLE_ADMIN,
                ]
            ],
            'multiple' => false,
            'required' => true,
            'label' => 'Rôle de l\'utilisateur',
            'label_attr' => ['class' => 'fw-bold pe-3']
        ])
        ->add('email', EmailType::class, [
            'label' => 'Adresse email',
            'label_attr' =>['class' => 'fw-bold'],
            'attr' => [
                'placeholder' => 'Indiques ton adresse mail',
            ],
            'required' => true,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Merci de renseigner ton Email.',
                    ]) 
            ],
        ])
        ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options' => [
                // 'constraints' => [
                //     new Assert\Type('string'),
                //     new Assert\Length([
                //         'min' => 12,
                //         'minMessage' => 'Le mot de passe doit contenir au moins 12 caractères.',
                //         // max length allowed by Symfony for security reasons
                //         'max' => 4096,
                //     ]),
                //     // Password must contain at least a upper and lower case
                //     new Assert\Regex([
                //         'pattern' => '/(?=.*[a-z])(?=.*[A-Z])/',
                //         'message' => 'Le mot de passe doit contenir au moins une majuscule et une minuscule.',
                //         'match' => true,
                //     ]),
                //     // Password must contain at least one digit
                //     new Assert\Regex([
                //         'pattern' => '/\d+/i',
                //         'message' => 'Le mot de passe doit contenir au moins un chiffre.',
                //         'match' => true,
                //     ]),
                //     // Password must contain at least one special char from the list (including space)
                //     new Assert\Regex([
                //         'pattern' => '/[^a-zA-Z0-9\n\r]+/i',
                //         'message' => 'Le mot de passe doit contenir au moins un caractère spécial.',
                //         'match' => true,
                //     ]),
                // ],
                'label' => 'Mot de passe',
                'label_attr' =>['class' => 'fw-bold '],
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'font-weight-light',
                    'placeholder' => 'Mot de passe sécurisé',

                ],
            ],
            'second_options' => [
                'label' => 'Tapez le mot de passe à nouveau',
                'label_attr' =>['class' => 'fw-bold'],
                'attr' => [
                    'class' => 'font-weight-light',
                    'placeholder' => 'Mot de passe identique',

                ],
            ],
            'invalid_message' => 'Les deux mots de passe doivent correspondre.',
            'mapped' => false,
        ])
        ;

        // For the user role
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagsAsArray) {
                    // transform the array to a string
                    return $tagsAsArray;
                },
                function ($tagsAsString) {
                    // transform the string back to an array
                    return [$tagsAsString];
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}