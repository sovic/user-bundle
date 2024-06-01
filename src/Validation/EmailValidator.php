<?php

namespace UserBundle\Validation;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;

class EmailValidator
{
    public static function validate(string $email): ConstraintViolationListInterface|bool
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate(
            $email,
            [
                new Length(
                    [
                        'min' => 3,
                    ]
                ),
                new Email(
                    [
                        'mode' => Email::VALIDATION_MODE_STRICT,
                    ]
                ),
            ]
        );
        $valid = 0 === count($violations);

        return $valid ?: $violations;
    }
}
