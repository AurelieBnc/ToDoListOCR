<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * secure password requirements
 */
#[\Attribute]
class PasswordRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(),
            new Assert\Type('string'),
            new Assert\Length(
                [
                    'min' => 12,
                    'minMessage' => 'Le mot de passe doit contenir au moins 12 caractères.',
                    'max' => 4096,
                ]
            ),
            // Password must contain at least a upper and lower case.
            new Assert\Regex(
                [
                    'pattern' => '/(?=.*[a-z])(?=.*[A-Z])/',
                    'message' => 'Le mot de passe doit contenir au moins une majuscule et une minuscule.',
                    'match' => true,
                ]
            ),
            // Password must contain at least one digit.
            new Assert\Regex(
                [
                    'pattern' => '/\d+/i',
                    'message' => 'Le mot de passe doit contenir au moins un chiffre.',
                    'match' => true,
                ]
            ),
            // Password must contain at least one special char from the list (including space).
            new Assert\Regex(
                [
                    'pattern' => '/[^a-zA-Z0-9\n\r]+/i',
                    'message' => 'Le mot de passe doit contenir au moins un caractère spécial.',
                    'match' => true,
                ]
            ),
        ];

    }
}
