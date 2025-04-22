<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import GoogleMap from '@/components/ui/GoogleMap.vue';
import MapMarker from '@/components/ui/MapMarker.vue';
import MapInfoWindow from '@/components/ui/MapInfoWindow.vue';

const router = useRouter();
const request = ref(null);

// Map data
const mapCenter = ref({ lat: 40.7128, lng: -74.0060 }); // Default to NYC
const mapZoom = ref(12);
const mapMarkers = ref([]);
const selectedMarker = ref(null);
const showInfoWindow = ref(false);
const routeDistance = ref('12.5 km');
const routeDuration = ref('45 min');

onMounted(() => {
  // Get selected request from localStorage
  const selectedRequest = localStorage.getItem('selectedRequest');
  if (selectedRequest) {
    request.value = JSON.parse(selectedRequest);
    
    // Set map markers based on addresses
    if (request.value?.loadingAddress && request.value?.unloadingAddress) {
      mapMarkers.value = [
        {
          position: { lat: 40.7128, lng: -74.0060 }, // Replace with actual geocoded coordinates
          title: 'Loading Point',
          address: request.value.loadingAddress,
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
          address: request.value.unloadingAddress,
          icon: {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#F97066">
                <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z"/>
              </svg>
            `)
          }
        }
      ];
      
      // Set map center midway between origin and destination
      const lat = (mapMarkers.value[0].position.lat + mapMarkers.value[1].position.lat) / 2;
      const lng = (mapMarkers.value[0].position.lng + mapMarkers.value[1].position.lng) / 2;
      mapCenter.value = { lat, lng };
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
</script>

<template>
  <div class="route-view">
    <Header :title="request ? `Route for ${request.requestNumber}` : 'Route'" />
    
    <main class="p-0 pb-24">
      <!-- Map with floating elements -->
      <div class="map-container relative" style="height: calc(100vh - 150px)">
        <GoogleMap 
          :height="'100%'"
          :center="mapCenter"
          :zoom="mapZoom"
          :markers="mapMarkers"
          :showRoute="true"
          :origin="mapMarkers[0]?.position"
          :destination="mapMarkers[1]?.position"
        />
        
        <!-- Floating route information -->
        <div class="floating-route-info">
          <div class="bg-[var(--color-background)] rounded-lg p-3 shadow-sm">
            <h2 class="text-[var(--color-text)] font-semibold mb-2">Route Details</h2>
            <div class="grid grid-cols-2 gap-4 mb-2">
              <div>
                <span class="text-[var(--color-text)] opacity-60 text-sm">Distance</span>
                <p class="text-[var(--color-text)] font-medium">{{ routeDistance }}</p>
              </div>
              <div>
                <span class="text-[var(--color-text)] opacity-60 text-sm">Estimated time</span>
                <p class="text-[var(--color-text)] font-medium">{{ routeDuration }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Floating addresses -->
        <div class="floating-addresses">
          <div class="bg-[var(--color-background)] rounded-lg p-3 shadow-sm">
            <div class="mb-3">
              <span class="text-[var(--color-text)] opacity-60 text-sm">From</span>
              <p class="text-[var(--color-text)] font-medium">{{ request?.loadingAddress || 'Loading address' }}</p>
            </div>
            <div>
              <span class="text-[var(--color-text)] opacity-60 text-sm">To</span>
              <p class="text-[var(--color-text)] font-medium">{{ request?.unloadingAddress || 'Unloading address' }}</p>
            </div>
          </div>
        </div>
        
        <!-- Map Info Window -->
        <div v-if="showInfoWindow && selectedMarker" class="absolute bottom-24 left-0 right-0 flex justify-center">
          <MapInfoWindow :title="selectedMarker.title" @close="closeInfoWindow">
            <p>{{ selectedMarker.address }}</p>
          </MapInfoWindow>
        </div>
      </div>
    </main>
    
    <Footer />
  </div>
</template>

<style scoped>
.route-view {
  min-height: 100vh;
  padding-top: 55px; /* Height of the header */
  background-color: var(--color-background-soft);
}

.map-container {
  position: relative;
  width: 100%;
}

.floating-route-info {
  position: absolute;
  top: 12px;
  left: 12px;
  right: 12px;
  z-index: 10;
}

.floating-addresses {
  position: absolute;
  top: 130px;
  left: 12px;
  right: 12px;
  z-index: 10;
}
</style> 