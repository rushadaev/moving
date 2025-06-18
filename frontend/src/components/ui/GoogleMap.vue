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

const emit = defineEmits(['route-calculated']);

const apiKey = import.meta.env.VITE_GOOGLE_MAPS_API_KEY;
const mapContainer = ref<HTMLElement | null>(null);
let map: google.maps.Map | null = null;
let mapMarkers: google.maps.Marker[] = [];
let routePolyline: google.maps.Polyline | null = null;

// Initialize the map
const initMap = async () => {
  if (!mapContainer.value) return;
  
  try {
    const loader = new Loader({
      apiKey,
      version: 'weekly',
      libraries: ['places', 'geometry', 'drawing', 'routes'],
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
    
    // Calculate and display route if showRoute is true and origin/destination are provided
    if (props.showRoute && props.origin && props.destination) {
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

// Calculate and display route using new Routes API
const calculateRoute = async () => {
  if (!props.origin || !props.destination || !map) return;
  
  try {
    // Clear previous routes
    if (routePolyline) {
      routePolyline.setMap(null);
    }
    
    // Use the new Routes API
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    headers.append('X-Goog-Api-Key', apiKey);
    headers.append('X-Goog-FieldMask', 'routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline');
    
    const requestBody = {
      origin: {
        location: {
          latLng: {
            latitude: props.origin.lat,
            longitude: props.origin.lng
          }
        }
      },
      destination: {
        location: {
          latLng: {
            latitude: props.destination.lat,
            longitude: props.destination.lng
          }
        }
      },
      travelMode: 'DRIVE',
      routingPreference: 'TRAFFIC_AWARE',
      computeAlternativeRoutes: false,
      languageCode: 'en-US',
      units: 'METRIC'
    };
    
    const response = await fetch('https://routes.googleapis.com/directions/v2:computeRoutes', {
      method: 'POST',
      headers: headers,
      body: JSON.stringify(requestBody)
    });
    
    if (response.ok) {
      const data = await response.json();
      
      if (data.routes && data.routes.length > 0) {
        const route = data.routes[0];
        
        // Decode and display the polyline
        if (route.polyline?.encodedPolyline) {
          const decodedPath = google.maps.geometry.encoding.decodePath(route.polyline.encodedPolyline);
          
          // Create a new polyline
          if (routePolyline) {
            routePolyline.setMap(null);
          }
          
          routePolyline = new google.maps.Polyline({
            path: decodedPath,
            geodesic: true,
            strokeColor: '#5D87EE',
            strokeOpacity: 0.8,
            strokeWeight: 5,
            map: map
          });
          
          // Fit bounds to show the entire route
          const bounds = new google.maps.LatLngBounds();
          decodedPath.forEach(point => bounds.extend(point));
          map.fitBounds(bounds);
        }
        
        // Extract route information
        const distance = route.distanceMeters || 0;
        const duration = parseInt(route.duration?.replace('s', '') || '0');
        
        emit('route-calculated', {
          distance: distance,
          duration: duration,
          distanceText: `${(distance / 1000).toFixed(1)} km`,
          durationText: duration > 3600 
            ? `${Math.floor(duration / 3600)}h ${Math.round((duration % 3600) / 60)}m`
            : `${Math.round(duration / 60)}m`
        });
      }
    } else {
      const errorData = await response.json();
      console.error('Routes API error:', errorData);
      
      // If Routes API is not enabled, fall back to simple calculation
      calculateFallbackRoute();
    }
  } catch (error) {
    console.error('Error with Routes API:', error);
    calculateFallbackRoute();
  }
};

// Fallback route calculation
const calculateFallbackRoute = () => {
  if (!props.origin || !props.destination || !map) return;
  
  if (window.google && window.google.maps && window.google.maps.geometry) {
    const origin = new google.maps.LatLng(props.origin.lat, props.origin.lng);
    const destination = new google.maps.LatLng(props.destination.lat, props.destination.lng);
    const distance = google.maps.geometry.spherical.computeDistanceBetween(origin, destination);
    
    // Estimate time based on average speed (50 km/h in city)
    const estimatedDuration = (distance / 1000) / 50 * 60 * 60; // seconds
    
    // Clear previous polylines
    if (routePolyline) {
      routePolyline.setMap(null);
    }
    
    // Draw a simple polyline between points
    routePolyline = new google.maps.Polyline({
      path: [props.origin, props.destination],
      geodesic: true,
      strokeColor: '#5D87EE',
      strokeOpacity: 0.6,
      strokeWeight: 4,
      map: map
    });
    
    emit('route-calculated', {
      distance: distance, // Distance in meters
      duration: estimatedDuration, // Duration in seconds
      distanceText: `${(distance / 1000).toFixed(1)} km (approx)`,
      durationText: `~${Math.round(estimatedDuration / 60)} min`,
      isEstimate: true
    });
  }
};

// Watch for changes in props
watchEffect(() => {
  if (map) {
    // Update map center and zoom
    map.setCenter(props.center);
    map.setZoom(props.zoom);
    
    // Recalculate route if origin or destination changes
    if (props.showRoute && props.origin && props.destination) {
      calculateRoute();
    }
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
  if (routePolyline) {
    routePolyline.setMap(null);
    routePolyline = null;
  }
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
  height: 100%;
  border-radius: 8px;
  overflow: hidden;
}
</style> 