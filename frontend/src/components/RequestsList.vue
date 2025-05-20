<script setup lang="ts">
import RequestItem from './RequestItem.vue'
import { useRouter } from 'vue-router'
import { ref, computed } from 'vue'
import { useRequestsStore, type Request } from '../stores/requests'

const props = defineProps<{
  requests: Request[]
}>()

const router = useRouter()
const requestsStore = useRequestsStore()
const loading = ref(false)

const formattedRequests = computed(() => {
  return props.requests.map(request => {
    // Find loading and unloading addresses
    const loadingAddress = request.addresses.find(addr => addr.type === 'loading')?.address || 'No loading address'
    const unloadingAddress = request.addresses.find(addr => addr.type === 'unloading')?.address || 'No unloading address'
    
    // Format date for display
    const date = new Date(request.departure_time)
    const formattedDate = date.toLocaleDateString()
    const formattedTime = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    
    return {
      id: request.id,
      requestNumber: `Request #${request.id}`,
      price: `$${request.hourly_rate}/hr`,
      type: request.property_type,
      time: `${formattedDate} | ${formattedTime}`,
      loadingAddress,
      unloadingAddress,
      status: request.status || 'pending',
      fullRequest: request // Store the full request data
    }
  })
})

// Navigate to details view when a request is clicked
const showRequestDetails = async (item) => {
  if (!item.id) {
    console.error("Request has no ID")
    return
  }
  
  console.log(`Attempting to navigate to details for request ID: ${item.id}`, item)
  
  try {
    loading.value = true
    // Navigate directly without waiting for API call
    router.push({
      path: '/details',
      query: { id: item.id.toString() }
    })
    
    // Load the request data in the background
    await requestsStore.getRequestById(item.id)
  } catch (err) {
    console.error('Error navigating to details:', err)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="w-full">
    <h1 class="text-2xl font-bold mb-4 ml-2">Requests</h1>
    
    <div v-if="loading" class="text-center p-6">
      <p>Loading request details...</p>
    </div>
    
    <div v-else-if="formattedRequests.length === 0" class="text-center p-6">
      <p>You don't have any moving requests yet.</p>
      <button 
        @click="router.push('/details')" 
        class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
      >
        Create New Request
      </button>
    </div>
    
    <div v-else class="grid gap-5 w-full justify-center items-center grid-cols-1">
      <div 
        v-for="item in formattedRequests" 
        :key="item.id"
        @click="showRequestDetails(item)"
        class="cursor-pointer"
      >
        <RequestItem 
          :item="item"
          :disabled="loading"
        />
      </div>
    </div>
  </div>
</template>
