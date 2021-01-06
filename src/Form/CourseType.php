<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\CourseCategory;
use App\Entity\CourseLevel;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom du cours'])
            ->add('smallDescription', TextareaType::class, ['label' => 'Description courte'])
            ->add('fullDescription', TextareaType::class, ['label' => 'Description'])
            ->add('duration', IntegerType::class, ['label' => 'Durée du cours'])
            ->add('price', IntegerType::class, ['label' => 'Prix inscription'])
            ->add('isPublished', ChoiceType::class, [
                'label' => 'Etat de publication',
                'choices' => [
                    'Publié' => true,
                    'Non publié' => false,
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Avatar (jpg,jpeg,png,gif file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpg',
                            'image/png',
                            'image/jpeg',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image document',
                    ])
                ],
            ])
            ->add('schedule', ChoiceType::class, [
                'label' => 'Date de cours',
                'choices' => [
                    'Monday' => 'Monday',
                    'Tuesday' => 'Tuesday',
                    'Wednesday' => 'Wednesday',
                    'Thursday' => 'Thursday',
                    'Friday' => 'Friday',
                    'Saturday' => 'Saturday',
                    'Sunday' => 'Sunday',


                ]
            ])
            ->add('program', FileType::class, [
                'label' => 'Brochure (PDF file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie de cours',
                'class' => CourseCategory::class,
                'choice_label' => function ($category) {
                    return $category->getId() . '. ' . $category->getName();
                }])
            ->add('level', EntityType::class, [
                'label' => 'Level requis',
                'class' => CourseLevel::class,
                'choice_label' => function ($category) {
                    return $category->getId() . '. ' . $category->getName();
                }])
            ->add('teacher', EntityType::class, [
                'label' => 'Chargé de cours',
                'required' => false,
                'placeholder' => '--- Sélectionnez un utilisateur (Laissez pour Aucun) ---',
                'class' => User::class,
                'choice_label' => function ($user) {
                    return $user->getId() . '. ' . $user->getFirstName() . ' ' . $user->getLastName();
                }])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
