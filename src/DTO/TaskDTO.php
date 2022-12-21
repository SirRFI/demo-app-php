<?php declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class TaskDTO
{
    #[Assert\NotBlank]
    public readonly string $title;

    public readonly string $description;

    public function __construct(string $title, string $description)
    {
        $this->title = trim($title);
        $this->description = trim($description);
    }
}
