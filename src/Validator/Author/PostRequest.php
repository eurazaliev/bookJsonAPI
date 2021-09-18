<?php
namespace App\Validator\Author;
use Symfony\Component\Validator\Constraints as Assert;

class PostRequest
{
    /**
     * @var name
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    public $name;

    public function getName(): string
    {
        return $this->name;
    }
}