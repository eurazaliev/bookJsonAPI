<?php
namespace App\Validator\Book;
use Symfony\Component\Validator\Constraints as Assert;

class PostRequest
{
    /**
     * @var nameRu
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    public $nameRu;

    /**
     * @var nameEn
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    public $nameEn;

    /**
     * @var authorId
     *
     * @Assert\Type("int")
     * @Assert\NotBlank
     */
    public $authorId;

    public function getNameRu(): string
    {
        return $this->nameRu;
    }

    public function getNameEn(): string
    {
        return $this->nameEn;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }
}