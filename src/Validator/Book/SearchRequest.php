<?php
namespace App\Validator\Book;
use Symfony\Component\Validator\Constraints as Assert;

class SearchRequest
{
    /**
     * @var nameRu
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    public $query;

    /**
     * @var int
     *
     * @Assert\Type("int")
     * @Assert\NotBlank
     */
    public $page = 1;

    /**
     * @var int
     *
     * @Assert\Type("int")
     * @Assert\NotBlank
     * @Assert\Range(
     *      min = 1,
     *      max = 5,
     * )
     */
    public $itemsPerPage = 5;

    public function getQueryString(): string
    {
        return $this->query;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

}