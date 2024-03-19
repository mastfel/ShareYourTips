<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstname',TextType::class, [ 
            'label' => 'Prénom *',
            'constraints' => [
                new NotBlank([
                    'message' => 'Ce champ ne peut être vide'
                ]),
            ],

            ])
            ->add('lastname',TextType::class, [
                'label' => 'Nom *',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                ],
                ])
            ->add('email',EmailType::class, [
                 'label' => 'E-mail *',
                 'constraints' => [
                    new Email([
                        'message' => "Votre email n'est pas au bon format : mail@exemple.fr"
                    ]),
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                   
                ],

            ])
            ->add('message', TextareaType::class, [
                'label' => ' Votre message *',
                'attr' => ['rows' => 6],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                ],
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'validate' => false,
                'attr' => [
                    'class' => 'd-block mx-auto my-3 col-3 btn btn-warning'
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
