<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class CinemaDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,
    ) {
    }
}
