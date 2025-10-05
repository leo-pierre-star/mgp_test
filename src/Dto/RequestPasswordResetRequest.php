<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RequestPasswordResetRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;
}
