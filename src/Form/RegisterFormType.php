<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'E-mail *',
                'constraints' => [
                    new Email([
                        'message' => "Votre email n'est pas au bon format : mail@exemple.fr"   
                    ]),
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                    new Length([
                        'min' => 4,
                        'max' => 180,
                        'minMessage' => "Votre email doit comporter {{ limit }} caractères minimum.",
                        'maxMessage' => "Votre email doit comporter {{ limit }} caractères maximum."
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe *',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                    
                    new Regex([
                        'pattern' => "/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[@?!$*]){12,}/",
                        'message' => 'min caractères : 12, au moins 1 caractère spécial, au moins 1 majuscule, au moins 1 minuscule, au moins 1 chiffre.'
                    ]),
                ],
                'help' => "* min caractères : 12,
                           * au moins 1 caractère spécial,
                           * au moins 1 majuscule,
                           * au moins 1 minuscule,
                           * au moins 1 chiffre"
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom *',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom *',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'validate' => false,
                'attr' => [
                    'class' => 'd-block col-3 my-3 mx-auto btn btn-warning'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
