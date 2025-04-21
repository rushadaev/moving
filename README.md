# Moving App

Full-stack application with Laravel backend and Vue.js frontend.

## Prerequisites

- Docker and Docker Compose
- Git
- Postman (for API testing)

## Quick Start

1. Clone the repository:
```bash
git clone https://github.com/rushadaev/moving.git
cd moving
```

2. Install and run the project:
```bash
make install
```

This command will:
- Copy the environment file
- Start Docker containers
- Install backend dependencies
- Generate application key
- Run database migrations and seeders
- Install frontend dependencies

## Default Access

After installation, you'll have access to:

- Default user:
  - Email: test@example.com
  - Password: password
  - Role: user

## API Testing with Postman

1. Import the Postman collection:
   - Open Postman
   - Click "Import"
   - Select the file: `postman/moving-app.postman_collection.json`

2. Set up environment variables:
   - Create a new environment in Postman
   - Add variables:
     - `base_url`: `http://localhost:8080`
     - `token`: Leave empty (will be filled after login)

3. Authentication Flow:
   1. Use "Register" request to create a new account
   2. Use "Login" request with your credentials
   3. Copy the token from the response
   4. Set the token in your environment variables
   5. Now you can use protected endpoints

Available Endpoints:
- Authentication:
  - `POST /api/v1/register` - Register new user
  - `POST /api/v1/login` - Login
  - `GET /api/v1/user` - Get current user (protected)
  - `POST /api/v1/logout` - Logout (protected)
  - `GET /sanctum/csrf-cookie` - Get CSRF cookie

## Development

The application runs on:
- Frontend: http://localhost:3000
- Backend API: http://localhost:8080
- Database: MySQL (Port 3306)

## Available Make Commands

### Development Commands
```bash
# Start development environment
make up-dev

# Stop development environment
make down-dev

# Build development containers
make build-dev

# Rebuild development containers from scratch
make rebuild-dev

# Clear development cache
make clear-cache-dev
```

### Production Commands
```bash
# Start production environment
make up-prod

# Stop production environment
make down-prod

# Build production containers
make build-prod

# Rebuild production containers from scratch
make rebuild-prod

# Clear and optimize production cache
make clear-cache-prod
```

### Common Commands
```bash
# Restart all containers
make restart

# View logs
make logs

# Access container shells
make frontend  # Frontend container shell
make backend   # Backend container shell
make mysql     # MySQL shell
```

## User Roles

The application uses Spatie Laravel Permission for role management. Default roles:
- admin
- user

New users automatically get the 'user' role upon registration.
