<template>
  <Modal
    v-model:show="showModal"
    title="Create New Moving Request"
    :mask-closable="false"
  >
    <div class="form-container">
      <!-- Basic Info Row -->
      <div class="form-row">
        <div class="form-group">
          <label>Property Type</label>
          <n-select
            v-model:value="formData.property_type"
            :options="propertyTypeOptions"
            placeholder="Select property type"
          />
        </div>

        <div class="form-group">
          <label>Square Feet</label>
          <n-input-number
            v-model:value="formData.square_feet"
            :min="0"
            placeholder="Enter square feet"
            style="width: 100%"
          />
        </div>
      </div>

      <!-- Date & Time Full Width -->
      <div class="form-group full-width">
        <label>Moving Date & Time</label>
        <n-date-picker
          v-model:value="departureTimestamp"
          type="datetime"
          placeholder="Select date and time"
          format="MM/dd/yyyy HH:mm"
          style="width: 100%"
        />
      </div>

      <!-- Addresses Section -->
      <div class="form-section">
        <h4 class="section-title">Addresses</h4>
        
        <div class="form-group full-width">
          <label>Pickup Location</label>
          <n-input
            v-model:value="formData.addresses[0].address"
            placeholder="Enter pickup address"
          />
        </div>

        <div class="form-group full-width">
          <label>Delivery Location</label>
          <n-input
            v-model:value="formData.addresses[1].address"
            placeholder="Enter delivery address"
          />
        </div>
      </div>

      <!-- Service Details Row -->
      <div class="form-row">
        <div class="form-group">
          <label>Number of Movers</label>
          <n-input-number
            v-model:value="formData.movers_count"
            :min="1"
            :max="10"
            style="width: 100%"
          />
        </div>

        <div class="form-group">
          <label>Hourly Rate ($)</label>
          <n-input-number
            v-model:value="formData.hourly_rate"
            :min="0"
            :precision="2"
            style="width: 100%"
          />
        </div>
      </div>

      <!-- Package and Options Row -->
      <div class="form-row">
        <div class="form-group">
          <label>Package Type</label>
          <n-select
            v-model:value="formData.package_type"
            :options="packageTypeOptions"
            placeholder="Select package"
          />
        </div>

        <div class="form-group">
          <label>Options</label>
          <n-checkbox v-model:checked="formData.labor_included">
            Labor included
          </n-checkbox>
        </div>
      </div>
    </div>

    <template #footer>
      <GradientButton @click="closeModal" :disabled="loading">Cancel</GradientButton>
      <GradientButton @click="createRequest" :disabled="loading">
        {{ loading ? 'Creating...' : 'Create Request' }}
      </GradientButton>
    </template>
  </Modal>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRequestsStore } from '@/stores/requests'
import { useMessage } from 'naive-ui'
import Modal from '@/components/ui/Modal.vue'
import GradientButton from '@/components/ui/GradientButton.vue'
import geocodingService from '@/services/geocoding.service'
import routesService from '@/services/routes.service'
import {
  NSelect,
  NInput,
  NInputNumber,
  NDatePicker,
  NCheckbox,
  NRadioGroup,
  NRadioButton,
  NSpace,
  NButton
} from 'naive-ui'

const props = defineProps<{
  show: boolean
}>()

const emit = defineEmits<{
  'update:show': [value: boolean]
  'created': []
}>()

const requestsStore = useRequestsStore()
const message = useMessage()
const loading = ref(false)

const showModal = computed({
  get: () => props.show,
  set: (value) => emit('update:show', value)
})

const departureTimestamp = ref<number | null>(null)

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

const propertyTypeOptions = [
  { label: 'Residential', value: 'residential' },
  { label: 'Commercial', value: 'commercial' }
]

const packageTypeOptions = [
  { label: 'Basic Package', value: 'basic' },
  { label: 'Standard Package', value: 'standard' },
  { label: 'Premium Package', value: 'premium' }
]

// Convert timestamp to ISO string for departure_time
watch(departureTimestamp, (newValue) => {
  if (newValue) {
    formData.value.departure_time = new Date(newValue).toISOString()
  }
})

const resetForm = () => {
  formData.value = {
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
  }
  departureTimestamp.value = null
}

const closeModal = () => {
  showModal.value = false
  resetForm()
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
    message.error('Please enter a loading address')
    return false
  }
  if (!formData.value.addresses[1].address) {
    message.error('Please enter an unloading address')
    return false
  }
  return true
}

const createRequest = async () => {
  if (!validateForm()) return

  loading.value = true
  try {
    // Geocode addresses first
    const pickupAddress = formData.value.addresses[0].address;
    const deliveryAddress = formData.value.addresses[1].address;
    
    message.info('Geocoding addresses...')
    
    // Geocode pickup location
    const pickupGeocode = await geocodingService.geocodeAddress(pickupAddress);
    if (pickupGeocode) {
      formData.value.addresses[0].latitude = pickupGeocode.latitude;
      formData.value.addresses[0].longitude = pickupGeocode.longitude;
      formData.value.addresses[0].address = pickupGeocode.formattedAddress;
    } else {
      message.error('Could not find pickup location');
      loading.value = false;
      return;
    }
    
    // Geocode delivery location
    const deliveryGeocode = await geocodingService.geocodeAddress(deliveryAddress);
    if (deliveryGeocode) {
      formData.value.addresses[1].latitude = deliveryGeocode.latitude;
      formData.value.addresses[1].longitude = deliveryGeocode.longitude;
      formData.value.addresses[1].address = deliveryGeocode.formattedAddress;
    } else {
      message.error('Could not find delivery location');
      loading.value = false;
      return;
    }
    
    // Calculate distance in miles
    message.info('Calculating distance...')
    const routeInfo = await routesService.calculateRoute(
      { lat: pickupGeocode.latitude, lng: pickupGeocode.longitude },
      { lat: deliveryGeocode.latitude, lng: deliveryGeocode.longitude }
    );
    
    let distanceMiles = 0;
    if (routeInfo) {
      distanceMiles = Math.round((routeInfo.distance / 1609.34) * 10) / 10; // Convert meters to miles, round to 1 decimal
      console.log('Calculated distance:', distanceMiles, 'miles');
    }
    
    // Add distance to form data
    const requestData = {
      ...formData.value,
      distance: distanceMiles
    };
    
    const result = await requestsStore.createRequest(requestData)
    if (result) {
      message.success(`Request created successfully! Distance: ${distanceMiles} miles`)
      emit('created')
      closeModal()
    } else {
      message.error(requestsStore.error || 'Failed to create request')
    }
  } catch (error) {
    message.error('An error occurred while creating the request')
    console.error('Create request error:', error)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.form-container {
  display: flex;
  flex-direction: column;
  gap: 16px;
  width: 100%;
  padding: 0;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group.full-width {
  grid-column: span 2;
}

.form-section {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 12px 0;
  border-top: 1px solid var(--color-border);
  border-bottom: 1px solid var(--color-border);
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  color: var(--color-text);
  margin: 0 0 8px 0;
}

label {
  font-weight: 500;
  font-size: 13px;
  color: var(--color-text);
  opacity: 0.8;
  line-height: 1.5;
}

/* Remove radio button styles since we're using select now */

@media (max-width: 640px) {
  .form-container {
    gap: 16px;
  }
  
  .form-row {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .form-group.full-width {
    grid-column: span 1;
  }
  
  .form-section {
    padding: 12px 0;
  }
}
</style>