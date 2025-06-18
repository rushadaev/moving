# Backend Updates Required - June 17, 2025

## 1. Distance Field Update
The frontend now calculates and sends distance in **miles** (not kilometers) when:
- Creating a new request via `CreateRequestModal`
- Adding/removing unloading points in `RouteView`

### Expected Request Format:
```json
{
  "addresses": [...],
  "distance": 12.5  // Distance in miles, rounded to 1 decimal place
}
```

Please ensure the backend:
- Accepts `distance` field in request create/update endpoints
- Stores distance as a decimal/float field
- Returns distance in the request data

## 2. Address Types
The frontend handles three types of addresses:
- `loading` - Pickup location (only one per request)
- `unloading` - Primary delivery location (first unloading point)
- `intermediate` - Additional stops (second and subsequent unloading points)

### Important Notes:
- When filtering addresses, we look for both `unloading` AND `intermediate` types
- The frontend saves the first unloading point as type `unloading`, and additional points as `intermediate`
- The order field indicates the sequence: loading (0), unloading (1), intermediate (2, 3, etc.)

## 3. Address Update Endpoint
When addresses are updated in RouteView, the frontend sends:
```json
{
  "addresses": [
    {
      "address": "123 Main St, New York, NY",
      "latitude": "40.7128",
      "longitude": "-74.0060",
      "type": "loading",
      "order": 0
    },
    {
      "address": "456 Park Ave, New York, NY",
      "latitude": "40.7527",
      "longitude": "-73.9772",
      "type": "unloading",
      "order": 1
    },
    {
      "address": "789 Broadway, New York, NY",
      "latitude": "40.7589",
      "longitude": "-73.9851",
      "type": "intermediate",
      "order": 2
    }
  ],
  "distance": 15.3  // Total route distance in miles
}
```

## 4. Geocoding Integration
The frontend now:
- Geocodes all addresses before sending to backend
- Sends formatted addresses from Google's geocoding service
- Includes latitude/longitude for all addresses

Make sure the backend:
- Accepts and stores the formatted addresses
- Stores latitude/longitude as strings or decimals
- Can handle addresses with special characters from Google's formatting

## 5. Request Status Flow
Current status values used by frontend:
- `pending` - Initial state
- `confirmed` - After confirmation
- `active` - When moving starts
- `break` - During a break
- `completed` - When request is finished

## 6. Movers Count Update
The frontend updates movers count separately via `updateMoversCount` which sends:
```json
{
  "movers_count": 3
}
```

## 7. Google Maps API Keys
Frontend uses Google Maps APIs for:
- Geocoding API - Converting addresses to coordinates
- Routes API (preferred) or Directions API (fallback) - Calculating distances
- Maps JavaScript API - Displaying maps

Ensure your API keys have access to these services.

## 8. Future Considerations
- Moving history endpoint - Currently using mock data in `MovingHistoryView`
- Real-time tracking updates - For live location updates during moves
- Webhook for status changes - To notify frontend of backend-initiated changes

## Questions for Backend Team:
1. Is there a maximum number of intermediate addresses allowed?
2. Should we validate minimum/maximum distance values?
3. Do you need duration/time estimates along with distance?
4. Should we store the full route polyline for visualization?

---
*Last updated: June 17, 2025*