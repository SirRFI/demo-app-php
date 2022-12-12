<?php declare(strict_types=1);

namespace App\Tests\FakeStore;

use App\FakeStore\AddProductCommand;
use App\FakeStore\API;
use App\FakeStore\UpdateProductCommand;
use App\Tests\TestHelper;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final class APITest extends KernelTestCase
{
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $container = static::getContainer();
        $this->serializer = $container->get(SerializerInterface::class);
    }

    public function testGetProductsSendsProperRequestAndReturnsSuccessfulResponse(): void
    {
        $expectedResult = SampleDataProvider::products();
        $response = new MockResponse(TestHelper::readFile(__DIR__ . '/samples/getProducts-response-200.json'), [
            'http_code' => Response::HTTP_OK,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($response);

        $api = new API($httpClient, $this->serializer);
        $result = $api->getProducts();

        self::assertSame(Request::METHOD_GET, $response->getRequestMethod());
        self::assertSame(TestHelper::HTTP_CLIENT_BASEURL . '/products', $response->getRequestUrl());
        self::assertSameSize($expectedResult, $result);
        self::assertEquals($expectedResult, $result);
    }

    public function testGetProductsThrowsOnAPIIssues(): void
    {
        $response = new MockResponse('', ['http_code' => Response::HTTP_INTERNAL_SERVER_ERROR]);
        $httpClient = new MockHttpClient($response);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('API responded with status 500');

        $api = new API($httpClient, $this->serializer);
        $api->getProducts();
    }

    public function testGetProductSendsProperRequestAndReturnsSuccessfulResponse(): void
    {
        $expectedResult = SampleDataProvider::products()[16 - 1];
        $response = new MockResponse(TestHelper::readFile(__DIR__ . '/samples/getProduct-16-response-200.json'), [
            'http_code' => Response::HTTP_OK,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($response);

        $api = new API($httpClient, $this->serializer);
        $result = $api->getProduct(16);

        self::assertSame(Request::METHOD_GET, $response->getRequestMethod());
        self::assertSame(TestHelper::HTTP_CLIENT_BASEURL . '/products/16', $response->getRequestUrl());
        self::assertEquals($expectedResult, $result);
    }

    public function testGetProductThrowsResourceNotFoundException(): void
    {
        // This API does not return 404 Not Found. Instead, it returns empty response body.
        $response = new MockResponse('', [
            'http_code' => Response::HTTP_OK,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($response);

        self::expectException(ResourceNotFoundException::class);

        $api = new API($httpClient, $this->serializer);
        $api->getProduct(1337);
    }

    public function testGetProductThrowsOnAPIIssues(): void
    {
        $response = new MockResponse('', ['http_code' => Response::HTTP_INTERNAL_SERVER_ERROR]);
        $httpClient = new MockHttpClient($response);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('API responded with status 500');

        $api = new API($httpClient, $this->serializer);
        $api->getProduct(16);
    }

    public function testAddProductSendsProperRequestAndReturnsSuccessfulResponse(): void
    {
        $expectedResult = SampleDataProvider::newProduct(21);
        $expectedRequestData = TestHelper::readFile(__DIR__ . '/samples/addProduct-request.json');
        $response = new MockResponse(TestHelper::readFile(__DIR__ . '/samples/addProduct-response-200.json'), [
            'http_code' => Response::HTTP_OK,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($response);

        $api = new API($httpClient, $this->serializer);
        $command = new AddProductCommand(
            $expectedResult->title,
            $expectedResult->price,
            $expectedResult->description,
            $expectedResult->category,
            $expectedResult->image,
        );
        $result = $api->addProduct($command);

        self::assertSame(Request::METHOD_POST, $response->getRequestMethod());
        self::assertSame(TestHelper::HTTP_CLIENT_BASEURL . '/products', $response->getRequestUrl());
        self::assertContains('Content-Type: application/json', $response->getRequestOptions()['headers']);
        self::assertJsonStringEqualsJsonString($expectedRequestData, $response->getRequestOptions()['body']);
        self::assertEquals($expectedResult, $result);
    }

    public function testAddProductThrowsOnAPIIssues(): void
    {
        $product = SampleDataProvider::newProduct(21);
        $response = new MockResponse('', ['http_code' => Response::HTTP_INTERNAL_SERVER_ERROR]);
        $httpClient = new MockHttpClient($response);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('API responded with status 500');

        $api = new API($httpClient, $this->serializer);
        $command = new AddProductCommand(
            $product->title,
            $product->price,
            $product->description,
            $product->category,
            $product->image,
        );
        $api->addProduct($command);
    }

    public function testUpdateProductSendsProperRequestAndReturnsSuccessfulResponse(): void
    {
        $expectedResult = SampleDataProvider::newProduct(16);
        $expectedRequestData = TestHelper::readFile(__DIR__ . '/samples/updateProduct-16-request.json');
        $response = new MockResponse(TestHelper::readFile(__DIR__ . '/samples/updateProduct-16-response-200.json'), [
            'http_code' => Response::HTTP_OK,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($response);

        $api = new API($httpClient, $this->serializer);
        $command = new UpdateProductCommand(
            $expectedResult->id,
            $expectedResult->title,
            $expectedResult->price,
            $expectedResult->description,
            $expectedResult->category,
            $expectedResult->image,
        );
        $result = $api->updateProduct($command);

        self::assertSame(Request::METHOD_PUT, $response->getRequestMethod());
        self::assertSame(TestHelper::HTTP_CLIENT_BASEURL . '/products/16', $response->getRequestUrl());
        self::assertContains('Content-Type: application/json', $response->getRequestOptions()['headers']);
        self::assertJsonStringEqualsJsonString($expectedRequestData, $response->getRequestOptions()['body']);
        self::assertEquals($expectedResult, $result);
    }

    public function testUpdateProductThrowsOnAPIIssues(): void
    {
        $expectedResult = SampleDataProvider::newProduct(16);
        $response = new MockResponse('', ['http_code' => Response::HTTP_INTERNAL_SERVER_ERROR]);
        $httpClient = new MockHttpClient($response);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('API responded with status 500');

        $api = new API($httpClient, $this->serializer);
        $command = new UpdateProductCommand(
            $expectedResult->id,
            $expectedResult->title,
            $expectedResult->price,
            $expectedResult->description,
            $expectedResult->category,
            $expectedResult->image,
        );
        $api->updateProduct($command);
    }

    public function testDeleteProductSendsProperRequestAndReturnsSuccessfulResponse(): void
    {
        $response = new MockResponse(TestHelper::readFile(__DIR__ . '/samples/deleteProduct-16-response-200.json'), [
            'http_code' => Response::HTTP_OK,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($response);

        $api = new API($httpClient, $this->serializer);
        $api->deleteProduct(16);

        self::assertSame(Request::METHOD_DELETE, $response->getRequestMethod());
        self::assertSame(TestHelper::HTTP_CLIENT_BASEURL . '/products/16', $response->getRequestUrl());
    }

    public function testDeleteProductThrowsResourceNotFound(): void
    {
        // This API does not return 204 No Content or 404 Not Found.
        // Instead, it returns the entire resource if it existed, and null when it didn't.
        $response = new MockResponse(TestHelper::readFile(__DIR__ . '/samples/deleteProduct-1337-response-200.json'), [
            'http_code' => Response::HTTP_OK,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($response);

        self::expectException(ResourceNotFoundException::class);

        $api = new API($httpClient, $this->serializer);
        $api->deleteProduct(1337);
    }

    public function testDeleteProductThrowsOnAPIIssues(): void
    {
        $response = new MockResponse('', ['http_code' => Response::HTTP_INTERNAL_SERVER_ERROR]);
        $httpClient = new MockHttpClient($response);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('API responded with status 500');

        $api = new API($httpClient, $this->serializer);
        $api->deleteProduct(16);
    }
}
