<?php declare(strict_types=1);

namespace App\FakeStore;

use App\Tests\FakeStore\ResourceNotFoundException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class API
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly SerializerInterface $serializer,
    )
    {
    }

    /** @return Product[] */
    public function getProducts(): array
    {
        // The request and most methods in response can throw an exception, but for simplicity they are not caught here.
        $response = $this->httpClient->request(Request::METHOD_GET, '/products');
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new RuntimeException(sprintf('API responded with status %d', $response->getStatusCode()));
        }

        // For simplicity of the demo, response data is not validated.
        return $this->serializer->deserialize($response->getContent(), Product::class . '[]', 'json');
    }

    public function getProduct(int $id): Product
    {
        // The request and most methods in response can throw an exception, but for simplicity they are not caught here.
        $response = $this->httpClient->request(Request::METHOD_GET, sprintf('/products/%d', $id));
        // This API does not return 404 Not Found. Instead, it returns empty response body.
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new RuntimeException(sprintf('API responded with status %d', $response->getStatusCode()));
        }
        // For simplicity of the demo, response data is not validated.
        $content = $response->getContent();
        if (empty($content)) {
            throw new ResourceNotFoundException();
        }

        return $this->serializer->deserialize($content, Product::class, 'json');
    }

    public function addProduct(AddProductCommand $command): Product
    {
        // The request and most methods in response can throw an exception, but for simplicity they are not caught here.
        $response = $this->httpClient->request(Request::METHOD_POST, '/products', [
            'json' => [
                'title' => $command->title,
                'price' => $command->price,
                'description' => $command->description,
                'category' => $command->category,
                'image' => $command->image,
            ],
        ]);
        // This API does not return 201 Created status, neither validates data anyhow to return 400 Bad Request.
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new RuntimeException(sprintf('API responded with status %d', $response->getStatusCode()));
        }

        // The API also returns only assigned id, instead of entire object.
        // For simplicity of the demo, it's not validated.
        $data = $response->toArray();

        return new Product(
            $data['id'],
            $command->title,
            $command->price,
            $command->description,
            $command->category,
            $command->image,
        );
    }

    public function updateProduct(UpdateProductCommand $command): Product
    {
        // The request and most methods in response can throw an exception, but for simplicity they are not caught here.
        $response = $this->httpClient->request(Request::METHOD_PUT, sprintf('/products/%d', $command->id), [
            'json' => [
                'title' => $command->title,
                'price' => $command->price,
                'description' => $command->description,
                'category' => $command->category,
                'image' => $command->image,
            ],
        ]);
        // This API does not return 404 Not Found. Unlike GetProduct, it always returns id and nothing else.
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new RuntimeException(sprintf('API responded with status %d', $response->getStatusCode()));
        }

        // For simplicity of the demo, the returned is not validated.
        $data = $response->toArray();
        if ($data['id'] !== $command->id) { // sanity check
            throw new RuntimeException(sprintf('expected product id #%d, got %d', $command->id, $data['id']));
        }

        return new Product(
            $command->id,
            $command->title,
            $command->price,
            $command->description,
            $command->category,
            $command->image,
        );
    }

    public function deleteProduct(int $id): void
    {
        // The request and most methods in response can throw an exception, but for simplicity they are not caught here.
        $response = $this->httpClient->request('DELETE', sprintf('/products/%d', $id));
        // This API does not return 204 No Content or 404 Not Found.
        // Instead, it returns the entire resource if it existed, and null when it doesn't.
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new RuntimeException(sprintf('API responded with status %d', $response->getStatusCode()));
        }

        $data = $response->getContent();
        if ($data === 'null') {
            throw new ResourceNotFoundException();
        }
    }
}
