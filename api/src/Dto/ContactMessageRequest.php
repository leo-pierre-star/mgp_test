<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ContactMessageRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    public string $subject;

    #[Assert\NotBlank]
    #[Assert\Length(max: 5000)]
    public string $message;
}
