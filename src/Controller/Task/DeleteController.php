<?php declare(strict_types=1);

namespace App\Controller\Task;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DeleteController extends AbstractController
{
    public function __construct(private readonly TaskRepository $repository)
    {
    }

    // Uses in-built ValueResolver to get Task by ID from the database.
    // Otherwise, it would be needed to get ID from the request and find the Task by it.
    #[Route(path: '/tasks/{id}', methods: [Request::METHOD_DELETE])]
    public function __invoke(Task $task): Response
    {
        $this->repository->remove($task, true);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
