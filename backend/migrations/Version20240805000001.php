<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240805000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema for project management system';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (
            id SERIAL PRIMARY KEY,
            email VARCHAR(180) NOT NULL UNIQUE,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            roles JSON NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NOT NULL,
            updated_at TIMESTAMP NOT NULL
        )');

        $this->addSql('CREATE TABLE projects (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            status VARCHAR(50) NOT NULL,
            start_date DATE DEFAULT NULL,
            end_date DATE DEFAULT NULL,
            owner_id INT NOT NULL,
            created_at TIMESTAMP NOT NULL,
            updated_at TIMESTAMP NOT NULL,
            CONSTRAINT fk_project_owner FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE
        )');

        $this->addSql('CREATE TABLE project_members (
            id SERIAL PRIMARY KEY,
            project_id INT NOT NULL,
            user_id INT NOT NULL,
            role VARCHAR(50) NOT NULL,
            joined_at TIMESTAMP NOT NULL,
            CONSTRAINT fk_member_project FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE,
            CONSTRAINT fk_member_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        )');

        $this->addSql('CREATE TABLE tasks (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            status VARCHAR(50) NOT NULL,
            priority VARCHAR(50) NOT NULL,
            project_id INT NOT NULL,
            assignee_id INT DEFAULT NULL,
            reporter_id INT NOT NULL,
            due_date DATE DEFAULT NULL,
            created_at TIMESTAMP NOT NULL,
            updated_at TIMESTAMP NOT NULL,
            CONSTRAINT fk_task_project FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE,
            CONSTRAINT fk_task_assignee FOREIGN KEY (assignee_id) REFERENCES users (id) ON DELETE SET NULL,
            CONSTRAINT fk_task_reporter FOREIGN KEY (reporter_id) REFERENCES users (id) ON DELETE CASCADE
        )');

        $this->addSql('CREATE INDEX idx_task_status ON tasks (status)');
        $this->addSql('CREATE INDEX idx_task_project ON tasks (project_id)');
        $this->addSql('CREATE INDEX idx_project_status ON projects (status)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE tasks');
        $this->addSql('DROP TABLE project_members');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE users');
    }
}
