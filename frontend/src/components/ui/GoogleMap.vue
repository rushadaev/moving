<template>
  <div class="google-map-container" ref="mapContainer"></div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watchEffect } from 'vue';
import { Loader } from '@googlemaps/js-api-loader';

const props = defineProps({
  height: {
    type: [String, Number],
    default: '300px',
  },
  center: {
    type: Object,
    default: () => ({ lat: 40.7128, lng: -74.0060 }), // Default to NYC
  },
  zoom: {
    type: Number,
    default: 12,
  },
  markers: {
    type: Array,
    default: () => [],
  },
  showRoute: {
    type: Boolean,
    default: false,
  },
  origin: {
    type: Object,
    default: null,
  },
  destination: {
    type: Object,
    default: null,
  },
});

const apiKey = import.meta.env.VITE_GOOGLE_MAPS_API_KEY;
const mapContainer = ref<HTMLElement | null>(null);
let map: google.maps.Map | null = null;
let directionsService: google.maps.DirectionsService | null = null;
let directionsRenderer: google.maps.DirectionsRenderer | null = null;
let mapMarkers: google.maps.Marker[] = [];

// Initialize the map
const initMap = async () => {
  if (!mapContainer.value) return;
  
  try {
    const loader = new Loader({
      apiKey,
      version: 'weekly',
      libraries: ['places'],
    });
    
    // Load the Google Maps API
    const googleMaps = await loader.load();
    
    // Create the map
    map = new googleMaps.maps.Map(mapContainer.value, {
      center: props.center,
      zoom: props.zoom,
      disableDefaultUI: true,
      zoomControl: true,
      mapTypeControl: false,
      streetViewControl: false,
      rotateControl: false,
      fullscreenControl: false,
      styles: [
        {
          featureType: 'poi',
          elementType: 'labels',
          stylers: [{ visibility: 'off' }]
        }
      ]
    });
    
    // Create directions service and renderer if needed
    if (props.showRoute) {
      directionsService = new googleMaps.maps.DirectionsService();
      directionsRenderer = new googleMaps.maps.DirectionsRenderer({
        map,
        suppressMarkers: true,
        polylineOptions: {
          strokeColor: '#5D87EE',
          strokeWeight: 5,
        }
      });
      
      // Calculate and display route if origin and destination are provided
      calculateRoute();
    }
    
    // Add markers
    addMarkers(googleMaps);
    
  } catch (error) {
    console.error('Error loading Google Maps:', error);
  }
};

// Add markers to the map
const addMarkers = (googleMaps) => {
  if (!map) return;
  
  // Clear existing markers
  clearMarkers();
  
  // Add new markers
  props.markers.forEach((marker: any) => {
    let markerIcon = marker.icon;
    
    // If marker has a basic icon URL, enhance it with size and anchor
    if (markerIcon && markerIcon.url) {
      markerIcon = {
        ...markerIcon,
        scaledSize: new googleMaps.maps.Size(32, 32),
        anchor: new googleMaps.maps.Point(16, 32)
      };
    }
    
    const newMarker = new googleMaps.maps.Marker({
      position: marker.position,
      map,
      title: marker.title,
      icon: markerIcon,
    });
    
    // Add click event listener if needed
    newMarker.addListener('click', () => {
      const event = new CustomEvent('marker-click', { 
        detail: marker 
      });
      mapContainer.value?.dispatchEvent(event);
    });
    
    mapMarkers.push(newMarker);
  });
};

// Clear all markers from the map
const clearMarkers = () => {
  mapMarkers.forEach(marker => marker.setMap(null));
  mapMarkers = [];
};

// Calculate and display route
const calculateRoute = () => {
  if (!directionsService || !directionsRenderer || !props.origin || !props.destination) return;
  
  directionsService.route({
    origin: props.origin,
    destination: props.destination,
    travelMode: google.maps.TravelMode.DRIVING,
  }, (response, status) => {
    if (status === google.maps.DirectionsStatus.OK) {
      directionsRenderer.setDirections(response);
    } else {
      console.error(`Directions request failed: ${status}`);
    }
  });
};

// Watch for changes in props
watchEffect(() => {
  if (map) {
    // Update map center and zoom
    map.setCenter(props.center);
    map.setZoom(props.zoom);
  }
});

onMounted(() => {
  initMap();
  
  // Listen for marker click events
  mapContainer.value?.addEventListener('marker-click', (e: any) => {
    const marker = e.detail;
    const customEvent = new CustomEvent('marker-click', { 
      detail: marker 
    });
    document.dispatchEvent(customEvent);
  });
});

onUnmounted(() => {
  // Clean up
  clearMarkers();
  mapContainer.value?.removeEventListener('marker-click', () => {});
  document.removeEventListener('marker-click', () => {});
});

// Expose events to parent components
defineExpose({
  mapContainer
});
</script>

<style scoped>
.google-map-container {
  width: 100%;
  height: v-bind('typeof props.height === "string" ? props.height : props.height + "px"');
  border-radius: 8px;
  overflow: hidden;
}
</style> 