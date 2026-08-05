<?php

namespace App\Service;

use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Psr\Cache\CacheItemPoolInterface;

class DashboardService
{
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly TaskRepository $taskRepository,
        private readonly CacheItemPoolInterface $dashboardCache,
    ) {
    }

    public function getStats(): array
    {
        $item = $this->dashboardCache->getItem('dashboard.stats');

        if ($item->isHit()) {
            return $item->get();
        }

        $projectStats = $this->projectRepository->countByStatus();
        $taskStats = [
            'total' => (int) $this->taskRepository->count([]),
            'overdue' => $this->taskRepository->countOverdue(),
        ];

        $data = [
            'projects' => [
                'total' => array_sum($projectStats),
                'byStatus' => $projectStats,
            ],
            'tasks' => $taskStats,
        ];

        $item->set($data);
        $item->expiresAfter(300);
        $this->dashboardCache->save($item);

        return $data;
    }

    public function invalidateCache(): void
    {
        $this->dashboardCache->deleteItem('dashboard.stats');
    }
}
