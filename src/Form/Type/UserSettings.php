<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Form\FormSettings;

class UserSettings extends AbstractType
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
                'username',
                TextType::class,
                [
                    'label' => 'user.settings.form.username',
                    'attr' => [
                        'placeholder' => 'user.settings.form.username',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'display_name',
                TextType::class,
                [
                    'label' => 'user.settings.form.display_name',
                    'attr' => [
                        'placeholder' => 'user.settings.form.display_name',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'is_emailing_enabled',
                CheckboxType::class,
                [
                    'label' => 'user.settings.form.is_emailing_enabled',
                    'required' => false,
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'user.settings.form.submit',
                    'attr' => [
                        'class' => FormSettings::SUBMIT_CLASS,
                    ],
                ]
            );
    }
}
