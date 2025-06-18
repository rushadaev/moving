# Frontend-Backend Connection Documentation

## Overview

This document describes the connections between the Vue.js frontend and Laravel backend in the Moving App. The communication is done via a RESTful API as defined in the Postman collection.

## Architecture

The application follows a standard client-server architecture:

- **Frontend**: Vue.js 3 application running at port 5173 in development mode
- **Backend**: Laravel API running at port 9000
- **Nginx**: Proxy server routing requests between frontend and backend
- **Database**: MySQL database for persistent storage

## Environment Configuration

Frontend environment variables (defined in `.env`):
- `VITE_API_URL`: Points to the backend API URL (e.g., '/api')
- `VITE_APP_NAME`: Application name

## Authentication Flow

1. **Registration**:
   - Frontend: `authStore.register()` in `src/stores/auth.ts`
   - Backend: `POST /api/v1/register`
   - Data flow: User credentials → Backend validates → Returns authentication token
   
2. **Login**:
   - Frontend: `authStore.login()` in `src/stores/auth.ts`
   - Backend: `POST /api/v1/login`
   - Data flow: Email/password → Backend validates → Returns authentication token

3. **Get Current User**:
   - Frontend: `authStore.getUser()` in `src/stores/auth.ts`
   - Backend: `GET /api/v1/user`
   - Data flow: Auth token in header → Backend validates token → Returns user data

4. **Logout**:
   - Frontend: `authStore.logout()` in `src/stores/auth.ts`
   - Backend: `POST /api/v1/logout`
   - Data flow: Auth token in header → Backend invalidates token

5. **Authentication UI Implementation**:
   - Frontend: `src/views/AuthView.vue` contains the login/registration forms
   - Authentication state is managed in `useAuthStore` with Pinia
   - Token is stored in localStorage for persistence between sessions
   - Protected routes use navigation guards in `router/index.ts` to redirect unauthenticated users

## Request Management

1. **Create Request**:
   - Frontend: `requestsStore.createRequest()` in `src/stores/requests.ts`
   - Backend: `POST /api/v1/requests`
   - Data flow: Request details with addresses and materials → Backend creates and stores
   
2. **List User Requests**:
   - Frontend: `requestsStore.fetchRequests()` in `src/stores/requests.ts`
   - Backend: `GET /api/v1/requests/user`
   - Data flow: Auth token in header → Backend returns user's requests

3. **Get Request Details**:
   - Frontend: `requestsStore.getRequestById()` in `src/stores/requests.ts`
   - Backend: `GET /api/v1/requests/:id`
   - Data flow: Request ID in URL → Backend returns complete request data

4. **Update Request**:
   - Frontend: `requestsStore.updateRequest()` in `src/stores/requests.ts`
   - Backend: `PUT /api/v1/requests/:id`
   - Data flow: Updated request data → Backend validates and updates

5. **Update Request Status**:
   - Frontend: `requestsStore.updateRequestStatus()` in `src/stores/requests.ts`
   - Backend: `PUT /api/v1/requests/:id` with status field
   - Data flow: New status → Backend updates request status

6. **Update Movers Count**:
   - Frontend: `requestsStore.updateMoversCount()` in `src/stores/requests.ts`
   - Backend: `PUT /api/v1/requests/:id` with movers_count field
   - Data flow: New movers count → Backend updates request

7. **Delete Request**:
   - Frontend: `requestsStore.deleteRequest()` in `src/stores/requests.ts`
   - Backend: `DELETE /api/v1/requests/:id`
   - Data flow: Request ID → Backend deletes request and related data

## View Components and API Integration

1. **AuthView.vue**:
   - Provides user registration and login forms
   - Integrates with `POST /api/v1/register` and `POST /api/v1/login`
   - Handles form validation and error display
   - Redirects to home page after successful authentication

2. **RequestsView.vue**:
   - Uses `requestsStore.fetchRequests()` to display user's requests
   - Shows authentication state-aware UI (login prompt or requests list)
   - Integrates with `GET /api/v1/requests/user`

3. **DetailsView.vue**:
   - Uses `requestsStore.getRequestById()` to display request details
   - Uses `requestsStore.createRequest()` and `requestsStore.updateRequest()` to edit requests
   - Includes form for creating/editing with address geocoding
   - Protected by authentication guard in router

4. **TrackingView.vue**:
   - Uses `requestsStore.updateRequestStatus()` to track request progress
   - Uses `requestsStore.updateMoversCount()` to update movers count
   - Uses `requestsStore.updateRequest()` to update materials used
   - Protected by authentication guard in router

## Header Component

The `Header.vue` component integrates with the authentication system:
- Shows Login/Register button when user is not authenticated
- Shows Logout button when user is authenticated
- Provides navigation controls for the application

## Data Models

1. **Request**:
   - Property type, square feet, movers count, departure time, etc.
   - Contains relations to addresses and materials
   
2. **Address**:
   - Loading, intermediate, and unloading addresses with coordinates
   - Belongs to a request

3. **Material**:
   - Materials used in a move with quantities
   - Belongs to a request

## Error Handling

All API requests include error handling that:
1. Displays user-friendly error messages
2. Logs detailed errors to the console
3. Handles authentication errors by logging out the user if token is invalid

## Docker Integration

The application uses Docker Compose for local development:

- Frontend container: Built from `frontend/Dockerfile`
- Backend container: Built from `backend/Dockerfile`
- Nginx container: Routes requests between frontend and backend
- MySQL container: Hosts the database

## API Security

All authenticated API requests include:
- `Authorization: Bearer {token}` header
- `Accept: application/json` header to receive JSON responses 