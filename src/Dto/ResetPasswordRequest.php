<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordRequest
{
    #[Assert\NotBlank]
    public string $token;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 255)]
    #[Assert\Regex(
        pattern: '/^(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z0-9]).{8,}$/',
        message: 'Le mot de passe doit contenir au moins 8 caractères dont une majuscule, un chiffre et un caractère spécial.'
    )]
    public string $newPassword;
}
