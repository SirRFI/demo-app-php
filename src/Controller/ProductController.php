<?php declare(strict_types=1);

namespace App\Controller;

use App\DTO\ProductDTO;
use App\FakeStore\AddProductCommand;
use App\FakeStore\API;
use App\FakeStore\UpdateProductCommand;
use App\Tests\FakeStore\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProductController extends AbstractController
{
    public function __construct(
        private readonly API $api,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    )
    {
    }

    #[Route(path: '/products', methods: [Request::METHOD_GET])]
    public function getProducts(): Response
    {
        $products = $this->api->getProducts();

        return $this->json($products, Response::HTTP_OK);
    }

    #[Route(path: '/products/{id}', methods: [Request::METHOD_GET])]
    public function getProduct(int $id): Response
    {
        try {
            $product = $this->api->getProduct($id);
        } catch (ResourceNotFoundException $exception) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        return $this->json($product, Response::HTTP_OK);
    }

    #[Route(path: '/products', methods: [Request::METHOD_POST])]
    public function addProduct(Request $request): Response
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), ProductDTO::class, 'json');
        }
        catch (UnexpectedValueException $exception) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $command = new AddProductCommand(
            $dto->title,
            $dto->price,
            $dto->description,
            $dto->category,
            $dto->image,
        );
        $product = $this->api->addProduct($command);

        return $this->json($product, Response::HTTP_CREATED);
    }

    #[Route(path: '/products/{id}', methods: [Request::METHOD_PUT])]
    public function updateProduct(Request $request, int $id): Response
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), ProductDTO::class, 'json');
        }
        catch (UnexpectedValueException $exception) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $command = new UpdateProductCommand(
            $id,
            $dto->title,
            $dto->price,
            $dto->description,
            $dto->category,
            $dto->image,
        );

        try {
            $product = $this->api->updateProduct($command);
        } catch (ResourceNotFoundException $exception) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        return $this->json($product, Response::HTTP_OK);
    }

    #[Route(path: '/products/{id}', methods: [Request::METHOD_DELETE])]
    public function deleteProduct(int $id): Response
    {
        try {
            $this->api->deleteProduct($id);
        } catch (ResourceNotFoundException $exception) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
