<?php

namespace App\Requests;
use Symfony\Component\Validator\Constraints as Assert;

class CinemaRequest extends BaseRequest
{
    
    #[Assert\Length(max: 128)]
    #[Assert\Type(type: 'string', message: "Test d'erreur")]
    #[Assert\NotBlank()]
    protected $name;
}