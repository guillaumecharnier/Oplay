<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Add this line
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom de la catégorie',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 3, 'max' => 25]),
                ],
            ])
            ->add('pictures', FileType::class, [
                'label' => 'Picture',
                'multiple' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxWidth' => 1980,
                        'maxWidthMessage' => 'L\'image doit faire {{ max_width }} pixels de large au maximum'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
