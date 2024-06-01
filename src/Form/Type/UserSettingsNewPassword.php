<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Form\FormSettings;

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
                    'label' => 'user.settings.new_password.form.old_password',
                    'attr' => [
                        'placeholder' => 'user.settings.new_password.form.old_password',
                        'autocomplete' => 'off',
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'new_password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'required' => true,
                    'first_options' => [
                        'label' => 'user.settings.new_password.form.new_password',
                        'attr' => [
                            'placeholder' => 'user.settings.new_password.form.new_password',
                            'autocomplete' => 'new-password',
                        ],
                        'required' => true,
                    ],
                    'second_options' => [
                        'label' => 'user.settings.new_password.form.new_password_check',
                        'attr' => [
                            'placeholder' => 'user.settings.new_password.form.new_password_check',
                        ],
                        'required' => true,
                    ],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'user.settings.new_password.form.submit',
                    'attr' => [
                        'class' => FormSettings::SUBMIT_CLASS,
                    ],
                ]
            );
    }
}
