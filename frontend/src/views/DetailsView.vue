<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useRequestsStore } from '../stores/requests'
import { useAuthStore } from '../stores/auth'
import Card from '@/components/ui/Card.vue'
import GradientButton from '@/components/ui/GradientButton.vue'
import Header from '@/components/Header.vue'
import Footer from '@/components/Footer.vue'

const router = useRouter()
const requestsStore = useRequestsStore()
const authStore = useAuthStore()
const loading = ref(false)
const isEditing = ref(false)
const errorMessage = ref('')

// Form data for new/edited request
const formData = ref({
  property_type: '',
  square_feet: 0,
  additional_objects: [],
  movers_count: 2,
  hourly_rate: 100,
  departure_time: '',
  labor_included: true,
  package_type: 'standard',
  addresses: [
    {
      address: '',
      type: 'loading',
      order: 0,
      latitude: 0,
      longitude: 0
    },
    {
      address: '',
      type: 'unloading',
      order: 1,
      latitude: 0,
      longitude: 0
    }
  ],
  materials: []
})

// Format date and time for display
const formattedDate = computed(() => {
  if (!requestsStore.selectedRequest?.departure_time) return ''
  const date = new Date(requestsStore.selectedRequest.departure_time)
  return date.toLocaleDateString()
})

const formattedTime = computed(() => {
  if (!requestsStore.selectedRequest?.departure_time) return ''
  const date = new Date(requestsStore.selectedRequest.departure_time)
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
})

// Get loading and unloading addresses
const loadingAddress = computed(() => {
  return requestsStore.selectedRequest?.addresses.find(addr => addr.type === 'loading')?.address || ''
})

const unloadingAddress = computed(() => {
  return requestsStore.selectedRequest?.addresses.find(addr => addr.type === 'unloading')?.address || ''
})

onMounted(async () => {
  if (!authStore.isAuthenticated) {
    router.push('/')
    return
  }
  
  loading.value = true
  errorMessage.value = ''
  
  try {
    // Get request ID from either URL query parameter or store
    let requestId = Number(router.currentRoute.value.query.id)
    console.log('DetailsView mounted with query ID:', requestId)
    
    // If ID is not in URL but we have a request in store, use that ID
    if (!requestId && requestsStore.selectedRequest?.id) {
      requestId = requestsStore.selectedRequest.id
      console.log(`No ID in URL, using ID from store: ${requestId}`)
      
      // Update the URL to include the ID without reloading the page
      router.replace({
        path: '/details',
        query: { id: requestId.toString() }
      })
      
      isEditing.value = true
    }
    
    if (requestId) {
      // If the selected request doesn't match the ID in URL or we don't have a request loaded
      // then load the request data from API
      if (!requestsStore.selectedRequest || requestsStore.selectedRequest.id !== requestId) {
        console.log(`Loading request with ID: ${requestId}`)
        const requestData = await requestsStore.getRequestById(requestId)
        
        if (!requestData || !requestsStore.selectedRequest) {
          errorMessage.value = `Failed to load request #${requestId}. ${requestsStore.error || 'Unknown error'}`
          console.error('Failed to load request:', requestsStore.error)
          loading.value = false
          return
        }
      } else {
        console.log('Request already loaded in store:', requestsStore.selectedRequest)
      }
      
      console.log('Request data loaded:', requestsStore.selectedRequest)
      isEditing.value = true
      
      // Initialize form with existing request data
      const sr = requestsStore.selectedRequest
      formData.value = {
        property_type: sr.property_type || '',
        square_feet: sr.square_feet || 0,
        additional_objects: sr.additional_objects || [],
        movers_count: sr.movers_count || 2,
        hourly_rate: sr.hourly_rate || 100,
        departure_time: sr.departure_time || '',
        labor_included: sr.labor_included !== undefined ? sr.labor_included : true,
        package_type: sr.package_type || 'standard',
        addresses: sr.addresses && sr.addresses.length > 0 ? [...sr.addresses] : [
          {
            address: '',
            type: 'loading',
            order: 0,
            latitude: 0,
            longitude: 0
          },
          {
            address: '',
            type: 'unloading',
            order: 1,
            latitude: 0,
            longitude: 0
          }
        ],
        materials: sr.materials && sr.materials.length > 0 ? [...sr.materials] : []
      }
      
      console.log('Form data initialized:', formData.value)
    } else {
      console.log('Creating new request (no ID provided)')
      isEditing.value = false
    }
  } catch (err) {
    console.error('Error initializing details view:', err)
    errorMessage.value = 'An error occurred while loading request details'
  } finally {
    loading.value = false
  }
})

// Geocode an address to get coordinates
async function geocodeAddress(address) {
  try {
    const response = await fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(address)}&key=${import.meta.env.VITE_GOOGLE_MAPS_API_KEY}`)
    const data = await response.json()
    
    if (data.status === 'OK' && data.results.length > 0) {
      const location = data.results[0].geometry.location
      return {
        latitude: location.lat,
        longitude: location.lng
      }
    }
    return null
  } catch (error) {
    console.error('Geocoding error:', error)
    return null
  }
}

// Save request (create or update)
async function saveRequest() {
  if (!authStore.isAuthenticated) {
    router.push('/')
    return
  }
  
  loading.value = true
  errorMessage.value = ''
  
  try {


    // Geocode addresses to get coordinates
    let loadingCoords = await geocodeAddress(formData.value.addresses[0].address)
    let unloadingCoords = await geocodeAddress(formData.value.addresses[1].address)
  
    const mapCenter = ref({ lat: 40.7128, lng: -74.0060 }); // Default to NYC

    //If not geocoded, set coordinates to 0
    if (!loadingCoords || !unloadingCoords) {
      loadingCoords = { latitude: mapCenter.value.lat, longitude: mapCenter.value.lng }
      unloadingCoords = { latitude: mapCenter.value.lat, longitude: mapCenter.value.lng }
    }
    
    // Update coordinates
    formData.value.addresses[0].latitude = loadingCoords.latitude
    formData.value.addresses[0].longitude = loadingCoords.longitude
    formData.value.addresses[1].latitude = unloadingCoords.latitude
    formData.value.addresses[1].longitude = unloadingCoords.longitude
    
    if (isEditing.value && requestsStore.selectedRequest?.id) {
      // Update existing request
      await requestsStore.updateRequest(requestsStore.selectedRequest.id, formData.value)
    } else {
      // Create new request
      await requestsStore.createRequest(formData.value)
    }
    
    // Navigate back to requests list
    router.push('/')
  } catch (error) {
    console.error('Save request error:', error)
    errorMessage.value = 'Failed to save request. Please try again.'
  } finally {
    loading.value = false
  }
}

// Update status and navigate to tracking
async function takeRequest() {
  if (!requestsStore.selectedRequest?.id) {
    console.error('No request selected or request has no ID')
    errorMessage.value = 'Cannot take request: No request selected'
    return
  }
  
  const requestId = requestsStore.selectedRequest.id
  console.log(`Taking request ID: ${requestId}`)
  
  loading.value = true
  try {
    // Update request status to 'confirmed'
    const updatedRequest = await requestsStore.updateRequestStatus(requestId, 'confirmed')
    
    if (!updatedRequest) {
      console.error('Failed to update request status')
      errorMessage.value = `Failed to update request status: ${requestsStore.error || 'Unknown error'}`
      return
    }
    
    console.log('Request status updated successfully, navigating to tracking')
    
    // Navigate to tracking view with request ID
    router.push({
      path: '/tracking',
      query: { id: requestId.toString() }
    })
  } catch (error) {
    console.error('Error taking request:', error)
    errorMessage.value = 'Failed to update request status'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="details-view">
    <Header :title="isEditing ? 'Edit Request' : 'Request Details'" />
    
    <main v-if="loading" class="p-5 pb-24 flex justify-center items-center">
      <p>Loading...</p>
    </main>
    
    <main v-else-if="errorMessage" class="p-5 pb-24">
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <p>{{ errorMessage }}</p>
      </div>
    </main>
    
    <main v-else-if="isEditing || !requestsStore.selectedRequest" class="p-5 pb-24">
      <!-- Edit/Create Form -->
      <form @submit.prevent="saveRequest">
        <!-- Property Type -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Property Type</label>
          <select v-model="formData.property_type" class="w-full p-2 border rounded">
            <option value="residential">Residential</option>
            <option value="commercial">Commercial</option>
            <option value="office">Office</option>
          </select>
        </div>
        
        <!-- Square Feet -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Square Feet</label>
          <input type="number" v-model="formData.square_feet" class="w-full p-2 border rounded" />
        </div>
        
        <!-- Movers Count -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Number of Movers</label>
          <input type="number" v-model="formData.movers_count" min="1" class="w-full p-2 border rounded" />
        </div>
        
        <!-- Hourly Rate -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Hourly Rate ($)</label>
          <input type="number" v-model="formData.hourly_rate" class="w-full p-2 border rounded" />
        </div>
        
        <!-- Departure Time -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Departure Time</label>
          <input type="datetime-local" v-model="formData.departure_time" class="w-full p-2 border rounded" />
        </div>
        
        <!-- Loading Address -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Loading Address</label>
          <input type="text" v-model="formData.addresses[0].address" class="w-full p-2 border rounded" />
        </div>
        
        <!-- Unloading Address -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Unloading Address</label>
          <input type="text" v-model="formData.addresses[1].address" class="w-full p-2 border rounded" />
        </div>
        
        <!-- Submit Button -->
        <GradientButton small-button="true" type="submit" class="w-full" :disabled="loading">
          {{ isEditing ? 'Update' : 'Create' }} Request
        </GradientButton>
      </form>
    </main>
    
    <main v-else class="p-5 pb-24">
      <!-- Request Number Section -->
      <div class="bg-[var(--color-background)] rounded-lg p-4 mb-4 shadow-sm">
        <h2 class="text-[var(--color-text)] opacity-60 text-sm">Request number</h2>
        <p class="text-[var(--color-text)] font-medium">{{ requestsStore.selectedRequest.id }}</p>
      </div>
      
      <!-- Type Section -->
      <div class="bg-[var(--color-background)] rounded-lg p-4 mb-4 shadow-sm">
        <h2 class="text-[var(--color-text)] opacity-60 text-sm">Type</h2>
        <p class="text-[var(--color-text)] font-medium">{{ requestsStore.selectedRequest.property_type }} - {{ requestsStore.selectedRequest.square_feet }} sq ft</p>
      </div>
      
      <!-- Date and Time Section -->
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="bg-[var(--color-background)] rounded-lg p-4 shadow-sm">
          <h2 class="text-[var(--color-text)] opacity-60 text-sm">Date</h2>
          <p class="text-[var(--color-text)] font-medium">{{ formattedDate }}</p>
        </div>
        <div class="bg-[var(--color-background)] rounded-lg p-4 shadow-sm">
          <h2 class="text-[var(--color-text)] opacity-60 text-sm">Time</h2>
          <p class="text-[var(--color-text)] font-medium">{{ formattedTime }}</p>
        </div>
      </div>
      
      <!-- Loading Address Section -->
      <div class="bg-[var(--color-background)] rounded-lg p-4 mb-4 shadow-sm">
        <h2 class="text-[var(--color-text)] opacity-60 text-sm">Loading address</h2>
        <p class="text-[var(--color-text)] font-medium">{{ loadingAddress }}</p>
      </div>
      
      <!-- Unloading Address Section -->
      <div class="bg-[var(--color-background)] rounded-lg p-4 mb-4 shadow-sm">
        <h2 class="text-[var(--color-text)] opacity-60 text-sm">Unloading address</h2>
        <p class="text-[var(--color-text)] font-medium">{{ unloadingAddress }}</p>
      </div>
      
      <!-- Price Section -->
      <div class="mb-6">
        <h2 class="text-[var(--color-text)] text-lg font-semibold mb-2">Approximate price</h2>
        <div class="bg-[var(--color-background)] rounded-lg p-4 shadow-sm">
          <h2 class="text-[var(--color-text)] opacity-60 text-sm">Price</h2>
          <p class="text-[var(--color-text)] font-medium">${{ requestsStore.selectedRequest.hourly_rate }}/hr</p>
        </div>
      </div>
      
      <!-- Action Buttons -->
      <div class="grid grid-cols-2 gap-4">
        <button 
          @click="router.push({ path: '/details', query: { id: requestsStore.selectedRequest.id } })"
          class="bg-gray-500 text-white p-2 rounded hover:bg-gray-600"
        >
          Edit
        </button>
        <GradientButton small-button="true" @click="takeRequest" :disabled="loading">
          Take request
        </GradientButton>
      </div>
    </main>
    
    <Footer />
  </div>
</template>

<style scoped>
.details-view {
  min-height: 100vh;
  padding-top: 55px; /* Height of the header */
  background-color: var(--color-background-soft);
}
</style>
