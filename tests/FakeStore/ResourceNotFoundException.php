<?php declare(strict_types=1);

namespace App\Tests\FakeStore;

use Exception;

final class ResourceNotFoundException extends Exception
{
    public function __construct(string $message = 'resource not found')
    {
        parent::__construct($message);
    }
}
