<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\ProjectMember;
use App\Entity\User;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectService
{
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getAllForUser(User $user): array
    {
        if (in_array(User::ROLE_ADMIN, $user->getRoles(), true)) {
            return $this->projectRepository->findBy([], ['createdAt' => 'DESC']);
        }

        return $this->projectRepository->findByMember($user->getId());
    }

    public function getById(int $id, User $user): Project
    {
        $project = $this->projectRepository->find($id);

        if (!$project) {
            throw new NotFoundHttpException('Project not found');
        }

        $this->assertAccess($project, $user);

        return $project;
    }

    public function create(array $data, User $owner): Project
    {
        $project = new Project();
        $project->setName($data['name']);
        $project->setDescription($data['description'] ?? null);
        $project->setStatus($data['status'] ?? Project::STATUS_PLANNING);
        $project->setOwner($owner);

        if (!empty($data['startDate'])) {
            $project->setStartDate(new \DateTimeImmutable($data['startDate']));
        }

        if (!empty($data['endDate'])) {
            $project->setEndDate(new \DateTimeImmutable($data['endDate']));
        }

        // Owner is automatically a lead member
        $member = new ProjectMember();
        $member->setProject($project);
        $member->setUser($owner);
        $member->setRole(ProjectMember::ROLE_LEAD);
        $project->addMember($member);

        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return $project;
    }

    public function update(Project $project, array $data, User $user): Project
    {
        $this->assertManagerAccess($project, $user);

        if (isset($data['name'])) {
            $project->setName($data['name']);
        }

        if (array_key_exists('description', $data)) {
            $project->setDescription($data['description']);
        }

        if (isset($data['status'])) {
            $project->setStatus($data['status']);
        }

        if (!empty($data['startDate'])) {
            $project->setStartDate(new \DateTimeImmutable($data['startDate']));
        }

        if (!empty($data['endDate'])) {
            $project->setEndDate(new \DateTimeImmutable($data['endDate']));
        }

        $this->entityManager->flush();

        return $project;
    }

    public function delete(Project $project, User $user): void
    {
        if ($project->getOwner() !== $user && !in_array(User::ROLE_ADMIN, $user->getRoles(), true)) {
            throw new AccessDeniedHttpException('Only project owner or admin can delete this project');
        }

        $this->entityManager->remove($project);
        $this->entityManager->flush();
    }

    public function addMember(Project $project, User $memberUser, string $role, User $currentUser): ProjectMember
    {
        $this->assertManagerAccess($project, $currentUser);

        $member = new ProjectMember();
        $member->setProject($project);
        $member->setUser($memberUser);
        $member->setRole($role);

        $this->entityManager->persist($member);
        $this->entityManager->flush();

        return $member;
    }

    private function assertAccess(Project $project, User $user): void
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

    private function assertManagerAccess(Project $project, User $user): void
    {
        if (in_array(User::ROLE_ADMIN, $user->getRoles(), true)) {
            return;
        }

        if ($project->getOwner() === $user) {
            return;
        }

        foreach ($project->getMembers() as $member) {
            if ($member->getUser() === $user && $member->getRole() === ProjectMember::ROLE_LEAD) {
                return;
            }
        }

        throw new AccessDeniedHttpException('Manager access required');
    }
}
