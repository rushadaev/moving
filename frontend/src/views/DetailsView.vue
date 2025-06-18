<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useRequestsStore } from '../stores/requests'
import { useAuthStore } from '../stores/auth'
import { useMessage } from 'naive-ui'
import GradientButton from '@/components/ui/GradientButton.vue'
import Header from '@/components/Header.vue'
import Footer from '@/components/Footer.vue'
import {
  NCard,
  NForm,
  NFormItem,
  NSelect,
  NInput,
  NInputNumber,
  NDatePicker,
  NCheckbox,
  NRadioGroup,
  NRadioButton,
  NSpace,
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

// Property type options - from backend enum
const propertyTypeOptions = [
  { label: 'Residential', value: 'residential' },
  { label: 'Commercial', value: 'commercial' }
]

const packageTypeOptions = [
  { label: 'Basic Package', value: 'basic' },
  { label: 'Standard Package', value: 'standard' },
  { label: 'Premium Package', value: 'premium' }
]

// Form data
const formData = ref({
  property_type: 'residential',
  square_feet: 1000,
  additional_objects: [],
  movers_count: 2,
  hourly_rate: 100,
  departure_time: '',
  labor_included: true,
  package_type: 'standard',
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
  materials: []
})

// Convert departure time to timestamp for date picker
const departureTimestamp = computed({
  get: () => {
    if (!formData.value.departure_time) return null
    return new Date(formData.value.departure_time).getTime()
  },
  set: (value) => {
    if (value) {
      formData.value.departure_time = new Date(value).toISOString()
    }
  }
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
      formData.value = {
        property_type: sr.property_type || 'residential',
        square_feet: sr.square_feet || 1000,
        additional_objects: sr.additional_objects || [],
        movers_count: sr.movers_count || 2,
        hourly_rate: sr.hourly_rate || 100,
        departure_time: sr.departure_time || '',
        labor_included: sr.labor_included !== undefined ? sr.labor_included : true,
        package_type: sr.package_type || 'standard',
        addresses: sr.addresses && sr.addresses.length > 0 ? [...sr.addresses] : [
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
        materials: sr.materials || []
      }
    }
  } catch (err) {
    console.error('Error loading request:', err)
    errorMessage.value = 'Failed to load request details'
  } finally {
    loading.value = false
  }
})

const saveRequest = async () => {
  if (!validateForm()) return
  
  saving.value = true
  
  try {
    if (isEditing.value && requestsStore.selectedRequest?.id) {
      // Update existing request
      const result = await requestsStore.updateRequest(
        requestsStore.selectedRequest.id,
        formData.value
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
  if (!formData.value.property_type) {
    message.error('Please select a property type')
    return false
  }
  if (!formData.value.departure_time) {
    message.error('Please select a moving date and time')
    return false
  }
  if (!formData.value.addresses[0].address) {
    message.error('Please enter a pickup address')
    return false
  }
  if (!formData.value.addresses[1].address) {
    message.error('Please enter a delivery address')
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
        <n-card>
          <n-form label-placement="top">
            <!-- Basic Info Section -->
            <div class="form-section">
              <h3 class="section-title">Basic Information</h3>
              
              <div class="form-row">
                <n-form-item label="Property Type" required>
                  <n-select
                    v-model:value="formData.property_type"
                    :options="propertyTypeOptions"
                    placeholder="Select property type"
                  />
                </n-form-item>
                
                <n-form-item label="Square Feet" required>
                  <n-input-number
                    v-model:value="formData.square_feet"
                    :min="0"
                    placeholder="Enter square feet"
                    style="width: 100%"
                  />
                </n-form-item>
              </div>
              
              <n-form-item label="Moving Date & Time" required>
                <n-date-picker
                  v-model:value="departureTimestamp"
                  type="datetime"
                  placeholder="Select date and time"
                  format="MM/dd/yyyy HH:mm"
                  style="width: 100%"
                />
              </n-form-item>
            </div>
            
            <!-- Addresses Section -->
            <div class="form-section">
              <h3 class="section-title">Addresses</h3>
              
              <n-form-item label="Pickup Location" required>
                <n-input
                  v-model:value="formData.addresses[0].address"
                  placeholder="Enter pickup address"
                />
              </n-form-item>
              
              <n-form-item label="Delivery Location" required>
                <n-input
                  v-model:value="formData.addresses[1].address"
                  placeholder="Enter delivery address"
                />
              </n-form-item>
            </div>
            
            <!-- Service Details Section -->
            <div class="form-section">
              <h3 class="section-title">Service Details</h3>
              
              <div class="form-row">
                <n-form-item label="Number of Movers">
                  <n-input-number
                    v-model:value="formData.movers_count"
                    :min="1"
                    :max="10"
                    style="width: 100%"
                  />
                </n-form-item>
                
                <n-form-item label="Hourly Rate ($)">
                  <n-input-number
                    v-model:value="formData.hourly_rate"
                    :min="0"
                    :precision="2"
                    style="width: 100%"
                  />
                </n-form-item>
              </div>
              
              <div class="form-row">
                <n-form-item label="Package Type">
                  <n-select
                    v-model:value="formData.package_type"
                    :options="packageTypeOptions"
                    placeholder="Select package"
                  />
                </n-form-item>
                
                <n-form-item label="Options">
                  <n-space vertical>
                    <n-checkbox v-model:checked="formData.labor_included">
                      Labor included
                    </n-checkbox>
                  </n-space>
                </n-form-item>
              </div>
            </div>
          </n-form>
          
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

.form-section {
  margin-bottom: 24px;
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: var(--color-text);
  margin-bottom: 16px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
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
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .action-buttons {
    flex-direction: column;
  }
  
  .action-buttons button {
    width: 100%;
  }
}
</style>