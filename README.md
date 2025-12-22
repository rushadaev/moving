# MOOWEE - Moving Company Management System

> More than a moving company — we're your moving partner

Full-stack web application for managing moving company operations, including customer requests, packing materials, pricing calculations, and admin panel.

## 🚀 Tech Stack

### Frontend
- **Vue 3** (Composition API + TypeScript)
- **Naive UI** - Component library
- **Pinia** - State management
- **Vite** - Build tool
- **Google Maps API** - Address autocomplete and route calculation

### Backend
- **Laravel 11** - PHP framework
- **MySQL 8.0** - Database
- **Laravel Sanctum** - API authentication
- **Filament 3** - Admin panel
- **Spatie Laravel Permission** - Role management

### Infrastructure
- **Docker** - Containerization
- **Nginx** - Web server
- **Docker Compose** - Multi-container orchestration

---

## 📋 Key Features

### Customer Portal
- **Moving Request Form**
  - Property type selection (residential/commercial)
  - Multiple address support (loading/unloading/intermediate stops)
  - Google Maps autocomplete for addresses
  - Automatic distance calculation
  - Date/time picker for departure
  - Number of movers selection
  - Packing materials selection with quantities
  - Full Service Packing option (exclusive)

- **Packing Materials**
  - Individual materials: Boxes, Bubble Wrap, Packing Tape, etc.
  - Full Service Packing (blocks individual selection)
  - Quantity controls with +/- buttons
  - Icons for visual identification
  - Price calculation based on quantities

- **Pricing**
  - Fixed hourly rate from settings
  - Travel cost calculation (distance-based)
  - Packing materials cost
  - Total estimate

### Admin Panel (`/admin`)
- **Dashboard** - Overview and statistics
- **Requests Management** - View and manage moving requests
- **Users Management** - User accounts and roles
- **Landing Page Settings**
  - Company information
  - Contact details
  - Hourly rate configuration
- **Packing Materials Catalog**
  - CRUD operations for materials
  - Price management
  - Active/inactive toggle
  - Display order

---

## 🛠️ Quick Start

### Prerequisites
- Docker Desktop installed
- Ports available: 80 (nginx), 3000 (frontend), 9000 (backend), 3306 (mysql)

### 1. Clone & Navigate
```bash
cd /path/to/moving
```

### 2. Start Docker Containers
```bash
# Development environment
docker-compose -f docker-compose.dev.yml up -d

# Check containers status
docker-compose -f docker-compose.dev.yml ps
```

### 3. Backend Setup (First Time Only)

```bash
# Run migrations
docker-compose -f docker-compose.dev.yml exec backend php artisan migrate

# Seed database with initial data
docker-compose -f docker-compose.dev.yml exec backend php artisan db:seed

# Create admin user (via Filament)
docker-compose -f docker-compose.dev.yml exec backend php artisan make:filament-user
```

### 4. Access the Application

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:80/api
- **Admin Panel**: http://localhost:80/admin

---

## 📁 Project Structure

```
moving/
├── backend/                    # Laravel backend
│   ├── app/
│   │   ├── Filament/          # Admin panel resources
│   │   │   └── Resources/
│   │   │       ├── LandingPageSettingsResource.php
│   │   │       ├── PackingMaterialResource.php
│   │   │       └── RequestResource.php
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   │       └── Api/V1/
│   │   │           ├── AuthController.php
│   │   │           ├── RequestController.php
│   │   │           └── PackingMaterialController.php
│   │   └── Models/
│   │       ├── User.php
│   │       ├── Request.php
│   │       ├── Address.php
│   │       ├── Material.php
│   │       ├── PackingMaterial.php
│   │       └── LandingPageSettings.php
│   ├── database/
│   │   ├── migrations/        # Database schema
│   │   └── seeders/           # Initial data
│   └── routes/
│       └── api.php            # API routes
│
├── frontend/                   # Vue 3 frontend
│   ├── src/
│   │   ├── components/
│   │   │   ├── forms/
│   │   │   │   └── RequestForm.vue        # Main request form
│   │   │   ├── modals/
│   │   │   │   └── CreateRequestModal.vue
│   │   │   └── ui/
│   │   │       ├── MaterialsList.vue      # Display materials
│   │   │       └── GradientButton.vue
│   │   ├── stores/
│   │   │   ├── requests.ts               # Requests state
│   │   │   └── auth.ts                   # Auth state
│   │   ├── services/
│   │   │   ├── api.service.ts            # API client
│   │   │   ├── geocoding.service.ts      # Google Maps
│   │   │   └── routes.service.ts         # Distance calc
│   │   └── views/
│   │       ├── HomeView.vue
│   │       └── AdminView.vue
│   └── public/
│       └── Icons/                        # Material icons
│           ├── Box Moowee.svg
│           ├── Box S.svg
│           ├── Bubble Wrap.svg
│           ├── Plastic tape.svg
│           ├── +.svg
│           └── -.svg
│
└── docker-compose.dev.yml                # Docker configuration
```

---

## 🗄️ Database Schema

### Main Tables

**requests**
- Property details (type, square_feet, bedrooms)
- Movers count, hourly rate
- Status (pending/confirmed/active/break/completed/cancelled)
- Request number (unique identifier)
- Departure time, price
- User relationship

**addresses**
- Multiple addresses per request
- Type: loading/unloading/intermediate
- Location type: apartment/storage/house/office/garage
- Latitude, longitude (for distance calculation)
- Order (sequence of stops)

**materials**
- Selected materials for each request
- Quantity, price (snapshot at time of request)
- Link to packing_material_id (catalog reference)

**packing_materials** (Catalog)
- Master data for available materials
- name, display_name, price, icon
- is_active, is_full_service flags
- display order

**landing_page_settings**
- Company information
- Contact details
- hourly_rate (base rate)
- Social media links

**users**
- Authentication
- Roles (via Spatie Permission)

---

## 🔌 API Endpoints

### Public Routes
```
POST   /api/v1/login           # User login
POST   /api/v1/register        # User registration
GET    /api/v1/packing-materials  # Get materials catalog
GET    /api/landing/settings   # Get company settings
```

### Protected Routes (require Bearer token)
```
GET    /api/v1/user            # Get current user
POST   /api/v1/logout          # Logout

# Requests
GET    /api/v1/requests        # List all requests (paginated)
GET    /api/v1/requests/user   # Get user's requests
POST   /api/v1/requests        # Create new request
GET    /api/v1/requests/{id}   # Get single request
PUT    /api/v1/requests/{id}   # Update request
DELETE /api/v1/requests/{id}   # Delete request
```

### Request Payload Example
```json
{
  "property_type": "residential",
  "square_feet": 1200,
  "bedrooms": 3,
  "movers_count": 3,
  "hourly_rate": 100,
  "departure_time": "2025-12-25 10:00:00",
  "addresses": [
    {
      "address": "123 Main St, New York, NY",
      "type": "loading",
      "order": 0,
      "latitude": 40.7128,
      "longitude": -74.0060
    },
    {
      "address": "456 Oak Ave, Brooklyn, NY",
      "type": "unloading",
      "order": 1,
      "latitude": 40.6782,
      "longitude": -73.9442
    }
  ],
  "materials": [
    {
      "name": "boxes",
      "quantity": 10,
      "price": 5,
      "packing_material_id": 4
    },
    {
      "name": "bubble_wrap",
      "quantity": 3,
      "price": 10,
      "packing_material_id": 9
    }
  ]
}
```

---

## 🔧 Development Commands

### Backend (Laravel)

```bash
# Access backend container
docker-compose -f docker-compose.dev.yml exec backend bash

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Run specific seeder
php artisan db:seed --class=PackingMaterialSeeder

# Run all seeders
php artisan db:seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Tinker (Laravel console)
php artisan tinker

# Create Filament admin user
php artisan make:filament-user
```

### Frontend (Vue)

```bash
# Access frontend container
docker-compose -f docker-compose.dev.yml exec frontend sh

# Install dependencies
npm install

# Run dev server (already running in container)
npm run dev

# Build for production
npm run build

# Type check
npm run type-check
```

### Database

```bash
# Access MySQL
docker-compose -f docker-compose.dev.yml exec mysql mysql -u moving_user -p
# Password: moving_password

# Backup database
docker-compose -f docker-compose.dev.yml exec mysql mysqldump -u moving_user -p moving_db > backup.sql

# Restore database
docker-compose -f docker-compose.dev.yml exec -T mysql mysql -u moving_user -p moving_db < backup.sql
```

---

## 🧪 Testing API with cURL

### Login and Get Token
```bash
curl -s -X POST http://localhost:80/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com", "password": "password"}'

# Response: {"token": "1|xxxxx", "user": {...}}
```

### Create Request
```bash
TOKEN="your_token_here"

curl -X POST http://localhost:80/api/v1/requests \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ${TOKEN}" \
  -d @- <<'EOF'
{
  "property_type": "residential",
  "square_feet": 1000,
  "movers_count": 2,
  "hourly_rate": 100,
  "departure_time": "2025-12-25 10:00:00",
  "addresses": [
    {
      "address": "123 Test St, NYC",
      "type": "loading",
      "order": 0,
      "latitude": 40.7128,
      "longitude": -74.0060
    },
    {
      "address": "456 Test Ave, NYC",
      "type": "unloading",
      "order": 1,
      "latitude": 40.7580,
      "longitude": -73.9855
    }
  ],
  "materials": [
    {
      "name": "bubble_wrap",
      "quantity": 2,
      "price": 10,
      "packing_material_id": 9
    }
  ]
}
EOF
```

### Get Packing Materials
```bash
curl -s http://localhost:80/api/v1/packing-materials | jq .
```

### Get Landing Page Settings
```bash
curl -s http://localhost:80/api/landing/settings | jq .
```

---

## 🎨 Frontend Key Components

### RequestForm.vue
Main form for creating/editing moving requests.

**Location:** `frontend/src/components/forms/RequestForm.vue`

**Key Features:**
- Property type tabs (residential/commercial)
- Square footage and bedrooms inputs
- Google Maps address autocomplete
- Movers count and hourly rate
- Packing materials with quantity controls
- Full Service Packing checkbox (exclusive)

**Important State:**
```typescript
const packingQuantities = ref({
  full_service: 0,
  boxes: 0,
  bubble_wrap: 0,
  packing_tape: 0
})

const isFullService = ref(false)
```

**Key Functions:**
- `toggleFullService()` - Handles Full Service checkbox
- `increasePackingQuantity(type)` - Increment material quantity
- `decreasePackingQuantity(type)` - Decrement material quantity
- `updatePackingOptions()` - Sync quantities to materials array

### MaterialsList.vue
Display component for showing selected materials with icons and quantities.

**Location:** `frontend/src/components/ui/MaterialsList.vue`

**Usage:**
```vue
<MaterialsList :materials="request.materials" />
```

### CreateRequestModal.vue
Modal for creating new requests with validation and API submission.

**Location:** `frontend/src/components/modals/CreateRequestModal.vue`

**Features:**
- Request form wrapper
- Distance calculation
- Price calculation
- API submission
- Error handling

---

## 💡 Key Implementation Details

### Packing Materials Logic

1. **Full Service Packing** (checkbox with box icon)
   - When checked: blocks all other materials
   - Sets quantity to 1 automatically
   - Price: $200 (configured in admin panel)
   - Uses `Box Moowee.svg` icon

2. **Individual Materials** (quantity controls)
   - Boxes (Box S.svg), Bubble Wrap, Packing Tape
   - +/- buttons with icon images
   - Icons from `/Icons/` folder
   - Custom prices per material (managed in admin panel)

**Code Reference:** `RequestForm.vue:547-561`

### Price Snapshots
Materials store price at time of request creation to maintain historical pricing integrity. The `packing_material_id` links to catalog for reference, but price is stored separately.

**Why?** If admin changes material prices, old requests maintain original pricing.

### Address Geocoding Flow
1. User types address → Google Places Autocomplete
2. User selects → Extract lat/lng from geometry
3. Calculate distance using Google Directions API
4. Convert meters to miles (1 mile = 1609.34 meters)
5. Calculate travel cost (configurable, default: $2 per mile)

**Code Reference:** `CreateRequestModal.vue:141-185`

### Hourly Rate
- Stored in `landing_page_settings` table
- Fetched on form load
- Displayed as read-only (no +/- buttons)
- No auto-calculation based on movers
- Updated via admin panel

---

## 🚨 Common Issues & Solutions

### Port Conflicts
```bash
# Check what's using port 80
lsof -i :80

# Stop containers and restart
docker-compose -f docker-compose.dev.yml down
docker-compose -f docker-compose.dev.yml up -d
```

### Frontend Not Updating
```bash
# Clear browser cache
# Hard refresh: Cmd+Shift+R (Mac) or Ctrl+F5 (Windows)

# Rebuild frontend container
docker-compose -f docker-compose.dev.yml restart frontend
```

### Database Migrations Failed
```bash
# Reset database (WARNING: deletes all data)
docker-compose -f docker-compose.dev.yml exec backend php artisan migrate:fresh --seed

# Or run specific migration
docker-compose -f docker-compose.dev.yml exec backend php artisan migrate --path=database/migrations/2025_12_21_000003_add_price_to_materials.php

# Or run SQL manually
docker-compose -f docker-compose.dev.yml exec mysql mysql -u moving_user -p moving_db < backend/add_price_column.sql
```

### Authentication Issues
```bash
# Clear Laravel cache
docker-compose -f docker-compose.dev.yml exec backend php artisan cache:clear
docker-compose -f docker-compose.dev.yml exec backend php artisan config:clear

# Check token in request headers
# Authorization: Bearer <token>
```

### Checkbox Not Showing Checked State
**Issue:** Checkbox clicks but no visual feedback

**Solution:** The `v-model` handles the state automatically. Remove manual `isFullService.value = !isFullService.value` from `toggleFullService()` function.

**Fixed in:** `RequestForm.vue:547` (removed double toggle)

---

## 📝 Environment Variables

### Backend (.env)
```env
APP_NAME=MOOWEE
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=moving_db
DB_USERNAME=moving_user
DB_PASSWORD=moving_password

GOOGLE_MAPS_API_KEY=your_api_key_here
```

### Frontend (.env)
```env
VITE_API_URL=http://localhost:80
VITE_GOOGLE_MAPS_API_KEY=your_api_key_here
```

---

## 🎯 Recent Changes

### December 21, 2025

**✅ Task 7: Mobile Responsiveness**
- Number of Movers field now full-width on mobile devices (<640px)
- Updated CSS: `.form-group.full-width-mobile`

**✅ Task 8: Packing Materials System**
- Individual materials with quantity controls (+/- buttons)
- Icons: Boxes (Box S.svg), Bubble Wrap, Packing Tape
- Full Service Packing with checkbox (Box Moowee.svg)
- Exclusive selection: Full Service blocks other materials
- Materials stored in database with quantities and prices
- MaterialsList component for displaying selected materials

**✅ Task 9: Fixed Hourly Rate**
- Removed auto-calculation (movers_count × $50)
- Added `hourly_rate` field to `landing_page_settings` table
- Display as read-only in form
- Managed via admin panel

**Database Migrations:**
- `2025_12_21_000001_add_hourly_rate_to_landing_page_settings.php`
- `2025_12_21_000002_create_packing_materials_table.php`
- `2025_12_21_000003_add_price_to_materials.php`

**New Seeders:**
- `PackingMaterialSeeder` - Creates 11 materials (1 Full Service + 10 individual)

**Bug Fixes:**
- Fixed checkbox double-toggle issue (visual state now shows correctly)
- Fixed SQL error: Column 'price' not found (migration executed)
- Background color changed from #1e293b to #303134

---

## 👨‍💻 Development Workflow

### Adding New Migration
```bash
docker-compose -f docker-compose.dev.yml exec backend php artisan make:migration create_table_name
# Edit migration file in backend/database/migrations/
docker-compose -f docker-compose.dev.yml exec backend php artisan migrate
```

### Adding New API Endpoint
1. Create controller method in `backend/app/Http/Controllers/Api/V1/`
2. Add route in `backend/routes/api.php`
3. Add service method in `frontend/src/services/api.service.ts`
4. Use in component/store

### Adding New Filament Resource
```bash
docker-compose -f docker-compose.dev.yml exec backend php artisan make:filament-resource ModelName --generate
```

### Testing Backend Changes
```bash
# Always test with curl requests
curl -X POST http://localhost:80/api/v1/endpoint \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ${TOKEN}" \
  -d '{"data": "value"}'
```

---

## 📞 Contact Information

**Company:** MOOWEE
**Tagline:** More than a moving company — we're your moving partner

**Contact:**
- Email: mooweemoving@gmail.com
- Phone: +1 (310) 753-42-48

**Business:**
- Residential moving
- Commercial moving
- Local moving services
- Packing services

---

## 📄 License

Proprietary - All rights reserved

---

**Last Updated:** December 21, 2025
**Version:** 1.0.0
