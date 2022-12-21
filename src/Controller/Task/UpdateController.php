<?php declare(strict_types=1);

namespace App\Controller\Task;

use App\DTO\TaskDTO;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UpdateController extends AbstractController
{
    public function __construct(private readonly TaskRepository $repository)
    {
    }

    // Uses TaskDTOResolver to deserialize, validate and map data to TaskDTO, which is then passed here.
    // Also uses in-built ValueResolver to get Task by ID.
    // ValueResolvers are executed in set order dictated by priority settings, which in this case decides whether
    // 400 Bad Request or 404 Not Found is returned in case both id and request data are invalid. See:
    // bin/console debug:container debug.argument_resolver.inner --show-arguments
    #[Route(path: '/tasks/{id}', methods: [Request::METHOD_PUT])]
    public function __invoke(TaskDTO $dto, Task $task): Response
    {
        $task->update($dto->title, $dto->description);
        $this->repository->save($task, true);

        return $this->json($task, Response::HTTP_OK);
    }
}
