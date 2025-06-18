<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useRequestsStore } from '@/stores/requests';
import { useAuthStore } from '@/stores/auth';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import GoogleMap from '@/components/ui/GoogleMap.vue';
import MapMarker from '@/components/ui/MapMarker.vue';
import GradientButton from '@/components/ui/GradientButton.vue';
import ChangeMoversModal from '@/components/modals/ChangeMoversModal.vue';
import AddUnloadingPointModal from '@/components/modals/AddUnloadingPointModal.vue';
import ApprovalActionModal from '@/components/modals/ApprovalActionModal.vue';
import geocodingService from '@/services/geocoding.service';
import routesService from '@/services/routes.service';

const router = useRouter();
const requestsStore = useRequestsStore();
const authStore = useAuthStore();

// State management
const loading = ref(false);
const error = ref('');
const mapCenter = ref({ lat: 40.7128, lng: -74.0060 });
const mapZoom = ref(12);
const routeDistance = ref('--');
const routeDuration = ref('--:--');
const bottomOverlayExpanded = ref(true);

// Modal states
const showMoversModal = ref(false);
const showAddPointModal = ref(false);
const showApprovalModal = ref(false);
const approvalAction = ref<'movers' | 'point' | null>(null);
const pendingMoversCount = ref(2);

// Request data
const request = computed(() => requestsStore.selectedRequest);
const executorName = ref('Name and surname');
const numberOfMovers = ref(2);

// Computed properties
const departureTime = computed(() => {
  if (!request.value?.departure_time) return '--:--';
  const date = new Date(request.value.departure_time);
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
});

const travelTime = computed(() => routeDuration.value);

const unloadingPoints = ref<Array<{
  id: number;
  address: string;
  latitude?: number;
  longitude?: number;
}>>([]);

const mapMarkers = computed(() => {
  const markers = [];
  
  // Add loading point if exists
  const loadingAddress = request.value?.addresses?.find(addr => addr.type === 'loading');
  if (loadingAddress) {
    markers.push({
      position: { 
        lat: parseFloat(loadingAddress.latitude), 
        lng: parseFloat(loadingAddress.longitude) 
      },
      title: 'Loading Point',
      icon: {
        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#5D87EE">
            <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z"/>
          </svg>
        `)
      },
      address: loadingAddress.address,
      type: 'loading'
    });
  }
  
  // Add unloading points
  unloadingPoints.value.forEach(point => {
    if (point.latitude && point.longitude) {
      markers.push({
        position: { 
          lat: point.latitude, 
          lng: point.longitude 
        },
        title: 'Unloading Point',
        icon: {
          url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#F97066">
              <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z"/>
            </svg>
          `)
        },
        address: point.address,
        type: 'unloading'
      });
    }
  });
  
  return markers;
});

const routeOrigin = computed(() => {
  const loadingAddress = request.value?.addresses?.find(addr => addr.type === 'loading');
  if (!loadingAddress) return null;
  
  return {
    lat: parseFloat(loadingAddress.latitude),
    lng: parseFloat(loadingAddress.longitude)
  };
});

const routeDestination = computed(() => {
  if (unloadingPoints.value.length === 0) return null;
  const lastPoint = unloadingPoints.value[unloadingPoints.value.length - 1];
  
  if (!lastPoint.latitude || !lastPoint.longitude) return null;
  
  return {
    lat: lastPoint.latitude,
    lng: lastPoint.longitude
  };
});

// Methods
const removeUnloadingPoint = async (id: number) => {
  unloadingPoints.value = unloadingPoints.value.filter(point => point.id !== id);
  await updateAddressesOnBackend();
  calculateRoute();
};

const handleAddPoint = async (address: string) => {
  const geocodeResult = await geocodingService.geocodeAddress(address);
  
  if (geocodeResult) {
    const newPoint = {
      id: Date.now(),
      address: geocodeResult.formattedAddress,
      latitude: geocodeResult.latitude,
      longitude: geocodeResult.longitude
    };
    
    unloadingPoints.value.push(newPoint);
    await updateAddressesOnBackend();
    await calculateRoute();
    
    // Close the modal after successful addition
    showAddPointModal.value = false;
  } else {
    error.value = 'Could not find location for this address';
  }
};

const openChangeMoversModal = () => {
  approvalAction.value = 'movers';
  showApprovalModal.value = true;
};

const openAddPointModal = () => {
  approvalAction.value = 'point';
  showApprovalModal.value = true;
};

const handleApprovalConfirm = () => {
  if (approvalAction.value === 'movers') {
    showMoversModal.value = true;
  } else if (approvalAction.value === 'point') {
    showAddPointModal.value = true;
  }
  approvalAction.value = null;
};

const handleMoversChange = async (newCount: number) => {
  numberOfMovers.value = newCount;
  pendingMoversCount.value = newCount;
  
  if (request.value?.id) {
    await requestsStore.updateMoversCount(request.value.id, newCount);
  }
  
  // Close the modal
  showMoversModal.value = false;
};

const viewMovingHistory = () => {
  router.push('/moving-history');
};

const updateAddressesOnBackend = async () => {
  if (!request.value?.id) return;
  
  try {
    // Prepare addresses array with both loading and unloading points
    const addresses = [];
    
    // Add loading point if exists
    const loadingAddress = request.value.addresses?.find(addr => addr.type === 'loading');
    if (loadingAddress) {
      addresses.push({
        address: loadingAddress.address,
        latitude: loadingAddress.latitude,
        longitude: loadingAddress.longitude,
        type: 'loading',
        order: 0
      });
    }
    
    // Add all unloading points
    unloadingPoints.value.forEach((point, index) => {
      addresses.push({
        address: point.address,
        latitude: point.latitude?.toString() || '',
        longitude: point.longitude?.toString() || '',
        type: index === 0 ? 'unloading' : 'intermediate', // First one is unloading, rest are intermediate
        order: index + 1
      });
    });
    
    console.log('Updating addresses on backend:', addresses);
    
    // Calculate total distance in miles
    let totalDistanceMiles = 0;
    if (routeOrigin.value && unloadingPoints.value.length > 0) {
      // Calculate full route distance through all points
      const allPoints = [routeOrigin.value];
      unloadingPoints.value.forEach(point => {
        if (point.latitude && point.longitude) {
          allPoints.push({ lat: point.latitude, lng: point.longitude });
        }
      });
      
      // Calculate distance between consecutive points
      for (let i = 0; i < allPoints.length - 1; i++) {
        const routeInfo = await routesService.calculateRoute(allPoints[i], allPoints[i + 1]);
        if (routeInfo) {
          totalDistanceMiles += routeInfo.distance / 1609.34; // Convert meters to miles
        }
      }
    }
    
    // Update the request with new addresses and distance
    const updateData = { 
      addresses,
      distance: Math.round(totalDistanceMiles * 10) / 10 // Round to 1 decimal place
    };
    
    console.log('Updating with distance (miles):', updateData.distance);
    
    const result = await requestsStore.updateRequest(request.value.id, updateData);
    
    if (result) {
      // Refresh the request data to ensure we have the latest from backend
      await requestsStore.getRequestById(request.value.id);
    }
  } catch (err) {
    console.error('Failed to update addresses:', err);
    error.value = 'Failed to update addresses on server';
  }
};

const calculateRoute = async () => {
  if (!routeOrigin.value || !routeDestination.value) {
    routeDistance.value = '--';
    routeDuration.value = '--:--';
    return;
  }

  const routeInfo = await routesService.calculateRoute(
    routeOrigin.value,
    routeDestination.value
  );

  if (routeInfo) {
    const distanceKm = (routeInfo.distance / 1000).toFixed(1);
    routeDistance.value = `${distanceKm} km`;
    
    const durationMinutes = Math.round(routeInfo.duration / 60);
    const hours = Math.floor(durationMinutes / 60);
    const minutes = durationMinutes % 60;
    routeDuration.value = hours > 0 ? `${hours}h ${minutes}m` : `${minutes}m`;
  }
};

// Handle route calculation from GoogleMap
const handleRouteCalculated = (routeInfo: any) => {
  if (routeInfo && routeInfo.duration && routeInfo.distance) {
    const durationInMinutes = Math.round(routeInfo.duration / 60);
    const hours = Math.floor(durationInMinutes / 60);
    const minutes = durationInMinutes % 60;
    routeDuration.value = hours > 0 ? `${hours}h ${minutes}m` : `${minutes}m`;
    
    const distanceInKm = (routeInfo.distance / 1000).toFixed(1);
    routeDistance.value = `${distanceInKm} km`;
  }
};

onMounted(async () => {
  if (!authStore.isAuthenticated) {
    router.push('/');
    return;
  }
  
  loading.value = true;
  
  try {
    // Get request ID from query params
    const requestId = Number(router.currentRoute.value.query.id);
    
    if (!requestId && !requestsStore.selectedRequest) {
      router.push('/requests');
      return;
    }
    
    if (requestId && (!requestsStore.selectedRequest || requestsStore.selectedRequest.id !== requestId)) {
      await requestsStore.getRequestById(requestId);
    }
    
    if (!requestsStore.selectedRequest) {
      router.push('/requests');
      return;
    }
    
    // Initialize unloading points from request
    console.log('All addresses from request:', request.value?.addresses);
    request.value?.addresses?.forEach((addr, index) => {
      console.log(`Address ${index}: type="${addr.type}", address="${addr.address}"`);
    });
    const unloadingAddresses = request.value?.addresses?.filter(addr => 
      addr.type === 'unloading' || addr.type === 'intermediate'
    ) || [];
    console.log('Filtered unloading addresses:', unloadingAddresses);
    unloadingPoints.value = unloadingAddresses.map((addr, index) => ({
      id: addr.id || Date.now() + index,
      address: addr.address,
      latitude: parseFloat(addr.latitude),
      longitude: parseFloat(addr.longitude)
    }));
    console.log('Unloading points after mapping:', unloadingPoints.value);
    
    // Set number of movers
    numberOfMovers.value = request.value?.movers_count || 2;
    pendingMoversCount.value = numberOfMovers.value;
    
    // Set map center
    if (mapMarkers.value.length > 0) {
      const bounds = {
        north: -90,
        south: 90,
        east: -180,
        west: 180
      };
      
      mapMarkers.value.forEach(marker => {
        bounds.north = Math.max(bounds.north, marker.position.lat);
        bounds.south = Math.min(bounds.south, marker.position.lat);
        bounds.east = Math.max(bounds.east, marker.position.lng);
        bounds.west = Math.min(bounds.west, marker.position.lng);
      });
      
      mapCenter.value = {
        lat: (bounds.north + bounds.south) / 2,
        lng: (bounds.east + bounds.west) / 2
      };
      
      const latDiff = bounds.north - bounds.south;
      const lngDiff = bounds.east - bounds.west;
      const maxDiff = Math.max(latDiff, lngDiff);
      
      if (maxDiff < 0.01) mapZoom.value = 15;
      else if (maxDiff < 0.05) mapZoom.value = 13;
      else if (maxDiff < 0.1) mapZoom.value = 12;
      else if (maxDiff < 0.5) mapZoom.value = 10;
      else mapZoom.value = 8;
    }
    
    // Calculate initial route
    await calculateRoute();
  } catch (err) {
    console.error('Error loading route view:', err);
    error.value = 'Failed to load route data';
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="route-view">
    <Header title="Tracking" />
    
    <main v-if="loading" class="flex items-center justify-center h-screen">
      <p>Loading route data...</p>
    </main>
    
    <main v-else-if="error" class="p-4">
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <p>{{ error }}</p>
      </div>
      <GradientButton @click="router.push('/requests')">
        Return to Requests
      </GradientButton>
    </main>
    
    <main v-else class="route-content">
      <!-- Map Container -->
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
            :key="marker.title + marker.address"
            :position="marker.position"
            :icon="marker.icon"
          />
        </GoogleMap>
        
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
        
        <!-- Executor Info -->
        <div class="executor-info">
          <div class="executor-details">
            <span class="executor-label">Executor</span>
            <span class="executor-name">{{ executorName }}</span>
          </div>
          <GradientButton :small-button="true" @click="">
            Status
          </GradientButton>
        </div>
        
        <!-- Bottom Overlay with Drag Handle -->
        <div class="bottom-overlay" :class="{ 'collapsed': !bottomOverlayExpanded }">
          <!-- Drag Handle -->
          <div class="drag-handle" @click="bottomOverlayExpanded = !bottomOverlayExpanded">
            <div class="handle-bar"></div>
          </div>
          
          <!-- Content Container -->
          <div class="overlay-content" v-show="bottomOverlayExpanded">
            <!-- Unloading Points -->
            <div class="content-block">
              <h3 class="block-title">Unloading points</h3>
              <div class="points-list" v-if="unloadingPoints.length > 0">
                <div 
                  v-for="point in unloadingPoints" 
                  :key="point.id" 
                  class="point-item"
                >
                  <span class="point-address">{{ point.address }}</span>
                  <button 
                    class="remove-button" 
                    @click="removeUnloadingPoint(point.id)"
                    aria-label="Remove point"
                  >
                    ×
                  </button>
                </div>
              </div>
              <div v-else class="empty-points">
                <p class="empty-text">No unloading points added yet</p>
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
              <GradientButton 
                @click="openAddPointModal"
                :full-width="true"
              >
                Add point
              </GradientButton>
              
              <GradientButton 
                @click="openChangeMoversModal"
                :full-width="true"
                class="secondary-action"
              >
                Change number of movers
              </GradientButton>
              
              <GradientButton 
                @click="viewMovingHistory"
                :full-width="true"
                class="secondary-action"
              >
                View moving history
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
    />
    
    <AddUnloadingPointModal 
      v-model:show="showAddPointModal"
      @add="handleAddPoint"
    />
    
    <ApprovalActionModal 
      v-model:show="showApprovalModal"
      :message="approvalAction === 'movers' 
        ? 'Are you sure that you want to change number of movers?' 
        : 'Are you sure that you want to add a new unloading point?'"
      @confirm="handleApprovalConfirm"
      @cancel="showApprovalModal = false"
    />
    
    <Footer />
  </div>
</template>

<style scoped>
.route-view {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: var(--color-background);
  padding-bottom: 70px;

  /* Override App.vue padding */
  margin-top: -55px;
  margin-bottom: -70px;
  padding-top: 55px;
}

.route-content {
  flex: 1;
  position: relative;
  padding-top: 55px;
  padding-bottom: 70px;
}

.map-container {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}

/* Top Info Cards */
.info-cards {
  position: absolute;
  top: 16px;
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

/* Executor Info */
.executor-info {
  position: absolute;
  top: 100px;
  left: 16px;
  right: 16px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  padding: 12px 16px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  display: flex;
  justify-content: space-between;
  align-items: center;
  z-index: 10;
}

.executor-details {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.executor-label {
  font-size: 12px;
  color: #666666;
}

.executor-name {
  font-size: 16px;
  font-weight: 500;
  color: var(--color-text);
}

/* Bottom Overlay with Drag Handle */
.bottom-overlay {
  position: absolute;
  bottom: 0;
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

.content-block {
  background: var(--color-background-mute);
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.block-title {
  font-size: 14px;
  color: #666666;
  font-weight: 500;
  margin-bottom: 12px;
}

.points-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.point-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  background: var(--color-background);
  border-radius: 8px;
}

.point-address {
  font-size: 16px;
  color: var(--color-text);
  flex: 1;
  margin-right: 12px;
}

.remove-button {
  background: none;
  border: none;
  color: var(--color-text-secondary);
  cursor: pointer;
  padding: 4px 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s;
  font-size: 24px;
  line-height: 1;
  font-weight: 400;
}

.remove-button:hover {
  color: var(--color-error);
}

.empty-points {
  padding: 20px;
  text-align: center;
}

.empty-text {
  color: var(--color-text-secondary);
  font-size: 14px;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.secondary-action {
  background: var(--color-background-mute) !important;
  color: var(--color-text) !important;
  border: 1px solid var(--color-border);
}

.secondary-action:hover {
  background: var(--color-background) !important;
  transform: translateY(-1px);
}

/* Dark mode adjustments */
@media (prefers-color-scheme: dark) {
  .info-card,
  .executor-info {
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
  
  .point-item {
    background: var(--color-background-mute);
  }
  
  .secondary-action {
    background: var(--color-background-soft) !important;
    border-color: var(--color-border);
  }
  
  .secondary-action:hover {
    background: var(--color-background-mute) !important;
  }
}

/* Responsive */
@media (max-width: 640px) {
  .info-cards {
    top: 12px;
    left: 12px;
    right: 12px;
  }
  
  .executor-info {
    top: 96px;
    left: 12px;
    right: 12px;
  }
  
  .info-card,
  .executor-info {
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