<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Form\FormSettings;

class SignUp extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id' => 'sign_in',
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
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'user.sign_up.form.email',
                    ],
                ]
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'required' => true,
                    'first_options' => [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'user.sign_up.form.password',
                            'autocomplete' => 'new-password',
                        ],
                    ],
                    'second_options' => [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'user.sign_up.form.password_check',
                        ],
                    ],
                ]
            )
            ->add(
                'terms',
                CheckboxType::class,
                [
                    'label' => 'user.sign_up.form.terms',
                    'label_translation_parameters' => [
                        // '%terms_url%' => $this->router->generate('page_terms'),
                    ],
                    'required' => true,
                    'label_html' => true,
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'user.sign_up.form.submit',
                    'attr' => [
                        'class' => FormSettings::SUBMIT_CLASS,
                    ],
                ]
            );
        $builder->add('_csrf_token', HiddenType::class);
    }
}
