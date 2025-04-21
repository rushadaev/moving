<script setup lang="ts">
import { ref, onMounted, computed, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
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

const router = useRouter();
const request = ref(null);
const departureTime = ref('12:00');
const travelTime = ref('45:54');

// State management for active request
const isMoving = ref(false);
const onBreak = ref(false);
const numberOfMovers = ref(2);
const showMoversModal = ref(false);
const showMaterialModal = ref(false);
const showUnloadLoadModal = ref(false);
const allAddressesVisited = ref(false);

// Unloading addresses
const unloadingAddresses = ref([
  { id: 1, address: 'Address 1' },
  { id: 2, address: 'Address 2' }
]);

// Materials used
const materials = ref([
  { id: 1, name: 'Material 1', count: 3 },
  { id: 2, name: 'Material 2', count: 3 },
  { id: 3, name: 'Material 3', count: 3 }
]);

// Map data
const mapCenter = ref({ lat: 40.7128, lng: -74.0060 }); // Default to NYC
const mapZoom = ref(12);
const mapMarkers = ref([]);
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
const mainActionHandler = () => {
  if (!isMoving.value) {
    // Start the move
    isMoving.value = true;
    console.log('Started moving with request:', request.value?.requestNumber);
  } else if (onBreak.value) {
    // Continue after break
    onBreak.value = false;
    console.log('Continuing after break');
  } else if (allAddressesVisited.value) {
    // Complete the request
    completeRequest();
  } else {
    // Take a break
    onBreak.value = true;
    console.log('Taking a break');
  }
};

const completeRequest = () => {
  console.log('Completing request with materials:', materials.value);
  // Here you would implement the logic to complete the request
  // After completion, redirect to home
  router.push('/');
};

const openChangeMoverModal = () => {
  showMoversModal.value = true;
};

const handleMoversChange = (newMoversCount) => {
  numberOfMovers.value = newMoversCount;
  console.log('Number of movers changed to:', newMoversCount);
};

const openAddMaterialModal = () => {
  showMaterialModal.value = true;
};

const handleAddMaterial = (materialName) => {
  const newId = materials.value.length > 0 
    ? Math.max(...materials.value.map(m => m.id)) + 1 
    : 1;
  
  materials.value.push({
    id: newId,
    name: materialName,
    count: 0
  });
  
  console.log('Material added:', materialName);
};

const handleMaterialCountChange = ({ name, count }) => {
  const material = materials.value.find(m => m.name === name);
  if (material) {
    material.count = count;
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

onMounted(() => {
  // Get selected request from localStorage
  const selectedRequest = localStorage.getItem('selectedRequest');
  if (selectedRequest) {
    request.value = JSON.parse(selectedRequest);
    
    // If the request has addresses, populate them
    if (request.value?.loadingAddress) {
      unloadingAddresses.value = [];
      unloadingAddresses.value.push({ id: 1, address: request.value.loadingAddress });
      
      if (request.value?.unloadingAddress) {
        unloadingAddresses.value.push({ id: 2, address: request.value.unloadingAddress });
      }
      
      // Set map markers based on addresses
      mapMarkers.value = [
        {
          position: { lat: 40.7128, lng: -74.0060 }, // Replace with actual geocoded coordinates
          title: 'Loading Point',
          icon: {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#5D87EE">
                <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z"/>
              </svg>
            `)
          }
        },
        {
          position: { lat: 40.7328, lng: -73.9960 }, // Replace with actual geocoded coordinates
          title: 'Unloading Point',
          icon: {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#F97066">
                <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z"/>
              </svg>
            `)
          }
        }
      ];
      
      // Set map center to the loading point
      mapCenter.value = mapMarkers.value[0].position;
    }
  } else {
    // If no request is found, redirect back to the requests page
    router.push('/');
  }
  
  // Listen for marker click events from GoogleMap component
  document.addEventListener('marker-click', (e: any) => {
    handleMarkerClick(e.detail);
  });
});

onUnmounted(() => {
  // Remove event listener
  document.removeEventListener('marker-click', () => {});
});
</script>

<template>
  <div class="tracking-view">
    <Header :title="request ? request.requestNumber : 'Request number'" />
    
    <main class="p-0">
      <!-- Status message when on break -->
      <div v-if="onBreak" class="status-message">
        <div class="bg-blue-100 text-blue-800 p-3 text-center rounded-lg mx-4 my-2">
          You took a break
        </div>
      </div>
      
      <!-- Map area with floating elements -->
      <div class="map-container relative" :style="{ height: isMoving ? 'calc(100vh - 200px)' : 'calc(100vh - 150px)' }">
        <GoogleMap 
          :height="'100%'" 
          :center="mapCenter"
          :zoom="mapZoom"
          :markers="mapMarkers"
          :showRoute="true"
          :origin="mapMarkers[0]?.position"
          :destination="mapMarkers[1]?.position"
        />
        
        <!-- Floating time info cards -->
        <div class="floating-time-cards">
          <div class="bg-white bg-opacity-90 rounded-lg p-3 shadow-sm flex-1 mr-2">
            <h2 class="text-gray-500 text-sm">Departure time</h2>
            <p class="font-medium">{{ departureTime }}</p>
          </div>
          <div class="bg-white bg-opacity-90 rounded-lg p-3 shadow-sm flex-1 ml-2">
            <h2 class="text-gray-500 text-sm">Travel time</h2>
            <p class="font-medium">{{ travelTime }}</p>
          </div>
        </div>
        
        <!-- Unloading addresses (shown when moving) - now as floating element -->
        <div v-if="isMoving" class="floating-addresses">
          <div class="bg-white bg-opacity-90 rounded-lg p-3 shadow-sm">
            <h2 class="text-gray-500 text-sm mb-2">Unloading point</h2>
            <div v-for="address in unloadingAddresses" :key="address.id" class="font-medium mb-1">
              {{ address.address }}
            </div>
          </div>
        </div>
        
        <!-- Map Info Window -->
        <div v-if="showInfoWindow && selectedMarker" class="absolute bottom-24 left-0 right-0 flex justify-center">
          <MapInfoWindow :title="selectedMarker.title" @close="closeInfoWindow">
            <p>{{ selectedMarker.address }}</p>
            <template #actions>
              <button class="text-blue-500 text-sm font-medium">View Details</button>
            </template>
          </MapInfoWindow>
        </div>
        
        <!-- Progress indicator -->
        <div class="absolute bottom-24 left-0 right-0 flex justify-center">
          <div class="w-16 h-1 bg-blue-500 rounded-full"></div>
        </div>
        
        <!-- Floating action button -->
        <div class="floating-action-button">
          <GradientButton @click="mainActionHandler" class="w-full">
            {{ mainActionButtonText }}
          </GradientButton>
        </div>
      </div>
      
      <!-- Materials section (shown when moving) -->
      <div v-if="isMoving" class="px-4 pt-4">
        <div class="bg-white rounded-lg p-3 shadow-sm">
          <h2 class="text-gray-500 text-sm mb-2">Used materials</h2>
          <div class="materials-list">
            <MaterialCounter 
              v-for="material in materials" 
              :key="material.id"
              :name="material.name"
              :initial-count="material.count"
              @update:count="handleMaterialCountChange"
            />
          </div>
        </div>
      </div>
      
      <!-- Action buttons (additional) -->
      <div v-if="isMoving && !allAddressesVisited" class="p-4 action-buttons">
        <div class="grid grid-cols-2 gap-3">
          <SecondaryButton @click="openAddMaterialModal">
            Add materials
          </SecondaryButton>
          
          <SecondaryButton @click="openUnloadLoadModal">
            Unload/Load
          </SecondaryButton>
          
          <SecondaryButton 
            @click="openChangeMoverModal"
            class="col-span-2"
          >
            Change number of movers
          </SecondaryButton>
        </div>
      </div>
    </main>
    
    <!-- Modals -->
    <ChangeMoversModal
      v-model="showMoversModal"
      :current-movers="numberOfMovers"
      @confirm="handleMoversChange"
    />
    
    <AddMaterialModal
      v-model="showMaterialModal"
      @add="handleAddMaterial"
    />
    
    <UnloadLoadModal
      v-model="showUnloadLoadModal"
      @unload="handleUnload"
      @load="handleLoad"
    />
    
    <div class="footer-container">
      <Footer />
    </div>
  </div>
</template>

<style scoped>
.tracking-view {
  min-height: 100vh;
  padding-top: 55px; /* Height of the header */
  background-color: #f5f5f7;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.map-container {
  position: relative;
  width: 100%;
}

.floating-time-cards {
  position: absolute;
  top: 12px;
  left: 12px;
  right: 12px;
  display: flex;
  z-index: 10;
}

.floating-addresses {
  position: absolute;
  top: 90px;
  left: 12px;
  right: 12px;
  z-index: 10;
}

.floating-action-button {
  position: absolute;
  bottom: 16px;
  left: 16px;
  right: 16px;
  z-index: 10;
}

.footer-container {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
}

.materials-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.action-buttons {
  display: flex;
  flex-direction: column;
}
</style>
