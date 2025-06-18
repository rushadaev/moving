# Moving App

A full-stack application for managing moving requests with payment integration.

## Prerequisites

- Docker and Docker Compose
- Node.js (for local development)
- PHP 8.2 (for local development)
- Composer (for local development)

## Setup

1. Clone the repository:
```bash
git clone https://github.com/rushadaev/moving.git
cd moving
```

2. Copy the environment file:
```bash
cp .env.example .env
```

3. Start the development environment:
```bash
make up-dev
```

4. Install dependencies and run migrations:
```bash
make install
```

## Default Access

- Frontend: http://localhost:3000
- Backend API: http://localhost:8080
- Database: MySQL (localhost:3306)
  - Database: laravel
  - Username: laravel
  - Password: secret

## Using Postman Collection

1. Import the collection from `postman/moving-app.postman_collection.json`
2. Set up environment variables:
   - `base_url`: http://localhost:8080
   - `token`: Your authentication token (received after login)

### Available Endpoints

#### Authentication
- POST `/api/v1/register` - Register a new user
- POST `/api/v1/login` - Login and get authentication token
- GET `/api/v1/user` - Get current user details
- POST `/api/v1/logout` - Logout and invalidate token

#### Requests
- GET `/api/v1/requests` - List all requests
- POST `/api/v1/requests` - Create a new request
- GET `/api/v1/requests/{id}` - Get request details
- PUT `/api/v1/requests/{id}` - Update a request
- DELETE `/api/v1/requests/{id}` - Delete a request
- GET `/api/v1/requests/user` - List user's requests

#### Payments
- POST `/api/v1/payments/create-intent` - Create a payment intent with redirect URLs
- POST `/api/v1/payments/confirm` - Confirm a payment
- GET `/api/v1/payments/status/{paymentIntentId}` - Check payment status

## Development Environment

The application uses Docker for development. The following services are available:

- Frontend (Vite + React)
- Backend (Laravel)
- MySQL Database
- Nginx

## Available Make Commands

### Development Commands
- `make up-dev` - Start development environment
- `make down-dev` - Stop development environment
- `make build-dev` - Build development containers
- `make rebuild-dev` - Rebuild development containers
- `make clear-cache-dev` - Clear Laravel cache in development

### Production Commands
- `make up-prod` - Start production environment
- `make down-prod` - Stop production environment
- `make build-prod` - Build production containers
- `make rebuild-prod` - Rebuild production containers
- `make clear-cache-prod` - Clear Laravel cache in production

### Common Commands
- `make restart` - Restart all containers
- `make logs` - View container logs
- `make frontend` - Access frontend container shell
- `make backend` - Access backend container shell
- `make mysql` - Access MySQL container shell

## Payment Integration

The application uses Stripe for payment processing. To set up payments:

1. Add your Stripe API keys to `.env`:
```
STRIPE_KEY=your_publishable_key
STRIPE_SECRET=your_secret_key
STRIPE_WEBHOOK_SECRET=your_webhook_secret
```

2. The payment flow:
   - Create a payment intent with amount, request ID, and redirect URLs
   - User is redirected to Stripe Checkout page
   - After payment, user is redirected back to your success/cancel URL
   - Confirm the payment on the backend
   - Check payment status as needed

3. Supported payment methods:
   - Credit/Debit Cards
   - Apple Pay
   - Google Pay

## Role Management

The application uses Spatie's Laravel Permission package for role management. Default roles are created during seeding:

- Admin
- User

Roles are automatically assigned during user registration.
