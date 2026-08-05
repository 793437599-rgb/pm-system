# Symfony Project Management System

A full-stack project management system built with **Symfony 7** (backend API) and **Vue 3** (frontend admin panel).

## Tech Stack

### Backend
- **Symfony 7** (PHP 8.2+)
- **PostgreSQL** (primary database)
- **Redis** (caching & session storage)
- **Symfony Messenger** + **RabbitMQ** (async task queue)
- **Lexik JWT Authentication** (stateless API auth)
- **Doctrine ORM** + Migrations
- **PHPUnit** (unit & functional tests)
- **Docker** + Docker Compose

### Frontend
- **Vue 3** + **Vite**
- **Pinia** (state management)
- **Vue Router**
- **Axios**
- **Element Plus** (UI components)

### DevOps
- **GitHub Actions** CI pipeline
- Docker multi-stage builds
- Nginx reverse proxy

## Features

- JWT-based authentication
- Project CRUD with members
- Task management with statuses & priorities
- Dashboard with project statistics (Redis cached)
- Async email notifications via Messenger queue
- RESTful API with JSON responses
- Responsive admin dashboard
- Comprehensive PHPUnit tests

## Project Structure

```
pm-system/
├── backend/          # Symfony API
├── frontend/         # Vue 3 admin panel
├── docker-compose.yml
└── README.md
```

## Quick Start

### With Docker

```bash
cd pm-system
docker-compose up -d
```

Services will be available at:
- Frontend: http://localhost:8080
- Backend API: http://localhost:8081
- API Docs: http://localhost:8081/api/doc

### Manual Setup

**Backend:**
```bash
cd backend
cp .env.example .env
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
php -S localhost:8081 -t public
```

**Frontend:**
```bash
cd frontend
npm install
npm run dev
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/login | Authenticate & get JWT |
| GET | /api/projects | List projects |
| POST | /api/projects | Create project |
| GET | /api/projects/{id} | Project detail |
| PUT | /api/projects/{id} | Update project |
| DELETE | /api/projects/{id} | Delete project |
| GET | /api/projects/{id}/tasks | List tasks |
| POST | /api/tasks | Create task |
| PUT | /api/tasks/{id} | Update task |
| DELETE | /api/tasks/{id} | Delete task |
| GET | /api/dashboard | Dashboard stats |

## Testing

```bash
cd backend
php bin/phpunit
```

## License

MIT
