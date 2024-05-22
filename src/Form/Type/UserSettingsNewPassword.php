<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSettingsNewPassword extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'user-bundle',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod('POST');
        $builder
            ->add(
                'password',
                PasswordType::class,
                [
                    'required' => false,
                    'label' => 'user.settings.new_password.form.old_password',
                    'attr' => [
                        'placeholder' => 'user.settings.new_password.form.old_password',
                        'autocomplete' => 'off',
                    ],
                    'row_attr' => [
                        'class' => 'mb-3',
                    ],
                ]
            )
            ->add(
                'new_password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'required' => false,
                    'first_options' => [
                        'label' => 'user.settings.new_password.form.new_password',
                        'attr' => [
                            'placeholder' => 'user.settings.new_password.form.new_password',
                            'autocomplete' => 'new-password',
                        ],
                        'row_attr' => [
                            'class' => 'mb-3',
                        ],
                    ],
                    'second_options' => [
                        'label' => 'user.settings.new_password.form.new_password_check',
                        'attr' => [
                            'placeholder' => 'user.settings.new_password.form.new_password_check',
                        ],
                        'row_attr' => [
                            'class' => 'mb-3',
                        ],
                    ],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'user.settings.new_password.form.submit',
                    'attr' => [
                        'class' => 'w-100 btn btn-primary mt-5',
                    ],
                ]
            );
    }
}
