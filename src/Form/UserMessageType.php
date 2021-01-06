<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\UserMessage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text',TextareaType::class, [
                'label' => 'Message',
            ])
            ->add('user', EntityType::class, [
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
            'data_class' => UserMessage::class,
        ]);
    }
}
