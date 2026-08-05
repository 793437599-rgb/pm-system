<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ProjectService;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class TaskController extends AbstractController
{
    public function __construct(
        private readonly TaskService $taskService,
        private readonly ProjectService $projectService,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('/projects/{projectId}/tasks', name: 'api_task_list', methods: ['GET'])]
    public function list(int $projectId): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $project = $this->projectService->getById($projectId, $user);
        $tasks = $this->taskService->getAllForProject($project);

        return $this->json(
            $this->serializer->serialize($tasks, 'json', ['groups' => 'task:read']),
            Response::HTTP_OK,
            [],
            ['json' => true]
        );
    }

    #[Route('/tasks', name: 'api_task_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (empty($data['title']) || empty($data['projectId'])) {
            return $this->json(['error' => 'Title and projectId are required'], Response::HTTP_BAD_REQUEST);
        }

        $project = $this->projectService->getById((int) $data['projectId'], $user);
        $task = $this->taskService->create($data, $project, $user);

        return $this->json(
            $this->serializer->serialize($task, 'json', ['groups' => 'task:read']),
            Response::HTTP_CREATED,
            [],
            ['json' => true]
        );
    }

    #[Route('/tasks/{id}', name: 'api_task_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $task = $this->taskService->getById($id, $user);

        return $this->json(
            $this->serializer->serialize($task, 'json', ['groups' => 'task:read']),
            Response::HTTP_OK,
            [],
            ['json' => true]
        );
    }

    #[Route('/tasks/{id}', name: 'api_task_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $task = $this->taskService->getById($id, $user);
        $data = json_decode($request->getContent(), true);

        $task = $this->taskService->update($task, $data, $user);

        return $this->json(
            $this->serializer->serialize($task, 'json', ['groups' => 'task:read']),
            Response::HTTP_OK,
            [],
            ['json' => true]
        );
    }

    #[Route('/tasks/{id}', name: 'api_task_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $task = $this->taskService->getById($id, $user);

        $this->taskService->delete($task, $user);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
