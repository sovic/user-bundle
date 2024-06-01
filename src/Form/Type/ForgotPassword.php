<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Form\FormSettings;

class ForgotPassword extends AbstractType
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
                'email',
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'user.forgot_password.form.email',
                    ],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'user.forgot_password.form.submit',
                    'attr' => [
                        'class' => FormSettings::SUBMIT_CLASS,
                    ],
                ]
            );
        $builder->add('_csrf_token', HiddenType::class);
    }
}
