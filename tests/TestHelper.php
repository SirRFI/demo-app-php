<?php declare(strict_types=1);

namespace App\Tests;

use Exception;
use JsonException;

final class TestHelper
{
    public const HTTP_CLIENT_BASEURL = 'https://example.com';

    public static function readFile(string $path): string
    {
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new Exception(sprintf('Failed to read file "%s"', $path));
        }

        return $contents;
    }

    public static function toJSON(array $data): string
    {
        try {
            $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE);
        } catch (JsonException $exception) {
            throw new Exception('Failed to encode JSON');
        }
        // Following condition might not happen due to JSON_THROW_ON_ERROR
        if ($json === false) {
            throw new Exception('Failed to encode JSON');
        }

        return $json;
    }
}
