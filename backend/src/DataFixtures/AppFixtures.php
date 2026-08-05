<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setRoles([User::ROLE_ADMIN]);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $managerUser = new User();
        $managerUser->setEmail('manager@example.com');
        $managerUser->setFirstName('Project');
        $managerUser->setLastName('Manager');
        $managerUser->setRoles([User::ROLE_MANAGER]);
        $managerUser->setPassword($this->passwordHasher->hashPassword($managerUser, 'manager123'));
        $manager->persist($managerUser);

        $user = new User();
        $user->setEmail('user@example.com');
        $user->setFirstName('Regular');
        $user->setLastName('User');
        $user->setRoles([User::ROLE_USER]);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        $manager->persist($user);

        $project = new Project();
        $project->setName('Website Redesign');
        $project->setDescription('Redesign the company website with modern UX.');
        $project->setStatus(Project::STATUS_ACTIVE);
        $project->setOwner($managerUser);
        $project->setStartDate(new \DateTimeImmutable('-1 month'));
        $project->setEndDate(new \DateTimeImmutable('+2 months'));
        $manager->persist($project);

        $task1 = new Task();
        $task1->setTitle('Create wireframes');
        $task1->setDescription('Design low-fidelity wireframes for all pages.');
        $task1->setStatus(Task::STATUS_DONE);
        $task1->setPriority(Task::PRIORITY_HIGH);
        $task1->setProject($project);
        $task1->setReporter($managerUser);
        $task1->setAssignee($user);
        $manager->persist($task1);

        $task2 = new Task();
        $task2->setTitle('Implement authentication');
        $task2->setDescription('Set up JWT authentication for the API.');
        $task2->setStatus(Task::STATUS_IN_PROGRESS);
        $task2->setPriority(Task::PRIORITY_URGENT);
        $task2->setProject($project);
        $task2->setReporter($managerUser);
        $task2->setAssignee($user);
        $task2->setDueDate(new \DateTimeImmutable('+1 week'));
        $manager->persist($task2);

        $manager->flush();
    }
}
