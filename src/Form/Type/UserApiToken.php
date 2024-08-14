<?php

declare(strict_types=1);

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserApiToken extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'user-bundle',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'token',
            TextType::class,
            [
                'disabled' => true,
                'required' => true,
                'label' => 'user.api_token.form.token',
                'attr' => [
                    'placeholder' => 'user.api_token.form.token',
                    'length' => 255,
                ],
            ]
        );

        $builder->add(
            'expiration_date',
            DateType::class,
            [
                'required' => false,
                'label' => 'user.api_token.form.expiration_date',
            ]
        );

        $builder->add(
            'save',
            SubmitType::class,
            [
                'label' => 'user.api_token.form.submit',
                'attr' => [
                    'class' => 'mt-5 w-100 btn btn-primary',
                ],
            ]
        );
    }
}
