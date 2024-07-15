<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Game;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Image;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom du jeu',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 3, 'max' => 255]),
                ],
            ])
            ->add('releaseDate', DateType::class, [
                'label' => 'Date de sortie',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('createdAt', DateType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('pictures', FileType::class, [
                'label' => 'Picture',
                'multiple' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxWidth' => '1080',
                        'maxWidthMessage' => 'L\'image doit faire {{ max_width }} pixels de large au maximum'
                    ])
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'EUR',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'numeric']),
                    new Assert\Positive(),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 5],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 10, 'max' => 255]),
                ],
            ])
            ->add('editor', null, [
                'label' => 'Éditeur',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 3, 'max' => 255]),
                ],
            ])
            ->add('hasCategory', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégories',
                'multiple' => true,
                'expanded' => true,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('hasTag', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'label' => 'Tags',
                'multiple' => true,
                'expanded' => true,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
