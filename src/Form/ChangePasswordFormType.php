<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrez votre mot de passe',
                        ]),
                        new Regex ([
                            'pattern' => "/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[@?!$*]){12,}/",
                            'message' => 'min caractères : 12,au moins 1 caractère spécial,au moins 1 majuscule,au moins 1 minuscule,au moins 1 chiffre.'
                        ])
                    ],
                    'label' => 'Nouveau mot de passe',
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'label' => 'Répéter le mot de passe ',
                ],
                'invalid_message' => 'Les mots de passe ne sont pas identiques.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
