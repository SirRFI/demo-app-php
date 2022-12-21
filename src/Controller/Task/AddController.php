<?php declare(strict_types=1);

namespace App\Controller\Task;

use App\DTO\TaskDTO;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AddController extends AbstractController
{
    public function __construct(private readonly TaskRepository $repository)
    {
    }

    // Uses TaskDTOResolver to deserialize, validate and map data to TaskDTO, which is then passed here.
    #[Route(path: '/tasks', methods: [Request::METHOD_POST])]
    public function __invoke(TaskDTO $dto): Response
    {
        $task = new Task($dto->title, $dto->description);
        $this->repository->save($task, true);

        return $this->json($task, Response::HTTP_CREATED);
    }
}
