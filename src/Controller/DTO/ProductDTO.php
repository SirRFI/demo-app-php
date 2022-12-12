<?php declare(strict_types=1);

namespace App\Controller\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductDTO
{
    #[Assert\NotBlank]
    public readonly string $title;

    #[Assert\GreaterThanOrEqual(0)]
    public readonly float $price;

    public readonly string $description;

    #[Assert\NotBlank]
    public readonly string $category;

    #[Assert\NotBlank]
    #[Assert\Url]
    public readonly string $image;

    // FakeStore does not have any requirements for the data, so the validation is purely for demo purposes.
    public function __construct(
        string $title,
        float $price,
        string $description,
        string $category,
        string $image,
    ) {
        $this->title = trim($title);
        $this->price = $price;
        $this->description = $description;
        $this->category = trim($category);
        $this->image = $image;
    }
}
