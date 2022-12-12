<?php declare(strict_types=1);

namespace App\FakeStore;

use InvalidArgumentException;

final class UpdateProductCommand
{
    public readonly int $id;
    public readonly string $title;
    public readonly float $price;
    public readonly string $description;
    public readonly string $category;
    public readonly string $image;

    // FakeStore does not have any requirements for the data, so the validation below is purely for demo purposes.
    public function __construct(
        int $id,
        string $title,
        float $price,
        string $description,
        string $category,
        string $image,
    ) {
        $title = trim($title);
        if ($title === '') {
            throw new InvalidArgumentException('title must not be empty');
        }
        if ($price < 0) {
            throw new InvalidArgumentException('price must not be negative');
        }
        $category = trim($category);
        if ($category === '') {
            throw new InvalidArgumentException('undefined category');
        }

        $this->id = $id;
        $this->title = $title;
        $this->price = $price;
        $this->description = $description;
        $this->category = $category;
        $this->image = $image;
    }
}
