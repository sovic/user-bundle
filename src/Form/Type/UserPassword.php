<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Form\FormSettings;

class UserPassword extends AbstractType
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

        $builder->add(
            'new_password',
            PasswordType::class,
            [
                'label' => 'user.password.form.new_password',
                'attr' => [
                    'placeholder' => 'user.password.form.new_password',
                    'autocomplete' => 'new-password',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'send_email',
            CheckboxType::class,
            [
                'label' => 'user.password.form.send_email',
                'required' => false,
            ]
        );

        $builder->add(
            'save',
            SubmitType::class,
            [
                'label' => 'user.password.form.submit',
                'attr' => [
                    'class' => FormSettings::SUBMIT_CLASS,
                ],
            ]
        );
    }
}
