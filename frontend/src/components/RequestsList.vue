<script setup lang="ts">
import RequestItem from './RequestItem.vue'
import CreateRequestModal from './modals/CreateRequestModal.vue'
import GradientButton from './ui/GradientButton.vue'
import { useRouter } from 'vue-router'
import { ref, computed } from 'vue'
import { useRequestsStore, type Request } from '../stores/requests'
import { NButton } from 'naive-ui'

const props = defineProps<{
  requests: Request[]
}>()

const router = useRouter()
const requestsStore = useRequestsStore()
const loading = ref(false)
const showCreateModal = ref(false)

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

// Handle when a new request is created
const onRequestCreated = () => {
  // The modal component already refreshes the requests list
  // Just close the modal which is handled by the modal component
  showCreateModal.value = false
}
</script>

<template>
  <div class="requests-list">
    <div class="requests-header">
      <h1 class="page-title">My Requests</h1>
      <GradientButton 
        @click="showCreateModal = true"
        :small-button="true"
      >
        + New Request
      </GradientButton>
    </div>
    
    <div v-if="loading" class="text-center p-6">
      <p>Loading request details...</p>
    </div>
    
    <div v-else-if="formattedRequests.length === 0" class="empty-state">
      <div class="empty-state-content">
        <p class="empty-state-text">You don't have any moving requests yet</p>
        <GradientButton 
          @click="showCreateModal = true"
        >
          Create Your First Request
        </GradientButton>
      </div>
    </div>
    
    <div v-else class="requests-grid">
      <div 
        v-for="item in formattedRequests" 
        :key="item.id"
        @click="showRequestDetails(item)"
        class="request-item-wrapper"
      >
        <RequestItem 
          :item="item"
          :disabled="loading"
        />
      </div>
    </div>

    <!-- Create Request Modal -->
    <CreateRequestModal
      v-model:show="showCreateModal"
      @created="onRequestCreated"
    />
  </div>
</template>

<style scoped>
.requests-list {
  width: 100%;
}

.requests-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding: 0 4px;
}

.page-title {
  font-size: 24px;
  font-weight: 700;
  color: var(--color-text);
  margin: 0;
}

.plus-icon {
  font-size: 16px;
  font-weight: 600;
  margin-right: 4px;
}

.empty-state {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 400px;
  padding: 40px 20px;
}

.empty-state-content {
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 24px;
}

.empty-state-text {
  font-size: 16px;
  color: var(--color-text-secondary);
  margin: 0;
}

.requests-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 16px;
  width: 100%;
}

.request-item-wrapper {
  cursor: pointer;
  transition: transform 0.2s ease;
}

.request-item-wrapper:hover {
  transform: translateY(-2px);
}

.request-item-wrapper:active {
  transform: translateY(0);
}

@media (max-width: 640px) {
  .requests-header {
    padding: 0;
  }

  .page-title {
    font-size: 20px;
  }
}
</style>
