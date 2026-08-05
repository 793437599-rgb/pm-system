<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Message\SendNotificationMessage;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

class TaskService
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function getAllForProject(Project $project): array
    {
        return $this->taskRepository->findByProject($project->getId());
    }

    public function getById(int $id, User $user): Task
    {
        $task = $this->taskRepository->find($id);

        if (!$task) {
            throw new NotFoundHttpException('Task not found');
        }

        $this->assertProjectAccess($task->getProject(), $user);

        return $task;
    }

    public function create(array $data, Project $project, User $reporter): Task
    {
        $this->assertProjectAccess($project, $reporter);

        $task = new Task();
        $task->setTitle($data['title']);
        $task->setDescription($data['description'] ?? null);
        $task->setStatus($data['status'] ?? Task::STATUS_TODO);
        $task->setPriority($data['priority'] ?? Task::PRIORITY_MEDIUM);
        $task->setProject($project);
        $task->setReporter($reporter);

        if (!empty($data['assigneeId'])) {
            $assignee = $this->entityManager->getRepository(User::class)->find($data['assigneeId']);
            if ($assignee) {
                $task->setAssignee($assignee);
            }
        }

        if (!empty($data['dueDate'])) {
            $task->setDueDate(new \DateTimeImmutable($data['dueDate']));
        }

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        if ($task->getAssignee()) {
            $this->messageBus->dispatch(new SendNotificationMessage(
                $task->getAssignee()->getEmail(),
                'New task assigned',
                sprintf('You have been assigned to task "%s" in project "%s".', $task->getTitle(), $project->getName())
            ));
        }

        return $task;
    }

    public function update(Task $task, array $data, User $user): Task
    {
        $this->assertProjectAccess($task->getProject(), $user);

        if (isset($data['title'])) {
            $task->setTitle($data['title']);
        }

        if (array_key_exists('description', $data)) {
            $task->setDescription($data['description']);
        }

        if (isset($data['status'])) {
            $task->setStatus($data['status']);
        }

        if (isset($data['priority'])) {
            $task->setPriority($data['priority']);
        }

        if (!empty($data['assigneeId'])) {
            $assignee = $this->entityManager->getRepository(User::class)->find($data['assigneeId']);
            $task->setAssignee($assignee);
        }

        if (!empty($data['dueDate'])) {
            $task->setDueDate(new \DateTimeImmutable($data['dueDate']));
        }

        $this->entityManager->flush();

        return $task;
    }

    public function delete(Task $task, User $user): void
    {
        $this->assertProjectAccess($task->getProject(), $user);

        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }

    private function assertProjectAccess(Project $project, User $user): void
    {
        if (in_array(User::ROLE_ADMIN, $user->getRoles(), true)) {
            return;
        }

        if ($project->getOwner() === $user) {
            return;
        }

        foreach ($project->getMembers() as $member) {
            if ($member->getUser() === $user) {
                return;
            }
        }

        throw new AccessDeniedHttpException('You do not have access to this project');
    }
}
