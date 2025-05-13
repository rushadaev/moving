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
import SecondaryButton from '@/components/ui/SecondaryButton.vue';
import MaterialCounter from '@/components/ui/MaterialCounter.vue';
import ChangeMoversModal from '@/components/modals/ChangeMoversModal.vue';
import AddMaterialModal from '@/components/modals/AddMaterialModal.vue';
import UnloadLoadModal from '@/components/modals/UnloadLoadModal.vue';
import { NDrawer, NDrawerContent, NIcon } from 'naive-ui';

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

// Computed properties for request details
const departureTime = computed(() => {
  if (!requestsStore.selectedRequest?.departure_time) return '--:--';
  const date = new Date(requestsStore.selectedRequest.departure_time);
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
});

const travelTime = ref('--:--');

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
    return {
      position: { 
        lat: addr.latitude, 
        lng: addr.longitude 
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

const handleUnload = () => {
  console.log('Unload operation triggered');
  // Simulate completing all addresses
  allAddressesVisited.value = true;
};

const handleLoad = () => {
  console.log('Load operation triggered');
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

onMounted(async () => {
  if (!authStore.isAuthenticated) {
    router.push('/')
    return
  }
  
  loading.value = true
  error.value = ''
  
  try {
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
      const firstAddr = requestsStore.selectedRequest.addresses[0]
      mapCenter.value = { 
        lat: firstAddr.latitude, 
        lng: firstAddr.longitude 
      }
      console.log('Map center set to:', mapCenter.value)
    } else {
      console.warn('Request has no addresses, using default map center')
    }
    
    // Set number of movers
    numberOfMovers.value = requestsStore.selectedRequest.movers_count || 2
    console.log(`Number of movers: ${numberOfMovers.value}`)
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
    mapCenter.value = mapMarkers.value[0].position;
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
    <Header title="Tracking" />
    
    <main v-if="loading" class="flex items-center justify-center h-screen">
      <p>Loading tracking data...</p>
    </main>
    
    <main v-else-if="error" class="p-4">
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <p>{{ error }}</p>
      </div>
      <button @click="router.push('/')" class="bg-blue-500 text-white px-4 py-2 rounded">
        Return to Requests
      </button>
    </main>
    
    <main v-else-if="!requestsStore.selectedRequest" class="p-4">
      <p>No request selected. Please select a request first.</p>
      <button @click="router.push('/')" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">
        Return to Requests
      </button>
    </main>
    
    <main v-else class="tracking-content">
      <!-- Map Section -->
      <div class="map-container">
        <GoogleMap :center="mapCenter" :zoom="mapZoom">
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
      
      <!-- Info Bar -->
      <div class="info-bar">
        <div class="info-item">
          <span class="info-label">Departure</span>
          <span class="info-value">{{ departureTime }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Travel time</span>
          <span class="info-value">{{ travelTime }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Movers</span>
          <span class="info-value flex items-center">
            {{ numberOfMovers }}
            <button 
              @click="openChangeMoverModal" 
              class="ml-2 text-blue-500 text-xs p-1"
              type="button"
            >
              Change
            </button>
          </span>
        </div>
      </div>
      
      <!-- Action Buttons -->
      <div class="action-buttons">
        <SecondaryButton @click="openUnloadLoadModal" :disabled="!isMoving || onBreak">
          {{ allAddressesVisited ? 'Loading/Unloading complete' : 'Unload/Load' }}
        </SecondaryButton>
        
        <GradientButton small-button="true" @click="mainActionHandler" :disabled="loading">
          {{ mainActionButtonText }}
        </GradientButton>
      </div>
      
      <!-- Materials Button (Floating) -->
      <button @click="toggleDrawer" class="materials-button">
        <span>Materials</span>
      </button>
      
      <!-- Materials Drawer -->
      <NDrawer v-model:show="showDrawer" placement="right" :width="280">
        <NDrawerContent title="Materials">
          <div class="materials-list">
            <div v-if="savingMaterials" class="saving-indicator text-blue-500 text-sm mb-2">
              Saving changes...
            </div>
            
            <MaterialCounter 
              v-for="material in materials" 
              :key="material.id"
              :name="material.name"
              :initialCount="material.count"
              @update:count="handleMaterialCountChange"
            />
            
            <button 
              @click="openAddMaterialModal"
              class="mt-4 w-full py-2 px-4 bg-blue-100 text-blue-600 rounded-lg"
              :disabled="savingMaterials"
            >
              Add New Material
            </button>
          </div>
        </NDrawerContent>
      </NDrawer>
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
    />
    
    <Footer />
  </div>
</template>

<style scoped>
.tracking-view {
  min-height: 100vh;
  padding-top: 55px; /* Height of the header */
  display: flex;
  flex-direction: column;
}

.tracking-content {
  display: flex;
  flex-direction: column;
  flex: 1;
  position: relative;
}

.map-container {
  height: 300px;
  width: 100%;
}

.info-bar {
  display: flex;
  justify-content: space-between;
  padding: 16px;
  background-color: var(--color-background);
  border-bottom: 1px solid var(--color-border);
}

.info-item {
  display: flex;
  flex-direction: column;
}

.info-label {
  font-size: 12px;
  color: var(--color-text-light);
}

.info-value {
  font-weight: 500;
}

.action-buttons {
  padding: 16px;
  display: flex;
  gap: 12px;
  margin-top: auto;
  margin-bottom: 70px; /* Space for the footer */
}

.materials-button {
  position: fixed;
  bottom: 80px;
  right: 16px;
  background-color: var(--color-background);
  color: var(--color-text);
  padding: 8px 16px;
  border-radius: 8px;
  z-index: 10;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.movers-test-button {
  position: fixed;
  bottom: 120px;
  right: 16px;
  background-color: var(--color-primary);
  color: white;
  padding: 8px 16px;
  border-radius: 8px;
  z-index: 10;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.materials-list {
  height: 100%;
  overflow-y: auto;
}
</style>
