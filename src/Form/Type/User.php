<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Form\FormSettings;

class User extends AbstractType
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
                EmailType::class,
                [
                    'label' => 'user.edit.form.email',
                    'attr' => [
                        'placeholder' => 'user.edit.form.email',
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'is_enabled',
                CheckboxType::class,
                [
                    'label' => 'user.edit.form.is_enabled',
                    'required' => false,
                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    'label' => 'user.edit.form.username',
                    'attr' => [
                        'placeholder' => 'user.edit.form.username',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'is_emailing_enabled',
                CheckboxType::class,
                [
                    'label' => 'user.edit.form.is_emailing_enabled',
                    'required' => false,
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'user.edit.form.submit',
                    'attr' => [
                        'class' => FormSettings::SUBMIT_CLASS,
                    ],
                ]
            );
    }
}
