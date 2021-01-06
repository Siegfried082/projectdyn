<?php

namespace App\Form;

use App\Entity\Bans;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BansType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('reason', TextAreaType::class, ['label' => 'Raison du bannissement'])
            ->add('users', EntityType::class, [
                'label' => 'Nom Utilisateur',
                'placeholder' => '--- Sélectionnez un utilisateur ---',
                'class' => User::class,
                'choice_label' => function ($user) {
                    return $user->getId() . '. ' . $user->getFirstName() . ' ' . $user->getLastName();
                }])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bans::class,
        ]);
    }
}
