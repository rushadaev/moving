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
          <label>{{ formData.property_type === 'residential' ? 'Number of Bedrooms' : 'Square Feet' }}</label>
          <n-input-number
            v-model:value="formData.property_type === 'residential' ? formData.bedrooms : formData.square_feet"
            :min="0"
            :placeholder="formData.property_type === 'residential' ? 'Enter number of bedrooms' : 'Enter square feet'"
            style="width: 100%"
          />
        </div>
      </div>

      <!-- Date & Time Full Width -->
      <div class="form-group full-width">
        <label>Moving Date</label>
        <n-date-picker
          v-model:value="departureDate"
          type="date"
          placeholder="Select date"
          format="MM/dd/yyyy"
          style="width: 100%"
          :is-date-disabled="isDateDisabled"
        />
      </div>

      <!-- Custom Time Picker -->
      <div class="form-group full-width">
        <label>Moving Time</label>
        <div class="time-picker-container">
          <n-select
            v-model:value="timeData.hour"
            :options="hourOptions"
            placeholder="Hour"
            style="width: 30%"
          />
          <n-select
            v-model:value="timeData.minute"
            :options="minuteOptions"
            placeholder="Minutes"
            style="width: 30%"
          />
          <n-select
            v-model:value="timeData.period"
            :options="periodOptions"
            placeholder="AM/PM"
            style="width: 30%"
          />
        </div>
      </div>

      <!-- Addresses Section -->
      <div class="form-section">
        <h4 class="section-title">Addresses</h4>
        
        <div class="form-group full-width">
          <label>Pickup Location</label>
          <AddressAutocomplete
            v-model="formData.addresses[0].address"
            placeholder="Enter pickup address"
            @placeSelected="handlePickupPlaceSelected"
          />
        </div>

        <div class="form-group full-width">
          <label>Delivery Location</label>
          <AddressAutocomplete
            v-model="formData.addresses[1].address"
            placeholder="Enter delivery address"
            @placeSelected="handleDeliveryPlaceSelected"
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
            :value="calculatedHourlyRate"
            :disabled="true"
            :precision="2"
            style="width: 100%"
          />
        </div>
      </div>

      <!-- Packing Options -->
      <div class="form-group full-width">
        <label>Packing Options (Optional)</label>
        <n-checkbox-group v-model:value="formData.packing_options">
          <n-space>
            <n-checkbox value="boxes" label="Boxes">
              Boxes
            </n-checkbox>
            <n-checkbox value="bubble_wrap" label="Bubble Wrap">
              Bubble Wrap
            </n-checkbox>
            <n-checkbox value="packing_tape" label="Packing Tape">
              Packing Tape
            </n-checkbox>
            <n-checkbox value="full_service" label="Full Service Packing">
              Full Service Packing
            </n-checkbox>
          </n-space>
        </n-checkbox-group>
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
import AddressAutocomplete from '@/components/ui/AddressAutocomplete.vue'
import geocodingService from '@/services/geocoding.service'
import routesService from '@/services/routes.service'
import {
  NSelect,
  NInput,
  NInputNumber,
  NDatePicker,
  NCheckbox,
  NCheckboxGroup,
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

const departureDate = ref<number | null>(null)

// Time picker data
const timeData = ref({
  hour: 12,
  minute: '00',
  period: 'PM'
})

// Time picker options
const hourOptions = Array.from({length: 12}, (_, i) => ({
  label: String(i + 1),
  value: i + 1
}))

const minuteOptions = [
  { label: '00', value: '00' },
  { label: '15', value: '15' },
  { label: '30', value: '30' },
  { label: '45', value: '45' }
]

const periodOptions = [
  { label: 'AM', value: 'AM' },
  { label: 'PM', value: 'PM' }
]

// Calculate hourly rate based on number of movers
const calculatedHourlyRate = computed(() => {
  const baseRate = 50 // Base rate per mover
  return formData.value.movers_count * baseRate
})

const formData = ref({
  property_type: 'residential',
  square_feet: 1000,
  bedrooms: 2,
  additional_objects: [],
  movers_count: 2,
  hourly_rate: 100,
  departure_time: '',
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

const propertyTypeOptions = [
  { label: 'Residential', value: 'residential' },
  { label: 'Commercial', value: 'commercial' }
]


// Convert date and time to ISO string for departure_time
watch([departureDate, timeData], ([newDate, newTime]) => {
  if (newDate) {
    const date = new Date(newDate)
    let hour = newTime.hour
    
    // Convert to 24-hour format
    if (newTime.period === 'PM' && hour !== 12) {
      hour += 12
    } else if (newTime.period === 'AM' && hour === 12) {
      hour = 0
    }
    
    date.setHours(hour)
    date.setMinutes(parseInt(newTime.minute))
    date.setSeconds(0)
    
    formData.value.departure_time = date.toISOString()
  }
}, { deep: true })

// Disable past dates
const isDateDisabled = (timestamp: number) => {
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  return timestamp < today.getTime()
}

const resetForm = () => {
  formData.value = {
    property_type: 'residential',
    square_feet: 1000,
    bedrooms: 2,
    additional_objects: [],
    movers_count: 2,
    hourly_rate: 100,
    departure_time: '',
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
  }
  departureDate.value = null
  timeData.value = {
    hour: 12,
    minute: '00',
    period: 'PM'
  }
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

// Handle place selection from Google Maps autocomplete
const handlePickupPlaceSelected = (place: any) => {
  if (place.geometry && place.geometry.location) {
    formData.value.addresses[0].latitude = place.geometry.location.lat()
    formData.value.addresses[0].longitude = place.geometry.location.lng()
    formData.value.addresses[0].address = place.formatted_address
  }
}

const handleDeliveryPlaceSelected = (place: any) => {
  if (place.geometry && place.geometry.location) {
    formData.value.addresses[1].latitude = place.geometry.location.lat()
    formData.value.addresses[1].longitude = place.geometry.location.lng()
    formData.value.addresses[1].address = place.formatted_address
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
    // Geocode addresses if not already geocoded by autocomplete
    const pickupAddress = formData.value.addresses[0];
    const deliveryAddress = formData.value.addresses[1];
    
    // Check if pickup location needs geocoding
    if (!pickupAddress.latitude || !pickupAddress.longitude) {
      message.info('Geocoding pickup address...')
      const pickupGeocode = await geocodingService.geocodeAddress(pickupAddress.address);
      if (pickupGeocode) {
        formData.value.addresses[0].latitude = pickupGeocode.latitude;
        formData.value.addresses[0].longitude = pickupGeocode.longitude;
        formData.value.addresses[0].address = pickupGeocode.formattedAddress;
      } else {
        message.error('Could not find pickup location');
        loading.value = false;
        return;
      }
    }
    
    // Check if delivery location needs geocoding
    if (!deliveryAddress.latitude || !deliveryAddress.longitude) {
      message.info('Geocoding delivery address...')
      const deliveryGeocode = await geocodingService.geocodeAddress(deliveryAddress.address);
      if (deliveryGeocode) {
        formData.value.addresses[1].latitude = deliveryGeocode.latitude;
        formData.value.addresses[1].longitude = deliveryGeocode.longitude;
        formData.value.addresses[1].address = deliveryGeocode.formattedAddress;
      } else {
        message.error('Could not find delivery location');
        loading.value = false;
        return;
      }
    }
    
    // Calculate distance in miles
    message.info('Calculating distance...')
    const routeInfo = await routesService.calculateRoute(
      { lat: formData.value.addresses[0].latitude, lng: formData.value.addresses[0].longitude },
      { lat: formData.value.addresses[1].latitude, lng: formData.value.addresses[1].longitude }
    );
    
    let distanceMiles = 0;
    if (routeInfo) {
      distanceMiles = Math.round((routeInfo.distance / 1609.34) * 10) / 10; // Convert meters to miles, round to 1 decimal
      console.log('Calculated distance:', distanceMiles, 'miles');
    }
    
    // Prepare packing prices (example prices - adjust as needed)
    const packingPrices = {
      boxes: 5, // $5 per box
      bubble_wrap: 10, // $10 for bubble wrap
      packing_tape: 3, // $3 for packing tape
      full_service: 200 // $200 for full service packing
    };
    
    // Calculate selected packing cost
    const selectedPackingCost = formData.value.packing_options.reduce((total, option) => {
      return total + (packingPrices[option as keyof typeof packingPrices] || 0);
    }, 0);
    
    // Calculate travel cost (example: $2 per mile)
    const travelCost = distanceMiles * 2;
    
    // Add distance and calculated values to form data
    const requestData = {
      ...formData.value,
      hourly_rate: calculatedHourlyRate.value,
      distance: distanceMiles,
      // Additional data for backend email
      pricing_details: {
        hourly_rate: calculatedHourlyRate.value,
        travel_cost: travelCost,
        selected_packing_options: formData.value.packing_options,
        packing_cost: selectedPackingCost,
        packing_prices: packingPrices,
        distance_miles: distanceMiles,
        movers_count: formData.value.movers_count,
        estimated_hours: 3, // Default estimate - backend can adjust
        estimated_total: (calculatedHourlyRate.value * 3) + travelCost + selectedPackingCost
      }
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

.time-picker-container {
  display: flex;
  gap: 12px;
  align-items: center;
  justify-content: space-between;
}

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