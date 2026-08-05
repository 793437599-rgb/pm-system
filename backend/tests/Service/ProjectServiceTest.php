<?php

namespace App\Tests\Service;

use App\Entity\Project;
use App\Entity\User;
use App\Service\ProjectService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ProjectServiceTest extends TestCase
{
    public function testCreateProject(): void
    {
        $projectRepository = $this->createMock(\App\Repository\ProjectRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Project::class));

        $entityManager->expects($this->once())
            ->method('flush');

        $service = new ProjectService($projectRepository, $entityManager);

        $owner = new User();
        $owner->setEmail('owner@example.com');
        $owner->setFirstName('Owner');
        $owner->setLastName('User');
        $owner->setPassword('password');

        $project = $service->create(['name' => 'Test Project'], $owner);

        $this->assertEquals('Test Project', $project->getName());
        $this->assertEquals(Project::STATUS_PLANNING, $project->getStatus());
        $this->assertSame($owner, $project->getOwner());
        $this->assertCount(1, $project->getMembers());
    }
}
