<?php declare(strict_types=1);

namespace App\Controller\Task;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ListController extends AbstractController
{
    public function __construct(private readonly TaskRepository $repository)
    {
    }

    #[Route(path: '/tasks', methods: [Request::METHOD_GET])]
    public function __invoke(): Response
    {
        return $this->json($this->repository->findAll(), Response::HTTP_OK);
    }
}
