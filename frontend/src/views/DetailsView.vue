<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useRequestsStore } from '../stores/requests'
import { useAuthStore } from '../stores/auth'
import { useMessage } from 'naive-ui'
import GradientButton from '@/components/ui/GradientButton.vue'
import Header from '@/components/Header.vue'
import Footer from '@/components/Footer.vue'
import RequestForm from '@/components/forms/RequestForm.vue'
import {
  NCard,
  NSpin,
  NAlert
} from 'naive-ui'

const router = useRouter()
const requestsStore = useRequestsStore()
const authStore = useAuthStore()
const message = useMessage()
const loading = ref(false)
const saving = ref(false)
const isEditing = ref(false)
const errorMessage = ref('')
const requestFormRef = ref<any>()

// Form data
const formData = ref({
  property_type: 'residential',
  square_feet: 1000,
  additional_objects: [],
  movers_count: 2,
  hourly_rate: 100,
  departure_time: '',
  bedrooms: 2,
  addresses: [
    {
      address: '',
      type: 'loading',
      order: 0,
      latitude: 0,
      longitude: 0
    },
    {
      address: '',
      type: 'unloading',
      order: 1,
      latitude: 0,
      longitude: 0
    }
  ],
  materials: [],
  packing_options: []
})


onMounted(async () => {
  if (!authStore.isAuthenticated) {
    router.push('/requests')
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    const requestId = Number(router.currentRoute.value.query.id)
    
    if (requestId) {
      // Load existing request
      if (!requestsStore.selectedRequest || requestsStore.selectedRequest.id !== requestId) {
        const requestData = await requestsStore.getRequestById(requestId)
        
        if (!requestData || !requestsStore.selectedRequest) {
          errorMessage.value = `Failed to load request #${requestId}`
          loading.value = false
          return
        }
      }
      
      isEditing.value = true
      
      // Initialize form with existing data
      const sr = requestsStore.selectedRequest
      console.log('Loading request data:', sr)
      console.log('Original addresses from API:', sr.addresses)

      formData.value = {
        property_type: sr.property_type || 'residential',
        square_feet: sr.square_feet || 1000,
        additional_objects: sr.additional_objects || [],
        movers_count: sr.movers_count || 2,
        hourly_rate: sr.hourly_rate || 100,
        departure_time: sr.departure_time || '',
        bedrooms: sr.bedrooms || 2,
        packing_options: sr.packing_options || [],
        addresses: sr.addresses && sr.addresses.length > 0
          ? sr.addresses.map(addr => {
              console.log('Processing address:', addr)
              console.log('location_type value:', addr.location_type, 'type:', typeof addr.location_type)
              return {
                ...addr,
                // Keep location_type as is (including null)
                location_type: addr.location_type
              }
            })
          : [
              {
                address: '',
                type: 'loading',
                location_type: null,
                order: 0,
                latitude: 0,
                longitude: 0
              },
              {
                address: '',
                type: 'unloading',
                location_type: null,
                order: 1,
                latitude: 0,
                longitude: 0
              }
            ],
        materials: sr.materials || []
      }

      console.log('Form data after initialization:', formData.value)
      console.log('Processed addresses:', formData.value.addresses)
    }
  } catch (err) {
    console.error('Error loading request:', err)
    errorMessage.value = 'Failed to load request details'
  } finally {
    loading.value = false
  }
})

// Helper to log data before sending to API
const prepareDataForApi = (data: any) => {
  console.log('Preparing data for API:', data)
  console.log('Addresses with location_type:', data.addresses.map((a: any) => ({
    address: a.address,
    type: a.type,
    location_type: a.location_type
  })))
  // No conversion needed - null is valid for JSON
  return data
}

const saveRequest = async () => {
  if (!validateForm()) return

  saving.value = true

  try {
    if (isEditing.value && requestsStore.selectedRequest?.id) {
      // Update existing request
      const preparedData = prepareDataForApi(formData.value)
      const result = await requestsStore.updateRequest(
        requestsStore.selectedRequest.id,
        preparedData
      )

      if (result) {
        message.success('Request updated successfully!')
        router.push('/requests')
      } else {
        message.error('Failed to update request')
      }
    }
  } catch (error) {
    message.error('An error occurred while saving')
    console.error('Save error:', error)
  } finally {
    saving.value = false
  }
}

const validateForm = () => {
  if (!requestFormRef.value) return false
  
  const validation = requestFormRef.value.validate()
  if (!validation.valid) {
    message.error(validation.message)
    return false
  }
  return true
}

const goToTracking = () => {
  if (requestsStore.selectedRequest?.id) {
    router.push({
      path: '/tracking',
      query: { id: requestsStore.selectedRequest.id }
    })
  }
}

const cancelEdit = () => {
  router.push('/requests')
}

// Financial Summary Helpers
const getPaymentStatusClass = (status: string) => {
  const statusMap = {
    'pending': 'status-pending',
    'paid': 'status-paid',
    'processing': 'status-processing',
    'failed': 'status-failed'
  }
  return statusMap[status] || 'status-pending'
}

const getPaymentStatusText = (status: string) => {
  const textMap = {
    'pending': 'Pending',
    'paid': 'Paid',
    'processing': 'Processing',
    'failed': 'Failed'
  }
  return textMap[status] || 'Pending'
}

const calculateTotal = () => {
  const basePrice = Number(requestsStore.selectedRequest?.price || 0)
  const tipsAmount = Number(requestsStore.selectedRequest?.tips_amount || 0)
  return (basePrice + tipsAmount).toFixed(2)
}
</script>

<template>
  <div class="details-page">
    <Header title="Request Details" />
    
    <main class="details-content">
      <div v-if="loading" class="loading-container">
        <n-spin size="medium" />
        <p>Loading request details...</p>
      </div>
      
      <div v-else-if="errorMessage" class="error-container">
        <n-alert type="error" :title="errorMessage" />
        <GradientButton @click="router.push('/requests')" class="mt-4">
          Back to Requests
        </GradientButton>
      </div>
      
      <div v-else class="form-container">
        <!-- Financial Summary Card -->
        <n-card v-if="isEditing && requestsStore.selectedRequest" class="financial-summary-card">
          <h2 class="summary-title">Financial Summary</h2>

          <div class="summary-grid">
            <!-- Base Price -->
            <div class="summary-row">
              <span class="summary-label">Base Price:</span>
              <span class="summary-value price-value">${{ requestsStore.selectedRequest.price || 0 }}</span>
            </div>

            <!-- Payment Status -->
            <div class="summary-row">
              <span class="summary-label">Payment Status:</span>
              <span class="summary-badge" :class="getPaymentStatusClass(requestsStore.selectedRequest.payment_status)">
                {{ getPaymentStatusText(requestsStore.selectedRequest.payment_status) }}
              </span>
            </div>

            <!-- Tips Amount (if added) -->
            <div v-if="requestsStore.selectedRequest.tips_amount" class="summary-row">
              <span class="summary-label">Tips:</span>
              <span class="summary-value tips-value">
                ${{ requestsStore.selectedRequest.tips_amount }}
                <span class="tips-percentage">({{ requestsStore.selectedRequest.tips_percentage }}%)</span>
              </span>
            </div>

            <!-- Tips Payment Status (if tips added) -->
            <div v-if="requestsStore.selectedRequest.tips_amount" class="summary-row">
              <span class="summary-label">Tips Payment:</span>
              <span class="summary-badge" :class="getPaymentStatusClass(requestsStore.selectedRequest.tips_payment_status)">
                {{ getPaymentStatusText(requestsStore.selectedRequest.tips_payment_status) }}
              </span>
            </div>

            <!-- Total -->
            <div class="summary-row total-row">
              <span class="summary-label">Total:</span>
              <span class="summary-value total-value">
                ${{ calculateTotal() }}
              </span>
            </div>
          </div>
        </n-card>

        <!-- Request Form Card -->
        <n-card>
          <RequestForm
            ref="requestFormRef"
            v-model="formData"
            mode="edit"
            :loading="saving"
          />

          <!-- Action Buttons -->
          <div class="action-buttons">
            <GradientButton @click="cancelEdit" :disabled="saving">
              Cancel
            </GradientButton>
            <GradientButton @click="saveRequest" :disabled="saving">
              {{ saving ? 'Saving...' : 'Save Changes' }}
            </GradientButton>
            <GradientButton
              v-if="isEditing && requestsStore.selectedRequest?.id"
              @click="goToTracking"
              :disabled="saving"
            >
              Track Request
            </GradientButton>
          </div>
        </n-card>
      </div>
    </main>
    
    <Footer />
  </div>
</template>

<style scoped>
.details-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: var(--color-background);
}

.details-content {
  flex: 1;
  padding: 20px;
  max-width: 800px;
  margin: 0 auto;
  width: 100%;
}

.loading-container,
.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
  min-height: 400px;
  text-align: center;
}

.loading-container p {
  color: var(--color-text-secondary);
}

.form-container {
  width: 100%;
}

.financial-summary-card {
  margin-bottom: 20px;
}

.summary-title {
  font-size: 20px;
  font-weight: 700;
  color: var(--color-text);
  margin: 0 0 20px 0;
}

.summary-grid {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid var(--color-border);
}

.summary-row:last-child {
  border-bottom: none;
}

.summary-label {
  font-size: 14px;
  font-weight: 500;
  color: var(--color-text-secondary);
}

.summary-value {
  font-size: 16px;
  font-weight: 600;
  color: var(--color-text);
}

.price-value {
  color: #3b82f6;
}

.tips-value {
  color: #10b981;
}

.tips-percentage {
  font-size: 14px;
  color: var(--color-text-secondary);
  margin-left: 8px;
}

.total-row {
  padding-top: 16px;
  margin-top: 8px;
  border-top: 2px solid var(--color-border);
  border-bottom: none;
}

.total-value {
  font-size: 20px;
  font-weight: 700;
  color: #059669;
}

.summary-badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 600;
  text-transform: capitalize;
}

.status-pending {
  background-color: rgba(251, 191, 36, 0.1);
  color: #f59e0b;
}

.status-paid {
  background-color: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.status-processing {
  background-color: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
}

.status-failed {
  background-color: rgba(239, 68, 68, 0.1);
  color: #ef4444;
}

.action-buttons {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 24px;
  padding-top: 24px;
  border-top: 1px solid var(--color-border);
}

@media (max-width: 640px) {
  .details-content {
    padding: 12px;
  }
  
  
  .action-buttons {
    flex-direction: column;
  }
  
  .action-buttons button {
    width: 100%;
  }
}
</style>