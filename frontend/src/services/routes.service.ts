interface RouteInfo {
  distance: number; // in meters
  duration: number; // in seconds
  polyline?: string;
}

interface LatLng {
  lat: number;
  lng: number;
}

class RoutesService {
  private apiKey: string;

  constructor() {
    this.apiKey = import.meta.env.VITE_GOOGLE_MAPS_API_KEY || '';
  }

  async calculateRoute(origin: LatLng, destination: LatLng, waypoints?: LatLng[]): Promise<RouteInfo | null> {
    if (!this.apiKey) {
      console.error('Google Maps API key not configured');
      return this.calculateStraightLineDistance(origin, destination);
    }

    // Try Routes API first
    const routesApiResult = await this.tryRoutesApi(origin, destination, waypoints);
    if (routesApiResult) return routesApiResult;

    // Fallback to Directions API
    const directionsApiResult = await this.tryDirectionsApi(origin, destination, waypoints);
    if (directionsApiResult) return directionsApiResult;

    // Final fallback to straight-line distance
    return this.calculateStraightLineDistance(origin, destination);
  }

  private async tryRoutesApi(origin: LatLng, destination: LatLng, waypoints?: LatLng[]): Promise<RouteInfo | null> {
    try {
      const routeRequest: any = {
        origin: {
          location: {
            latLng: {
              latitude: origin.lat,
              longitude: origin.lng
            }
          }
        },
        destination: {
          location: {
            latLng: {
              latitude: destination.lat,
              longitude: destination.lng
            }
          }
        },
        travelMode: 'DRIVE',
        routingPreference: 'TRAFFIC_UNAWARE',
        computeAlternativeRoutes: false
      };

      if (waypoints && waypoints.length > 0) {
        routeRequest.intermediates = waypoints.map(wp => ({
          location: {
            latLng: {
              latitude: wp.lat,
              longitude: wp.lng
            }
          }
        }));
      }

      const response = await fetch('https://routes.googleapis.com/directions/v2:computeRoutes', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Goog-Api-Key': this.apiKey,
          'X-Goog-FieldMask': 'routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline'
        },
        body: JSON.stringify(routeRequest)
      });

      if (response.ok) {
        const data = await response.json();
        if (data.routes && data.routes.length > 0) {
          const route = data.routes[0];
          return {
            distance: route.distanceMeters || 0,
            duration: parseInt(route.duration?.replace('s', '') || '0'),
            polyline: route.polyline?.encodedPolyline
          };
        }
      }
    } catch (error) {
      console.error('Routes API error:', error);
    }
    return null;
  }

  private async tryDirectionsApi(origin: LatLng, destination: LatLng, waypoints?: LatLng[]): Promise<RouteInfo | null> {
    try {
      let url = `https://maps.googleapis.com/maps/api/directions/json?origin=${origin.lat},${origin.lng}&destination=${destination.lat},${destination.lng}&mode=driving&key=${this.apiKey}`;
      
      if (waypoints && waypoints.length > 0) {
        const waypointStr = waypoints.map(wp => `${wp.lat},${wp.lng}`).join('|');
        url += `&waypoints=${waypointStr}`;
      }

      const response = await fetch(url);
      const data = await response.json();

      if (data.status === 'OK' && data.routes.length > 0) {
        const route = data.routes[0];
        const leg = route.legs[0];
        
        return {
          distance: leg.distance.value,
          duration: leg.duration.value,
          polyline: route.overview_polyline?.points
        };
      }
    } catch (error) {
      console.error('Directions API error:', error);
    }
    return null;
  }

  private calculateStraightLineDistance(origin: LatLng, destination: LatLng): RouteInfo {
    const R = 6371e3; // Earth's radius in meters
    const φ1 = origin.lat * Math.PI / 180;
    const φ2 = destination.lat * Math.PI / 180;
    const Δφ = (destination.lat - origin.lat) * Math.PI / 180;
    const Δλ = (destination.lng - origin.lng) * Math.PI / 180;

    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ/2) * Math.sin(Δλ/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distance = R * c;

    // Estimate duration based on average speed of 40 km/h
    const avgSpeedMs = 40 * 1000 / 3600;
    const duration = Math.round(distance / avgSpeedMs);

    return { distance: Math.round(distance), duration };
  }
}

export default new RoutesService();