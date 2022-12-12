<?php declare(strict_types=1);

namespace App\FakeStore;

final class Product
{
    // Properties are public readonly to ensure immutability and reduce getter boilerplate
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly float $price,
        public readonly string $description,
        public readonly string $category,
        public readonly string $image,
    ) {
    }
}
