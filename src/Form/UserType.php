<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\Constraints as CraftAssert;
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
            ->add('username', TextType::class, 
                [
                    'required' => true,
                    'label' => 'Nom d\'utilisateur',
                    'label_attr' => ['class' => 'fw-bold'],
                    'constraints' => [new Assert\NotBlank],
                ]
            )
            ->add('roles', ChoiceType::class,
                [
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
                ]
            )
            ->add('email', EmailType::class,
                [
                    'label' => 'Adresse email',
                    'label_attr' => ['class' => 'fw-bold'],
                    'required' => true,
                    'constraints' => [new Assert\NotBlank],
                ]
            )
            ->add('plainPassword', RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'constraints' => [new CraftAssert\PasswordRequirements([])],
                        'label' => 'Mot de passe',
                        'label_attr' => ['class' => 'fw-bold '],
                        'attr' => [
                            'autocomplete' => 'new-password',
                            'class' => 'font-weight-light',
                            'placeholder' => 'Mot de passe sécurisé'
                            ],
                    ],
                    'second_options' => [
                        'label' => 'Tapez le mot de passe à nouveau',
                        'label_attr' => ['class' => 'fw-bold'],
                        'attr' => [
                            'class' => 'font-weight-light',
                            'placeholder' => 'Mot de passe identique'
                            ],
                    ],
                    'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                    'mapped' => false,
                ]
            );

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagsAsArray) {
                    return $tagsAsArray;

                },
                function ($tagsAsString) {
                    return [$tagsAsString];

                }
            )
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );

    }
}
