<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class ProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('/projects', name: 'api_project_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $projects = $this->projectService->getAllForUser($user);

        return $this->json(
            $this->serializer->serialize($projects, 'json', ['groups' => 'project:read']),
            Response::HTTP_OK,
            [],
            ['json' => true]
        );
    }

    #[Route('/projects', name: 'api_project_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (empty($data['name'])) {
            return $this->json(['error' => 'Project name is required'], Response::HTTP_BAD_REQUEST);
        }

        $project = $this->projectService->create($data, $user);

        return $this->json(
            $this->serializer->serialize($project, 'json', ['groups' => 'project:read']),
            Response::HTTP_CREATED,
            [],
            ['json' => true]
        );
    }

    #[Route('/projects/{id}', name: 'api_project_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $project = $this->projectService->getById($id, $user);

        return $this->json(
            $this->serializer->serialize($project, 'json', ['groups' => 'project:read']),
            Response::HTTP_OK,
            [],
            ['json' => true]
        );
    }

    #[Route('/projects/{id}', name: 'api_project_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $project = $this->projectService->getById($id, $user);
        $data = json_decode($request->getContent(), true);

        $project = $this->projectService->update($project, $data, $user);

        return $this->json(
            $this->serializer->serialize($project, 'json', ['groups' => 'project:read']),
            Response::HTTP_OK,
            [],
            ['json' => true]
        );
    }

    #[Route('/projects/{id}', name: 'api_project_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $project = $this->projectService->getById($id, $user);

        $this->projectService->delete($project, $user);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
