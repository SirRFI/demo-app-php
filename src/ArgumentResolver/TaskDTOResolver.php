<?php declare(strict_types=1);

namespace App\ArgumentResolver;

use App\DTO\TaskDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TaskDTOResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== TaskDTO::class) {
            return [];
        }

        $dto = $this->serializer->deserialize($request->getContent(), TaskDTO::class, 'json');
        $errors = $this->validator->validate($dto);
        if ($errors->count() !== 0) {
            throw new HttpException(Response::HTTP_BAD_REQUEST);
        }

        return [$dto];
    }
}
