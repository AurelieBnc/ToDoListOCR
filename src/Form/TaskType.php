<?php

namespace App\Form;

use App\Entity\Task;
use App\EnumTodo\TaskStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,
                [
                    'required' => true,
                    'label' => 'Titre',
                    'label_attr' => ['class' => 'fw-bold pb-2 mt-3'],
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Merci de renseigner un titre.']
                        )
                    ],
                ]
            )
            ->add('content', TextareaType::class,
                [
                    'label' => 'Détail de la tâche',
                    'required' => false,
                    'attr' => ['rows' => 10],
                ]
            )
            ->add('status', EnumType::class,
                [
                    'class' => TaskStatus::class,
                    'required' => false,
                ]
            );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Task::class]);

    }
}
