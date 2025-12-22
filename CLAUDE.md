# Claude Code Development Guide - MOOWEE Project

## Project Context

This is a full-stack moving company management system built with Vue 3 + Laravel 11. The application handles customer requests, packing materials, pricing, and admin management through Filament.

**Key Business Logic:**
- Customers create moving requests with multiple addresses
- Google Maps calculates distance and travel costs
- Packing materials have quantity controls with price snapshots
- Full Service Packing is exclusive (blocks other materials)
- Hourly rate is fixed from settings (not calculated)

---

## Architecture Overview

### Tech Stack
- **Frontend**: Vue 3 (Composition API) + TypeScript + Naive UI + Vite
- **Backend**: Laravel 11 + MySQL 8.0 + Sanctum + Filament 3
- **Infrastructure**: Docker Compose (dev environment)

### Key Directories
```
backend/
├── app/Models/              # Eloquent models
├── app/Http/Controllers/Api/V1/  # API controllers
├── app/Filament/Resources/  # Admin panel resources
├── database/migrations/     # Schema migrations
└── database/seeders/        # Data seeders

frontend/
├── src/components/forms/    # Form components (RequestForm)
├── src/components/modals/   # Modal components
├── src/components/ui/       # Reusable UI components
├── src/stores/             # Pinia state management
├── src/services/           # API services
└── public/Icons/           # SVG icons for materials
```

---

## Development Environment

### Starting Containers
```bash
docker-compose -f docker-compose.dev.yml up -d
docker-compose -f docker-compose.dev.yml ps
```

### Container Access
```bash
# Backend (Laravel)
docker-compose -f docker-compose.dev.yml exec backend bash

# Frontend (Vue)
docker-compose -f docker-compose.dev.yml exec frontend sh

# MySQL
docker-compose -f docker-compose.dev.yml exec mysql mysql -u moving_user -p
# Password: moving_password
```

---

## Common Development Tasks

### 1. Adding a Database Migration

**Always check if table/column exists first:**
```bash
docker-compose -f docker-compose.dev.yml exec backend php artisan tinker
>>> Schema::hasTable('table_name')
>>> Schema::hasColumn('table_name', 'column_name')
```

**Create migration:**
```bash
docker-compose -f docker-compose.dev.yml exec backend php artisan make:migration add_column_to_table
```

**Edit migration file** in `backend/database/migrations/`

**Run migration:**
```bash
docker-compose -f docker-compose.dev.yml exec backend php artisan migrate
```

**If migration fails, check for:**
- Foreign key constraints
- Existing data that violates new constraints
- Duplicate column names

### 2. Adding a New API Endpoint

**Step 1:** Add method to controller (`backend/app/Http/Controllers/Api/V1/`)
```php
public function methodName(Request $request)
{
    $validated = $request->validate([
        'field' => 'required|string',
    ]);

    // Logic here

    return response()->json($data);
}
```

**Step 2:** Add route (`backend/routes/api.php`)
```php
// Public
Route::get('/endpoint', [Controller::class, 'method']);

// Protected (requires auth)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/endpoint', [Controller::class, 'method']);
});
```

**Step 3:** Test with cURL
```bash
curl -X POST http://localhost:80/api/v1/endpoint \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ${TOKEN}" \
  -d '{"field": "value"}'
```

**Step 4:** Add frontend service method (`frontend/src/services/api.service.ts`)
```typescript
async methodName(data: any) {
  const response = await this.client.post('/endpoint', data)
  return response.data
}
```

### 3. Adding a Vue Component

**Location depends on purpose:**
- Forms: `frontend/src/components/forms/`
- Modals: `frontend/src/components/modals/`
- UI elements: `frontend/src/components/ui/`

**Template structure:**
```vue
<template>
  <div class="component-name">
    <!-- Content -->
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'

// Props
const props = defineProps<{
  modelValue: any
}>()

// Emits
const emit = defineEmits<{
  'update:modelValue': [value: any]
}>()

// State
const localValue = ref(props.modelValue)

// Watchers
watch(() => props.modelValue, (newVal) => {
  localValue.value = newVal
})
</script>

<style scoped>
.component-name {
  /* Styles */
}
</style>
```

### 4. Adding a Filament Resource

**Generate resource:**
```bash
docker-compose -f docker-compose.dev.yml exec backend php artisan make:filament-resource ModelName --generate
```

**Edit generated files** in `backend/app/Filament/Resources/ModelNameResource.php`

**Key methods:**
- `form()` - Form fields
- `table()` - Table columns and filters
- `getRelations()` - Related records
- `getPages()` - Resource pages

### 5. Testing Changes

**Always test backend changes with cURL:**
```bash
# Login first
TOKEN=$(curl -s -X POST http://localhost:80/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}' \
  | jq -r '.token')

# Test endpoint
curl -X POST http://localhost:80/api/v1/endpoint \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ${TOKEN}" \
  -d '{"data": "value"}'
```

**Frontend testing:**
- Use browser DevTools Network tab
- Check console for Vue errors
- Verify Pinia store state with Vue DevTools

---

## Critical Code Patterns

### 1. Materials with Price Snapshots

**Why:** Preserves historical pricing when admin changes prices

**Backend validation:**
```php
'materials' => 'nullable|array',
'materials.*.name' => 'required|string',
'materials.*.quantity' => 'required|integer|min:1',
'materials.*.price' => 'required|numeric|min:0',  // Snapshot
'materials.*.packing_material_id' => 'nullable|exists:packing_materials,id',  // Reference
```

**Frontend submission:**
```typescript
materials: [
  {
    name: 'boxes',
    quantity: 5,
    price: 5,  // Current price from catalog
    packing_material_id: 4  // Link to catalog
  }
]
```

### 2. Full Service Packing Exclusivity

**Frontend logic** (`RequestForm.vue:547-561`):
```typescript
const toggleFullService = () => {
  // v-model already toggles, just react to state
  if (isFullService.value) {
    packingQuantities.value.full_service = 1
    // Clear all other materials
    packingQuantities.value.boxes = 0
    packingQuantities.value.bubble_wrap = 0
    packingQuantities.value.packing_tape = 0
  } else {
    packingQuantities.value.full_service = 0
  }

  updatePackingOptions()
}
```

**Template:**
```vue
<!-- Full Service blocks others -->
<div class="packing-option-card" :class="{ 'disabled': isFullService }">
  <button :disabled="isFullService" @click="increasePackingQuantity('boxes')">
    <!-- Button content -->
  </button>
</div>
```

### 3. Google Maps Geocoding

**Service** (`frontend/src/services/geocoding.service.ts`):
```typescript
async geocodeAddress(address: string) {
  const geocoder = new google.maps.Geocoder()
  const result = await geocoder.geocode({ address })

  return {
    latitude: result.results[0].geometry.location.lat(),
    longitude: result.results[0].geometry.location.lng(),
    formattedAddress: result.results[0].formatted_address
  }
}
```

**Distance calculation** (`frontend/src/services/routes.service.ts`):
```typescript
async calculateRoute(origin: LatLng, destination: LatLng) {
  const directionsService = new google.maps.DirectionsService()
  const result = await directionsService.route({
    origin,
    destination,
    travelMode: google.maps.TravelMode.DRIVING
  })

  return {
    distance: result.routes[0].legs[0].distance.value,  // meters
    duration: result.routes[0].legs[0].duration.value   // seconds
  }
}
```

**Convert to miles:**
```typescript
const distanceMiles = Math.round((distanceMeters / 1609.34) * 10) / 10
```

### 4. Pinia Store Pattern

**Define store** (`frontend/src/stores/storeName.ts`):
```typescript
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useStoreNameStore = defineStore('storeName', () => {
  // State
  const items = ref<Item[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  // Actions
  const fetchItems = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await apiService.getItems()
      items.value = response.data
    } catch (e) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  return {
    items,
    loading,
    error,
    fetchItems
  }
})
```

**Usage in component:**
```vue
<script setup lang="ts">
import { useStoreNameStore } from '@/stores/storeName'

const store = useStoreNameStore()

onMounted(() => {
  store.fetchItems()
})
</script>
```

---

## Important Files Reference

### Backend

**Models:**
- `backend/app/Models/Request.php` - Moving request (hasMany addresses, materials)
- `backend/app/Models/Material.php` - Selected materials per request (belongsTo packingMaterial)
- `backend/app/Models/PackingMaterial.php` - Materials catalog (hasMany materials)
- `backend/app/Models/LandingPageSettings.php` - Company settings including hourly_rate

**Controllers:**
- `backend/app/Http/Controllers/Api/V1/RequestController.php` - CRUD for requests
- `backend/app/Http/Controllers/Api/V1/PackingMaterialController.php` - Get materials catalog
- `backend/app/Http/Controllers/Api/LandingPageController.php` - Public settings

**Migrations:**
- `2025_12_21_000001_add_hourly_rate_to_landing_page_settings.php`
- `2025_12_21_000002_create_packing_materials_table.php`
- `2025_12_21_000003_add_price_to_materials.php`

**Seeders:**
- `backend/database/seeders/PackingMaterialSeeder.php` - 11 materials (1 Full Service + 10 individual)
- `backend/database/seeders/LandingPageSeeder.php` - Company settings with hourly_rate

### Frontend

**Key Components:**
- `frontend/src/components/forms/RequestForm.vue` - Main request form (700+ lines)
  - Property type, addresses, movers, packing materials
  - Google Maps autocomplete integration
  - Packing quantities with +/- controls
  - Full Service checkbox with exclusive logic

- `frontend/src/components/modals/CreateRequestModal.vue` - Request creation wrapper
  - Geocoding addresses
  - Distance calculation
  - Price calculation (hourly + travel + materials)
  - API submission

- `frontend/src/components/ui/MaterialsList.vue` - Display selected materials
  - Icons from `/Icons/` folder
  - Quantity badges
  - Material name formatting

**Stores:**
- `frontend/src/stores/requests.ts` - Requests state (list, create, update, delete)
- `frontend/src/stores/auth.ts` - Authentication state (login, logout, user)

**Services:**
- `frontend/src/services/api.service.ts` - Axios HTTP client
- `frontend/src/services/geocoding.service.ts` - Google Maps Geocoding API
- `frontend/src/services/routes.service.ts` - Google Maps Directions API

---

## Database Schema Quick Reference

### requests
```sql
id, request_number, property_type, square_feet, bedrooms,
additional_objects (json), movers_count, hourly_rate,
departure_time, labor_included, package_type, price,
status (pending/confirmed/active/break/completed/cancelled),
user_id, operator_id, created_at, updated_at, deleted_at
```

### addresses
```sql
id, request_id, address, latitude, longitude,
order, type (loading/unloading/intermediate),
location_type (apartment/storage/house/office/garage),
created_at, updated_at
```

### materials
```sql
id, request_id, packing_material_id, name,
quantity, price,
created_at, updated_at
```

### packing_materials (Catalog)
```sql
id, name, display_name, price, icon,
description, is_active, is_full_service, order,
created_at, updated_at
```

### landing_page_settings
```sql
id, logo, company_name, tagline, description,
photo_title, photo_url, video_title, video_url,
phone, email, hourly_rate,
instagram_url, facebook_url, youtube_url,
created_at, updated_at
```

---

## Coding Conventions

### Backend (Laravel)

**Model relationships:**
```php
// Request.php
public function addresses(): HasMany
{
    return $this->hasMany(Address::class);
}

public function materials(): HasMany
{
    return $this->hasMany(Material::class);
}
```

**Controller validation:**
```php
$validated = $request->validate([
    'field' => 'required|string|max:255',
    'nested.*.field' => 'required|integer|min:0',
]);
```

**Response format:**
```php
// Success
return response()->json($data, 200);

// Created
return response()->json($data, 201);

// Error
return response()->json(['message' => 'Error'], 422);
```

### Frontend (Vue 3)

**Component naming:** PascalCase for components, camelCase for variables

**Props and emits:**
```typescript
const props = defineProps<{
  modelValue: Type
}>()

const emit = defineEmits<{
  'update:modelValue': [value: Type]
}>()
```

**Reactive state:**
```typescript
// Use ref for primitives
const count = ref(0)

// Use ref for objects too (Vue 3)
const user = ref<User | null>(null)

// Use computed for derived state
const fullName = computed(() => `${user.value?.firstName} ${user.value?.lastName}`)
```

**API calls:**
```typescript
const loading = ref(false)
const error = ref<string | null>(null)

const fetchData = async () => {
  loading.value = true
  error.value = null
  try {
    const result = await apiService.getData()
    // Handle result
  } catch (e: any) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}
```

### CSS

**Use scoped styles:**
```vue
<style scoped>
/* Styles only apply to this component */
</style>
```

**Color scheme (dark theme):**
```css
--background: #303134
--border: #334155
--text: #e2e8f0
--text-muted: #9ca3af
--primary: #3b82f6
--primary-hover: rgba(59, 130, 246, 0.1)
```

**Responsive breakpoints:**
```css
@media (max-width: 640px) {
  /* Mobile styles */
}
```

---

## Common Pitfalls & Solutions

### 1. "Column not found" SQL Error

**Cause:** Migration not run on database

**Solution:**
```bash
docker-compose -f docker-compose.dev.yml exec backend php artisan migrate
```

**Check if migration ran:**
```bash
docker-compose -f docker-compose.dev.yml exec backend php artisan migrate:status
```

### 2. Checkbox Not Showing Checked State

**Cause:** Double-toggling state (v-model + manual toggle)

**Solution:** Remove manual toggle, let v-model handle it:
```typescript
// WRONG
const toggle = () => {
  isChecked.value = !isChecked.value  // v-model already does this!
  // ... rest of logic
}

// CORRECT
const toggle = () => {
  // v-model already toggled, just react to new state
  if (isChecked.value) {
    // Do something
  }
}
```

### 3. API Returns 401 Unauthenticated

**Cause:** Missing or invalid auth token

**Solution:**
```bash
# Get fresh token
TOKEN=$(curl -s -X POST http://localhost:80/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}' \
  | jq -r '.token')

# Use in requests
curl -H "Authorization: Bearer ${TOKEN}" ...
```

### 4. Frontend Not Reflecting Changes

**Solution:**
```bash
# Hard refresh browser: Cmd+Shift+R (Mac) or Ctrl+F5 (Windows)

# If still not working, restart frontend container
docker-compose -f docker-compose.dev.yml restart frontend
```

### 5. Materials Not Saving to Database

**Check:**
1. Validation includes `price` field
2. Model has `price` in `$fillable`
3. Migration added `price` column
4. Frontend sends `price` in payload

### 6. Google Maps Not Loading

**Check:**
1. `.env` has `VITE_GOOGLE_MAPS_API_KEY`
2. API key has Geocoding + Directions APIs enabled
3. Billing enabled on Google Cloud project

---

## Testing Checklist

### Before Committing Backend Changes

- [ ] Run migrations successfully
- [ ] Test API endpoint with cURL
- [ ] Check response format matches documentation
- [ ] Verify authentication works correctly
- [ ] Check database records created/updated correctly

### Before Committing Frontend Changes

- [ ] No TypeScript errors (`npm run type-check`)
- [ ] Component renders without console errors
- [ ] API calls work (check Network tab)
- [ ] State management works (check Vue DevTools)
- [ ] Mobile responsive (test at 640px width)

### End-to-End Testing

- [ ] Create new request with materials
- [ ] Update existing request
- [ ] Materials display correctly with quantities
- [ ] Full Service Packing blocks other materials
- [ ] Distance calculation works
- [ ] Admin panel shows data correctly

---

## Debugging Tips

### Backend Debugging

**Check logs:**
```bash
docker-compose -f docker-compose.dev.yml logs backend -f
```

**Use tinker for quick tests:**
```bash
docker-compose -f docker-compose.dev.yml exec backend php artisan tinker
>>> $request = App\Models\Request::first()
>>> $request->materials
>>> App\Models\PackingMaterial::where('is_full_service', true)->first()
```

**Database queries:**
```bash
docker-compose -f docker-compose.dev.yml exec mysql mysql -u moving_user -p
mysql> SELECT * FROM materials WHERE request_id = 1;
mysql> SELECT * FROM packing_materials;
```

### Frontend Debugging

**Console logging:**
```typescript
console.log('State:', toRaw(reactiveObject))  // Use toRaw for reactive objects
console.log('Props:', props)
console.log('Event:', event)
```

**Vue DevTools:**
- Check component state in Components tab
- Check Pinia stores in Pinia tab
- Check emitted events in Events tab

**Network debugging:**
- Open DevTools → Network tab
- Filter by XHR
- Check request payload and response

---

## Quick Command Reference

```bash
# Start environment
docker-compose -f docker-compose.dev.yml up -d

# Stop environment
docker-compose -f docker-compose.dev.yml down

# Restart service
docker-compose -f docker-compose.dev.yml restart backend

# View logs
docker-compose -f docker-compose.dev.yml logs -f backend

# Run migration
docker-compose -f docker-compose.dev.yml exec backend php artisan migrate

# Run seeder
docker-compose -f docker-compose.dev.yml exec backend php artisan db:seed --class=ClassName

# Clear cache
docker-compose -f docker-compose.dev.yml exec backend php artisan cache:clear

# Tinker console
docker-compose -f docker-compose.dev.yml exec backend php artisan tinker

# Access MySQL
docker-compose -f docker-compose.dev.yml exec mysql mysql -u moving_user -p

# Frontend shell
docker-compose -f docker-compose.dev.yml exec frontend sh
```

---

## Project-Specific Notes

### Material Icons Location
All material icons are in `frontend/public/Icons/`:
- Box Moowee.svg (Full Service)
- Box S.svg (Boxes)
- Bubble Wrap.svg
- Plastic tape.svg
- +.svg (increase quantity)
- -.svg (decrease quantity)

**Usage in template:**
```vue
<img src="/Icons/Box S.svg" alt="Boxes" />
```

### Hourly Rate
- **NOT** auto-calculated based on movers count
- Fixed value from `landing_page_settings.hourly_rate`
- Default: $100.00
- Updated via admin panel only
- Displayed as read-only in form (no +/- buttons)

### Full Service Packing
- `packing_material_id: 1`
- `is_full_service: true` in catalog
- `price: $200`
- When selected: blocks all other materials
- Uses checkbox UI (not quantity controls)

### Distance Calculation
- Google Maps Directions API
- Returns distance in meters
- Convert to miles: `meters / 1609.34`
- Round to 1 decimal: `Math.round(miles * 10) / 10`
- Travel cost: `miles × $2` (default, configurable)

---

## Contact & Resources

**Project:** MOOWEE Moving Company
**Email:** mooweemoving@gmail.com
**Phone:** +1 (310) 753-42-48

**Useful Links:**
- Laravel Docs: https://laravel.com/docs/11.x
- Vue 3 Docs: https://vuejs.org/guide/
- Naive UI: https://www.naiveui.com/
- Filament Docs: https://filamentphp.com/docs

---

**Last Updated:** December 21, 2025
