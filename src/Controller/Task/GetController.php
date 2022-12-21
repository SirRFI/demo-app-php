<?php declare(strict_types=1);

namespace App\Controller\Task;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class GetController extends AbstractController
{
    // Uses in-built ValueResolver to get Task by ID from the database.
    // Otherwise, it would be needed to get ID from the request and find the Task by it.
    #[Route(path: '/tasks/{id}', methods: [Request::METHOD_GET])]
    public function __invoke(Task $task): Response
    {
        return $this->json($task, Response::HTTP_OK);
    }
}
