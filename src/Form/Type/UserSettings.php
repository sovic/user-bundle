<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserSettings extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod('POST');
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'label' => 'form.user_settings.username',
                    'attr' => [
                        'placeholder' => 'form.user_settings.username',
                    ],
                    'row_attr' => [
                        'class' => 'mb-3',
                    ],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'form.user_settings.submit',
                    'attr' => [
                        'class' => 'w-100 btn btn-primary',
                    ],
                ]
            );
    }
}
