<script setup lang="ts">
import { ref, onMounted, computed, onUnmounted, watchEffect, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useRequestsStore } from '../stores/requests';
import { useAuthStore } from '../stores/auth';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import GoogleMap from '@/components/ui/GoogleMap.vue';
import MapMarker from '@/components/ui/MapMarker.vue';
import MapInfoWindow from '@/components/ui/MapInfoWindow.vue';
import GradientButton from '@/components/ui/GradientButton.vue';
import MaterialCounter from '@/components/ui/MaterialCounter.vue';
import ChangeMoversModal from '@/components/modals/ChangeMoversModal.vue';
import AddMaterialModal from '@/components/modals/AddMaterialModal.vue';
import UnloadLoadModal from '@/components/modals/UnloadLoadModal.vue';
import { NDrawer, NDrawerContent, NIcon, NSelect } from 'naive-ui';

const router = useRouter();
const requestsStore = useRequestsStore();
const authStore = useAuthStore();
const loading = ref(false);
const savingMaterials = ref(false);
const error = ref('');

// State management for active request
const isMoving = ref(false);
const onBreak = ref(false);
const numberOfMovers = ref(2);
const showMoversModal = ref(false);
const showMaterialModal = ref(false);
const showUnloadLoadModal = ref(false);
const allAddressesVisited = ref(false);
const showDrawer = ref(false);
const materialSaveTimeout = ref(null);
const bottomOverlayExpanded = ref(true);
const selectedRequestId = ref<number | null>(null);
const userRequests = ref([]);

// Computed properties for request details
const departureTime = computed(() => {
  if (!requestsStore.selectedRequest?.departure_time) return '--:--';
  const date = new Date(requestsStore.selectedRequest.departure_time);
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
});

const travelTime = ref('--:--');
const routeDistance = ref('--');

// Computed route origin and destination
const routeOrigin = computed(() => {
  const addresses = requestsStore.selectedRequest?.addresses;
  if (!addresses || addresses.length === 0) return null;
  
  // Find the first loading point or use first address
  const loadingPoint = addresses.find(addr => addr.type === 'loading');
  const origin = loadingPoint || addresses[0];
  
  return {
    lat: parseFloat(origin.latitude),
    lng: parseFloat(origin.longitude)
  };
});

const routeDestination = computed(() => {
  const addresses = requestsStore.selectedRequest?.addresses;
  if (!addresses || addresses.length === 0) return null;
  
  // Find the first unloading point or use last address
  const unloadingPoints = addresses.filter(addr => addr.type === 'unloading');
  const destination = unloadingPoints.length > 0 ? unloadingPoints[unloadingPoints.length - 1] : addresses[addresses.length - 1];
  
  return {
    lat: parseFloat(destination.latitude),
    lng: parseFloat(destination.longitude)
  };
});

// Addresses from the selected request
const unloadingAddresses = computed(() => {
  if (!requestsStore.selectedRequest?.addresses) return [];
  
  return requestsStore.selectedRequest.addresses.map(addr => ({
    id: addr.id || addr.order,
    address: addr.address,
    type: addr.type,
    latitude: addr.latitude,
    longitude: addr.longitude,
    visited: false
  }));
});

// Materials used
const materials = computed(() => {
  if (!requestsStore.selectedRequest?.materials) return [];
  
  return requestsStore.selectedRequest.materials.map(mat => ({
    id: mat.id || Math.random(),
    name: mat.name,
    count: Math.max(1, mat.quantity || 1) // Ensure minimum count of 1
  }));
});

// Map data
const mapCenter = ref({ lat: 40.7128, lng: -74.0060 }); // Default to NYC
const mapZoom = ref(12);
const mapMarkers = computed(() => {
  if (!requestsStore.selectedRequest?.addresses) return [];
  
  return requestsStore.selectedRequest.addresses.map(addr => {
    const isLoading = addr.type === 'loading';
    // Ensure coordinates are numbers
    const lat = parseFloat(addr.latitude);
    const lng = parseFloat(addr.longitude);
    
    return {
      position: { 
        lat: isNaN(lat) ? 40.7128 : lat, 
        lng: isNaN(lng) ? -74.0060 : lng 
      },
      title: isLoading ? 'Loading Point' : 'Unloading Point',
      icon: {
        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="${isLoading ? '#5D87EE' : '#F97066'}">
            <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z"/>
          </svg>
        `)
      },
      address: addr.address,
      type: addr.type
    };
  });
});
const selectedMarker = ref(null);
const showInfoWindow = ref(false);

// Button text changes based on state
const mainActionButtonText = computed(() => {
  if (!isMoving.value) return 'Start moving';
  if (onBreak.value) return 'Continue';
  if (allAddressesVisited.value) return 'Complete request';
  return 'Take a break';
});

// Request selector options
const requestOptions = computed(() => {
  return userRequests.value.map((request: any) => {
    const date = new Date(request.departure_time);
    const formattedDate = date.toLocaleDateString();

    return {
      label: `Request #${request.id} - ${request.property_type} - ${formattedDate}`,
      value: request.id
    };
  });
});

// Button click handler changes based on state
const mainActionHandler = async () => {
  if (!requestsStore.selectedRequest?.id) return;
  
  loading.value = true;
  
  try {
    if (!isMoving.value) {
      // Start the move - update status to active
      await requestsStore.updateRequestStatus(requestsStore.selectedRequest.id, 'active');
      isMoving.value = true;
    } else if (onBreak.value) {
      // Continue after break - update status to active
      await requestsStore.updateRequestStatus(requestsStore.selectedRequest.id, 'active');
      onBreak.value = false;
    } else if (allAddressesVisited.value) {
      // Complete the request
      await completeRequest();
    } else {
      // Take a break - update status to break
      await requestsStore.updateRequestStatus(requestsStore.selectedRequest.id, 'break');
      onBreak.value = true;
    }
  } catch (err) {
    console.error('Action error:', err);
    error.value = 'Failed to update request status';
  } finally {
    loading.value = false;
  }
};

const completeRequest = async () => {
  if (!requestsStore.selectedRequest?.id) return;
  
  try {
    // Update materials if changed, ensuring minimum quantity of 1
    const updatedRequest = {
      status: 'completed',
      materials: materials.value.map(m => ({
        name: m.name,
        quantity: Math.max(1, m.count)
      }))
    };
    
    await requestsStore.updateRequest(requestsStore.selectedRequest.id, updatedRequest);
    router.push('/');
  } catch (err) {
    console.error('Complete request error:', err);
    error.value = 'Failed to complete request';
  }
};

// Open change mover modal
const openChangeMoverModal = () => {
  console.log('Opening change movers modal, current movers:', numberOfMovers.value);
  showMoversModal.value = true;
};

const handleMoversChange = async (newMoversCount) => {
  if (!requestsStore.selectedRequest?.id) {
    console.error('No request selected for updating movers count');
    return;
  }
  
  console.log(`Updating movers count to: ${newMoversCount} for request: ${requestsStore.selectedRequest.id}`);
  
  try {
    loading.value = true;
    const result = await requestsStore.updateMoversCount(requestsStore.selectedRequest.id, newMoversCount);
    
    if (result) {
      numberOfMovers.value = newMoversCount;
      console.log('Movers count updated successfully');
    } else {
      console.error('Failed to update movers count:', requestsStore.error);
      error.value = `Failed to update movers count: ${requestsStore.error || 'Unknown error'}`;
    }
  } catch (err) {
    console.error('Update movers error:', err);
    error.value = 'Failed to update movers count';
  } finally {
    loading.value = false;
  }
};

const handleAddMaterial = async (materialName) => {
  if (!requestsStore.selectedRequest?.id) {
    console.error('No request selected for adding material');
    return;
  }
  
  if (!requestsStore.selectedRequest.materials) {
    requestsStore.selectedRequest.materials = [];
  }
  
  console.log(`Adding material: ${materialName}`);
  
  // Add material to the request object with quantity 1 (not 0)
  const newMaterial = {
    name: materialName,
    quantity: 1  // Set initial quantity to 1 to pass validation
  };
  
  // Add to store first
  requestsStore.selectedRequest.materials.push(newMaterial);
  console.log('Materials after adding locally:', requestsStore.selectedRequest.materials);
  
  // Save to backend
  try {
    savingMaterials.value = true;
    
    // Create an update with just the materials array
    const updateData = {
      materials: requestsStore.selectedRequest.materials.map(m => ({
        name: m.name,
        quantity: m.quantity || 1  // Ensure at least 1 quantity
      }))
    };
    
    // Update the request on the server
    const result = await requestsStore.updateRequest(requestsStore.selectedRequest.id, updateData);
    
    if (result) {
      console.log('Material saved successfully to backend');
    } else {
      console.error('Failed to save material to backend:', requestsStore.error);
      error.value = `Failed to save material: ${requestsStore.error || 'Unknown error'}`;
    }
  } catch (err) {
    console.error('Error saving material:', err);
    error.value = 'Failed to save material. Please try again.';
  } finally {
    savingMaterials.value = false;
  }
};

const handleMaterialCountChange = async ({ name, count }) => {
  if (!requestsStore.selectedRequest?.id || !requestsStore.selectedRequest?.materials) {
    console.error('No request selected for updating material');
    return;
  }
  
  const material = requestsStore.selectedRequest.materials.find(m => m.name === name);
  if (material) {
    // Enforce minimum quantity of 1
    const newQuantity = Math.max(1, count);
    
    // Update local value
    material.quantity = newQuantity;
    console.log(`Updated material ${name} quantity to ${newQuantity}`);
    
    // Debounce the save operation to avoid too many API calls
    if (materialSaveTimeout.value) {
      clearTimeout(materialSaveTimeout.value);
    }
    
    materialSaveTimeout.value = setTimeout(async () => {
      try {
        savingMaterials.value = true;
        
        // Create an update with just the materials array
        const updateData = {
          materials: requestsStore.selectedRequest.materials.map(m => ({
            name: m.name,
            quantity: m.quantity || 1  // Ensure at least 1 quantity
          }))
        };
        
        // Update the request on the server
        const result = await requestsStore.updateRequest(requestsStore.selectedRequest.id, updateData);
        
        if (result) {
          console.log('Material quantities saved successfully to backend');
        } else {
          console.error('Failed to save material quantities to backend:', requestsStore.error);
          error.value = `Failed to save quantity changes: ${requestsStore.error || 'Unknown error'}`;
        }
      } catch (err) {
        console.error('Error saving material quantities:', err);
        error.value = 'Failed to save quantity changes. Please try again.';
      } finally {
        savingMaterials.value = false;
      }
    }, 500); // Reduce the delay to 500ms for quicker updates
  }
};

const openUnloadLoadModal = () => {
  showUnloadLoadModal.value = true;
};

const handleUnload = async () => {
  console.log('Unload operation triggered');
  
  // Track that we've performed an unload operation
  // Since we don't have a specific API endpoint for marking addresses as visited,
  // we'll update the request with a note or status change
  if (!requestsStore.selectedRequest?.id) return;
  
  try {
    // Mark that unloading has been done
    // You could potentially update the request with metadata about completed addresses
    allAddressesVisited.value = true;
    
    // Close the modal
    showUnloadLoadModal.value = false;
    
    // Show success message (if you have a message system)
    console.log('Unloading completed at current location');
  } catch (err) {
    console.error('Unload error:', err);
    error.value = 'Failed to mark unloading complete';
  }
};

const handleLoad = async () => {
  console.log('Load operation triggered');
  
  if (!requestsStore.selectedRequest?.id) return;
  
  try {
    // Track that we've performed a load operation
    // This could update the request status or add metadata
    
    // Close the modal
    showUnloadLoadModal.value = false;
    
    // Show success message
    console.log('Loading completed at current location');
  } catch (err) {
    console.error('Load error:', err);
    error.value = 'Failed to mark loading complete';
  }
};

// Toggle materials drawer
const toggleDrawer = () => {
  showDrawer.value = !showDrawer.value;
};

// Open add material modal
const openAddMaterialModal = () => {
  showMaterialModal.value = true;
  console.log('Opening add material modal');
};

// Handle marker click
const handleMarkerClick = (marker) => {
  selectedMarker.value = marker;
  showInfoWindow.value = true;
};

// Close info window
const closeInfoWindow = () => {
  showInfoWindow.value = false;
  selectedMarker.value = null;
};

// Handle route calculation results
const handleRouteCalculated = (routeInfo) => {
  if (routeInfo && routeInfo.duration && routeInfo.distance) {
    // Update travel time
    const durationInMinutes = Math.round(routeInfo.duration / 60);
    const hours = Math.floor(durationInMinutes / 60);
    const minutes = durationInMinutes % 60;
    travelTime.value = hours > 0 ? `${hours}h ${minutes}m` : `${minutes}m`;

    // Update distance
    const distanceInKm = (routeInfo.distance / 1000).toFixed(1);
    routeDistance.value = `${distanceInKm} km`;
  }
};

// Handle request selection change
const handleRequestChange = async (requestId: number) => {
  if (!requestId || requestId === requestsStore.selectedRequest?.id) return;

  loading.value = true;
  error.value = '';

  try {
    // Load the selected request
    const request = await requestsStore.getRequestById(requestId);

    if (!request || !requestsStore.selectedRequest) {
      error.value = `Failed to load request #${requestId}`;
      loading.value = false;
      return;
    }

    // Update URL with new request ID
    router.replace({
      path: '/tracking',
      query: { id: requestId.toString() }
    });

    // Update selected request ID
    selectedRequestId.value = requestId;

    // Update status flags
    const status = requestsStore.selectedRequest.status || 'pending';

    if (status === 'confirmed') {
      isMoving.value = false;
      onBreak.value = false;
    } else if (status === 'active') {
      isMoving.value = true;
      onBreak.value = false;
    } else if (status === 'break') {
      isMoving.value = true;
      onBreak.value = true;
    }

    // Update map center
    if (requestsStore.selectedRequest.addresses?.length > 0) {
      const firstAddr = requestsStore.selectedRequest.addresses[0];
      const lat = parseFloat(firstAddr.latitude);
      const lng = parseFloat(firstAddr.longitude);

      if (!isNaN(lat) && !isNaN(lng)) {
        mapCenter.value = { lat, lng };
      }
    }

    // Update number of movers
    numberOfMovers.value = requestsStore.selectedRequest.movers_count || 2;

  } catch (err) {
    console.error('Error changing request:', err);
    error.value = 'Failed to load selected request';
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  if (!authStore.isAuthenticated) {
    router.push('/')
    return
  }

  loading.value = true
  error.value = ''

  try {
    // Fetch all user requests for the selector
    await requestsStore.fetchRequests();
    userRequests.value = requestsStore.requests || [];
    console.log('Loaded user requests:', userRequests.value.length);

    // Get request ID from either URL query parameter or store
    let requestId = Number(router.currentRoute.value.query.id)
    console.log('TrackingView mounted with query ID:', requestId)
    
    // If ID is not in URL but we have a request in store, use that ID
    if (!requestId && requestsStore.selectedRequest?.id) {
      requestId = requestsStore.selectedRequest.id
      console.log(`No ID in URL, using ID from store: ${requestId}`)
      
      // Update the URL to include the ID without reloading the page
      router.replace({
        path: '/tracking',
        query: { id: requestId.toString() }
      })
    }
    
    // If we still don't have an ID, show error
    if (!requestId) {
      error.value = 'No request ID provided. Please select a request first.'
      console.error('No request ID in URL or store')
      loading.value = false
      return
    }
    
    // If the selected request doesn't match the ID in URL or we don't have a request loaded
    // then load the request data from API
    if (!requestsStore.selectedRequest || requestsStore.selectedRequest.id !== requestId) {
      console.log(`Loading request with ID: ${requestId}`)
      const request = await requestsStore.getRequestById(requestId)
      
      if (!request || !requestsStore.selectedRequest) {
        error.value = `Failed to load request #${requestId}. ${requestsStore.error || 'Unknown error'}`
        console.error('Failed to load request:', requestsStore.error)
        loading.value = false
        return
      }
    } else {
      console.log('Request already loaded in store:', requestsStore.selectedRequest)
    }
    
    // Update status if needed
    const status = requestsStore.selectedRequest.status || 'pending'
    console.log(`Request status: ${status}`)
    
    if (status === 'confirmed') {
      isMoving.value = false
      onBreak.value = false
    } else if (status === 'active') {
      isMoving.value = true
      onBreak.value = false
    } else if (status === 'break') {
      isMoving.value = true
      onBreak.value = true
    }
    
    // Set map center to the first address if available
    if (requestsStore.selectedRequest.addresses?.length > 0) {
      const firstAddr = requestsStore.selectedRequest.addresses[0];
      const lat = parseFloat(firstAddr.latitude);
      const lng = parseFloat(firstAddr.longitude);
      
      if (!isNaN(lat) && !isNaN(lng)) {
        mapCenter.value = { lat, lng };
        console.log('Map center set to:', mapCenter.value);
      } else {
        console.warn('Invalid coordinates, using default map center');
        mapCenter.value = { lat: 40.7128, lng: -74.0060 }; // Default to NYC
      }
    } else {
      console.warn('Request has no addresses, using default map center');
      mapCenter.value = { lat: 40.7128, lng: -74.0060 }; // Default to NYC
    }
    
    // Set number of movers
    numberOfMovers.value = requestsStore.selectedRequest.movers_count || 2
    console.log(`Number of movers: ${numberOfMovers.value}`)

    // Set selected request ID for the selector
    selectedRequestId.value = requestsStore.selectedRequest.id
  } catch (err) {
    console.error('Init tracking error:', err)
    error.value = 'An error occurred while loading the request'
  } finally {
    loading.value = false
  }
})

// Update URL whenever request status changes
watch(() => requestsStore.selectedRequest?.status, (newStatus, oldStatus) => {
  if (newStatus !== oldStatus && requestsStore.selectedRequest?.id) {
    const currentId = router.currentRoute.value.query.id
    const storeId = requestsStore.selectedRequest.id.toString()
    
    // If the ID in URL doesn't match the store ID, update URL
    if (currentId !== storeId) {
      console.log(`Updating URL with request ID: ${storeId}`)
      router.replace({
        path: '/tracking',
        query: { id: storeId }
      })
    }
  }
})

// Keep the map centered when markers change
watchEffect(() => {
  if (mapMarkers.value.length > 0 && !selectedMarker.value) {
    const firstMarker = mapMarkers.value[0];
    if (firstMarker && firstMarker.position) {
      mapCenter.value = {
        lat: parseFloat(firstMarker.position.lat) || 40.7128,
        lng: parseFloat(firstMarker.position.lng) || -74.0060
      };
    }
  }
});

// Clean up
onUnmounted(() => {
  // Clear any pending timeouts
  if (materialSaveTimeout.value) {
    clearTimeout(materialSaveTimeout.value);
    materialSaveTimeout.value = null;
  }
});
</script>

<template>
  <div class="tracking-view">
    <Header :title="`Request ${requestsStore.selectedRequest?.id || ''}`" />
    
    <main v-if="loading" class="flex items-center justify-center h-screen">
      <p>Loading tracking data...</p>
    </main>
    
    <main v-else-if="error" class="p-4">
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <p>{{ error }}</p>
      </div>
      <GradientButton @click="router.push('/')">
        Return to Requests
      </GradientButton>
    </main>
    
    <main v-else-if="!requestsStore.selectedRequest" class="p-4">
      <p>No request selected. Please select a request first.</p>
      <GradientButton @click="router.push('/')" class="mt-4">
        Return to Requests
      </GradientButton>
    </main>
    
    <main v-else class="tracking-content">
      <!-- Request Selector -->
      <div class="request-selector-container">
        <n-select
          v-model:value="selectedRequestId"
          :options="requestOptions"
          placeholder="Select a request"
          @update:value="handleRequestChange"
          :disabled="loading"
          class="request-selector"
        />
      </div>

      <!-- Map Container with overlays -->
      <div class="map-wrapper">
        <!-- Map -->
        <div class="map-container">
          <GoogleMap 
            :center="mapCenter" 
            :zoom="mapZoom" 
            height="100%"
            :show-route="true"
            :origin="routeOrigin"
            :destination="routeDestination"
            @route-calculated="handleRouteCalculated"
          >
            <MapMarker 
              v-for="marker in mapMarkers"
              :key="marker.title"
              :position="marker.position"
              :icon="marker.icon"
              @click="handleMarkerClick(marker)"
            />
            <MapInfoWindow
              v-if="showInfoWindow && selectedMarker"
              :position="selectedMarker.position"
              @close="closeInfoWindow"
            >
              <div class="info-window-content">
                <h3 class="text-lg font-semibold">{{ selectedMarker.title }}</h3>
                <p>{{ selectedMarker.address }}</p>
              </div>
            </MapInfoWindow>
          </GoogleMap>
        </div>
        
        <!-- Top Info Cards -->
        <div class="info-cards">
          <div class="info-card">
            <span class="info-label">Departure time</span>
            <span class="info-value">{{ departureTime }}</span>
          </div>
          <div class="info-card">
            <span class="info-label">Travel time</span>
            <span class="info-value">{{ travelTime }}</span>
          </div>
        </div>
        
        <!-- Bottom Content -->
        <div class="bottom-content flex" v-if="!isMoving">
          <!-- Start Moving Button -->
          <GradientButton 
            @click="mainActionHandler" 
            :disabled="loading"
            :full-width="true"
            class="start-button"
          >
            {{ mainActionButtonText }}
          </GradientButton>
        </div>
        
        <!-- After Start Moving -->
        <div class="bottom-overlay" :class="{ 'collapsed': !bottomOverlayExpanded }" v-else>
          <!-- Drag Handle -->
          <div class="drag-handle" @click="bottomOverlayExpanded = !bottomOverlayExpanded">
            <div class="handle-bar"></div>
          </div>
          
          <!-- Content Container -->
          <div class="overlay-content" v-show="bottomOverlayExpanded">
            <!-- Unloading Points Block -->
            <div class="content-block">
              <h3 class="block-title">Unloading point</h3>
              <div class="address-list">
                <div v-for="(addr, index) in unloadingAddresses" :key="addr.id" class="address-item">
                  {{ addr.address }}
                </div>
              </div>
            </div>
            
            <!-- Used Materials Block -->
            <div class="content-block" v-if="materials.length > 0">
              <h3 class="block-title">Used materials</h3>
              <div class="materials-grid">
                <MaterialCounter 
                  v-for="material in materials" 
                  :key="material.id"
                  :name="material.name"
                  :initialCount="material.count"
                  @update:count="handleMaterialCountChange"
                />
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
              <GradientButton 
                @click="mainActionHandler" 
                :disabled="loading"
                :full-width="true"
                class="take-break-button"
              >
                {{ mainActionButtonText }}
              </GradientButton>
              
              <div class="secondary-buttons">
                <GradientButton 
                  @click="openAddMaterialModal"
                  :disabled="savingMaterials"
                  class="secondary-btn"
                >
                  Add materials
                </GradientButton>
                
                <GradientButton 
                  @click="openUnloadLoadModal" 
                  :disabled="onBreak"
                  class="secondary-btn"
                >
                  Unload/Load
                </GradientButton>
              </div>
              
              <GradientButton 
                @click="openChangeMoverModal"
                :full-width="true"
                class="change-movers-btn"
              >
                Change number of movers
              </GradientButton>
            </div>
          </div>
        </div>
      </div>
    </main>
    
    <!-- Modals -->
    <ChangeMoversModal 
      v-model:show="showMoversModal"
      :initialCount="numberOfMovers"
      @confirm="handleMoversChange"
      @cancel="showMoversModal = false"
    />
    
    <AddMaterialModal
      v-model:show="showMaterialModal"
      @add="handleAddMaterial"
      @cancel="showMaterialModal = false"
    />
    
    <UnloadLoadModal
      v-model:show="showUnloadLoadModal"
      :addresses="unloadingAddresses"
      @unload="handleUnload"
      @load="handleLoad"
      @cancel="showUnloadLoadModal = false"
    />
    
    <Footer />
  </div>
</template>

<style scoped>
.tracking-view {
  min-height: 100vh;
  padding-bottom: 70px;
  display: flex;
  flex-direction: column;
  background-color: var(--color-background);
  
  /* Override App.vue padding */
  margin-top: -55px;
  margin-bottom: -70px;
  padding-top: 55px;
}

.tracking-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  position: relative;
}

.request-selector-container {
  background: var(--color-background);
  padding: 12px 16px;
  border-bottom: 1px solid var(--color-border);
  z-index: 20;
}

.request-selector {
  max-width: 500px;
  margin: 0 auto;
}

.map-wrapper {
  flex: 1;
  position: relative;
  display: flex;
  flex-direction: column;
}

.map-container {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1;
}

/* Top Info Cards */
.info-cards {
  position: absolute;
  top: 20px;
  left: 16px;
  right: 16px;
  display: flex;
  gap: 12px;
  z-index: 10;
}

.info-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  padding: 12px 16px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  gap: 4px;
  flex: 1;
}

.info-label {
  font-size: 12px;
  color: #666666;
  font-weight: 400;
}

.info-value {
  font-size: 18px;
  font-weight: 600;
  color: var(--color-text);
}

/* Bottom Content - Initial State */
.bottom-content {
  position: absolute;
  bottom: 70px; /* Height of footer */
  left: 0;
  right: 0;
  padding: 20px 16px;
  z-index: 10;
}

.start-button {
  max-width: 400px;
  margin: 0 auto;
}

/* Bottom Overlay - After Start Moving */
.bottom-overlay {
  position: absolute;
  bottom: 0; /* Height of footer */
  left: 0;
  right: 0;
  background: transparent;
  z-index: 10;
  transition: transform 0.3s ease;
}

.bottom-overlay.collapsed {
  transform: translateY(calc(100% - 40px));
}

/* Drag Handle */
.drag-handle {
  width: 60px;
  height: 32px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  margin: 0 auto 12px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
}

.handle-bar {
  width: 28px;
  height: 3px;
  background: #C0C0C0;
  border-radius: 2px;
}

/* Content Container */
.overlay-content {
  background: transparent;
  padding: 0 16px 16px;
  max-height: calc(60vh - 40px);
  overflow-y: auto;
}

/* Content Blocks */
.content-block {
  background: var(--color-background-mute);
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.block-title {
  font-size: 14px;
  color: #666666;
  font-weight: 500;
  margin-bottom: 12px;
}

/* Address List */
.address-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.address-item {
  font-size: 16px;
  color: var(--color-text);
  line-height: 1.4;
}

/* Materials Grid */
.materials-grid {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 12px;
}


.secondary-buttons {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.secondary-btn {
  font-size: 14px;
  padding: 12px 16px;
}

.change-movers-btn {
  background: rgba(255, 255, 255, 0.9);
  color: var(--color-text);
  border: 1px solid var(--color-border);
  font-weight: 500;
}

.change-movers-btn:hover {
  background: rgba(255, 255, 255, 1);
  transform: translateY(-1px);
}

/* Dark mode adjustments */
@media (prefers-color-scheme: dark) {
  .info-card {
    background: rgba(42, 42, 42, 0.95);
  }
  
  .drag-handle {
    background: rgba(60, 60, 60, 0.95);
  }
  
  .handle-bar {
    background: #666666;
  }
  
  .content-block {
    background: var(--color-background-soft);
  }
  
  .change-movers-btn {
    background: rgba(42, 42, 42, 0.9);
    border-color: var(--color-border);
  }
  
  .change-movers-btn:hover {
    background: rgba(42, 42, 42, 1);
  }
}

/* Responsive */
@media (max-width: 640px) {
  .request-selector-container {
    padding: 8px 12px;
  }

  .request-selector {
    max-width: 100%;
  }

  .info-cards {
    top: 12px;
    left: 12px;
    right: 12px;
  }

  .info-card {
    padding: 10px 14px;
  }

  .info-value {
    font-size: 16px;
  }

  .overlay-content {
    padding: 0 12px 12px;
  }

  .content-block {
    padding: 12px;
  }
}
</style>
