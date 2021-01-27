<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('reset_password', RepeatedType::class,[
                'type' => PasswordType::class,
                'label' => 'New password',
                'invalid_message'=> "The password doesn't match",
                'required'=> true,
                'first_options'=>[
                    'label'=>' New password'
                ],
                'second_options'=>[
                    'label'=>'Confirm New password'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Confirm new password',
                'attr' => [
                    'class' => 'btn-block btn-info'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
