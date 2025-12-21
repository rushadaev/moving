import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useAuthStore } from './auth'

const apiURL = import.meta.env.VITE_API_URL || '/api'

export interface Address {
  id?: number
  address: string
  type: 'loading' | 'intermediate' | 'unloading'
  location_type?: 'apartment' | 'storage' | 'house' | 'office' | 'garage'
  order: number
  latitude: number
  longitude: number
}

export interface Material {
  id?: number
  name: string
  quantity: number
}

export interface Request {
  id?: number
  property_type: string
  square_feet: number
  additional_objects?: string[]
  movers_count: number
  hourly_rate: number
  departure_time: string
  labor_included: boolean
  package_type: string
  status?: string
  addresses: Address[]
  materials: Material[]
  created_at?: string
  updated_at?: string
}

export const useRequestsStore = defineStore('requests', () => {
  const authStore = useAuthStore()
  
  const requests = ref<Request[]>([])
  const selectedRequest = ref<Request | null>(null)
  const loading = ref(false)
  const error = ref('')
  
  // Computed property to get the headers with auth token
  const authHeaders = computed(() => ({
    'Authorization': `Bearer ${authStore.token}`,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }))
  
  async function fetchRequests() {
    if (!authStore.isAuthenticated) return
    
    loading.value = true
    error.value = ''
    
    try {
      const response = await fetch(`${apiURL}/v1/requests/user`, {
        headers: authHeaders.value
      })
      
      if (response.ok) {
        const data = await response.json()
        requests.value = data.data || []
      } else {
        error.value = 'Failed to fetch requests'
      }
    } catch (err) {
      error.value = 'Error fetching requests. Please try again.'
      console.error('Fetch requests error:', err)
    } finally {
      loading.value = false
    }
  }
  
  // Add this function to ensure request data has required properties
  function sanitizeRequest(request: any): Request {
    // Make sure we have valid addresses
    if (!request.addresses || !Array.isArray(request.addresses) || request.addresses.length === 0) {
      console.warn('Request has no addresses, adding default addresses')
      request.addresses = [
        {
          address: 'Loading address not specified',
          type: 'loading',
          order: 0,
          latitude: 40.7128, // Default to NYC
          longitude: -74.0060
        },
        {
          address: 'Unloading address not specified',
          type: 'unloading',
          order: 1,
          latitude: 40.7128,
          longitude: -74.0060
        }
      ]
    }
    
    // Make sure we have valid materials
    if (!request.materials || !Array.isArray(request.materials)) {
      console.warn('Request has no materials, adding empty array')
      request.materials = []
    }
    
    // Return the sanitized request
    return request as Request
  }
  
  async function getRequestById(id: number) {
    if (!authStore.isAuthenticated) return null
    
    loading.value = true
    error.value = ''
    
    try {
      console.log(`Fetching request with ID: ${id}`)
      
      const response = await fetch(`${apiURL}/v1/requests/${id}`, {
        headers: authHeaders.value
      })
      
      const data = await response.json()
      console.log('Request API response:', data)
      
      if (response.ok) {
        if (!data) {
          console.error('API returned success but no data')
          error.value = 'Received invalid data from server'
          return null
        }
        
        // Sanitize the request data to ensure it has required properties
        selectedRequest.value = sanitizeRequest(data)
        
        console.log('Request loaded successfully:', selectedRequest.value)
        return selectedRequest.value
      } else {
        error.value = data.message || 'Failed to fetch request details'
        console.error('API error:', data)
        return null
      }
    } catch (err) {
      error.value = 'Error fetching request details. Please try again.'
      console.error('Get request by ID error:', err)
      return null
    } finally {
      loading.value = false
    }
  }
  
  async function createRequest(newRequest: Request) {
    if (!authStore.isAuthenticated) return null
    
    loading.value = true
    error.value = ''
    
    try {
      const response = await fetch(`${apiURL}/v1/requests`, {
        method: 'POST',
        headers: authHeaders.value,
        body: JSON.stringify(newRequest)
      })
      
      const data = await response.json()
      
      if (response.ok) {
        // Refresh requests list after creating new request
        await fetchRequests()
        return data
      } else {
        error.value = data.message || 'Failed to create request'
        return null
      }
    } catch (err) {
      error.value = 'Error creating request. Please try again.'
      console.error('Create request error:', err)
      return null
    } finally {
      loading.value = false
    }
  }
  
  async function updateRequest(id: number, updatedRequest: Partial<Request>) {
    if (!authStore.isAuthenticated) return null
    
    loading.value = true
    error.value = ''
    
    try {
      const response = await fetch(`${apiURL}/v1/requests/${id}`, {
        method: 'PUT',
        headers: authHeaders.value,
        body: JSON.stringify(updatedRequest)
      })
      
      const data = await response.json()
      
      if (response.ok) {
        // Refresh requests list and selected request after update
        await fetchRequests()
        if (selectedRequest.value?.id === id) {
          await getRequestById(id)
        }
        return data
      } else {
        error.value = data.message || 'Failed to update request'
        return null
      }
    } catch (err) {
      error.value = 'Error updating request. Please try again.'
      console.error('Update request error:', err)
      return null
    } finally {
      loading.value = false
    }
  }
  
  async function updateRequestStatus(id: number, status: string) {
    console.log(`Updating request ${id} status to: ${status}`)
    return updateRequest(id, { status })
  }
  
  async function updateMoversCount(id: number, movers_count: number) {
    return updateRequest(id, { movers_count })
  }
  
  async function deleteRequest(id: number) {
    if (!authStore.isAuthenticated) return false
    
    loading.value = true
    error.value = ''
    
    try {
      const response = await fetch(`${apiURL}/v1/requests/${id}`, {
        method: 'DELETE',
        headers: authHeaders.value
      })
      
      if (response.ok) {
        // Refresh requests list after deletion
        await fetchRequests()
        // If the deleted request was selected, clear selection
        if (selectedRequest.value?.id === id) {
          selectedRequest.value = null
        }
        return true
      } else {
        const data = await response.json()
        error.value = data.message || 'Failed to delete request'
        return false
      }
    } catch (err) {
      error.value = 'Error deleting request. Please try again.'
      console.error('Delete request error:', err)
      return false
    } finally {
      loading.value = false
    }
  }
  
  // Initialize by fetching requests if user is authenticated
  if (authStore.isAuthenticated) {
    fetchRequests()
  }
  
  return {
    requests,
    selectedRequest,
    loading,
    error,
    fetchRequests,
    getRequestById,
    createRequest,
    updateRequest,
    updateRequestStatus,
    updateMoversCount,
    deleteRequest
  }
}) 