<template>
  <div class="request-form">
    <!-- Basic Info Row -->
    <div class="form-row">
      <div class="form-group">
        <label>Property Type</label>
        <n-select
          :value="formData.property_type"
          @update:value="updateField('property_type', $event)"
          :options="propertyTypeOptions"
          placeholder="Select property type"
        />
      </div>

      <div class="form-group">
        <label>{{ formData.property_type === 'residential' ? 'Number of Bedrooms' : 'Square Feet' }}</label>
        <n-input-number
          v-if="formData.property_type === 'residential'"
          :value="formData.bedrooms"
          @update:value="updateField('bedrooms', $event)"
          :min="0"
          placeholder="Enter number of bedrooms"
          style="width: 100%"
        />
        <n-input-number
          v-else
          :value="formData.square_feet"
          @update:value="updateField('square_feet', $event)"
          :min="0"
          placeholder="Enter square feet"
          style="width: 100%"
        />
      </div>
    </div>

    <!-- Date & Time Full Width -->
    <div class="form-group full-width">
      <label>Moving Date</label>
      <n-date-picker
        :value="departureDate"
        @update:value="updateDepartureDate"
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
          :value="timeData.hour"
          @update:value="updateTime('hour', $event)"
          :options="hourOptions"
          placeholder="Hour"
          style="width: 30%"
        />
        <n-select
          :value="timeData.minute"
          @update:value="updateTime('minute', $event)"
          :options="minuteOptions"
          placeholder="Minutes"
          style="width: 30%"
        />
        <n-select
          :value="timeData.period"
          @update:value="updateTime('period', $event)"
          :options="periodOptions"
          placeholder="AM/PM"
          style="width: 30%"
        />
      </div>
    </div>

    <!-- Addresses Section -->
    <div class="form-section">
      <div class="section-header">
        <h4 class="section-title">Addresses</h4>
        <small class="section-subtitle">Add pickup, delivery, and optional stops</small>
      </div>

      <!-- Dynamic Address List -->
      <div
        v-for="(address, index) in formData.addresses"
        :key="index"
        class="address-card"
      >
        <div class="address-card-header">
          <div class="address-number">
            <span class="number-badge">{{ index + 1 }}</span>
            <span class="address-label">{{ getAddressLabel(index) }}</span>
          </div>

          <!-- Remove button (only for intermediate) -->
          <n-button
            v-if="address.type === 'intermediate'"
            @click="removeAddress(index)"
            text
            type="error"
            size="small"
          >
            <template #icon>
              <n-icon><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg></n-icon>
            </template>
          </n-button>
        </div>

        <div class="address-card-content">
          <!-- Address Autocomplete -->
          <div class="form-group full-width">
            <label>Address</label>
            <AddressAutocomplete
              :model-value="address.address"
              @update:model-value="updateAddress(index, 'address', $event)"
              :placeholder="`Enter ${getAddressLabel(index).toLowerCase()}`"
              @placeSelected="(place) => handlePlaceSelected(index, place)"
            />
          </div>

          <!-- Action Type & Location Type Row -->
          <div class="form-row">
            <div class="form-group">
              <label>Action Type</label>
              <n-select
                :value="address.type"
                @update:value="(value) => {
                  console.log('Action type changed for address', index, 'to', value)
                  updateAddress(index, 'type', value)
                }"
                :options="actionTypeOptions"
                placeholder="Select action"
              />
            </div>

            <div class="form-group">
              <label>Location Type <span class="optional-text">(Optional)</span></label>
              <n-select
                :value="address.location_type"
                @update:value="(value) => {
                  console.log('Location type changed for address', index, 'to', value)
                  updateAddress(index, 'location_type', value)
                }"
                :options="locationTypeOptions"
                placeholder="Select type"
                clearable
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Add Intermediate Button -->
      <div class="add-address-container">
        <n-button
          @click="addIntermediateAddress"
          dashed
          block
          type="primary"
        >
          <template #icon>
            <n-icon><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg></n-icon>
          </template>
          Add Intermediate Stop
        </n-button>
      </div>
    </div>

    <!-- Service Details Row -->
    <div class="form-row">
      <div class="form-group">
        <label>Number of Movers</label>
        <n-input-number
          :value="formData.movers_count"
          @update:value="updateField('movers_count', $event)"
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
      <n-checkbox-group 
        :value="formData.packing_options"
        @update:value="updateField('packing_options', $event)"
      >
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
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import AddressAutocomplete from '@/components/ui/AddressAutocomplete.vue'
import {
  NSelect,
  NInput,
  NInputNumber,
  NDatePicker,
  NCheckbox,
  NCheckboxGroup,
  NSpace,
  NButton,
  NIcon
} from 'naive-ui'

const props = defineProps<{
  modelValue: any
  mode?: 'create' | 'edit'
  loading?: boolean
}>()

const emit = defineEmits<{
  'update:modelValue': [value: any]
  'update:valid': [value: boolean]
  'placeSelected': [type: 'pickup' | 'delivery', place: any]
}>()

// Local copy of form data
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
  materials: [],
  packing_options: []
})

// Date and time state
const departureDate = ref<number | null>(null)
const timeData = ref({
  hour: 12,
  minute: '00',
  period: 'PM'
})

// Options
const propertyTypeOptions = [
  { label: 'Residential', value: 'residential' },
  { label: 'Commercial', value: 'commercial' }
]

const actionTypeOptions = [
  { label: 'Loading', value: 'loading' },
  { label: 'Unloading', value: 'unloading' },
  { label: 'Loading & Unloading', value: 'intermediate' }
]

const locationTypeOptions = [
  { label: 'Apartment', value: 'apartment' },
  { label: 'Storage', value: 'storage' },
  { label: 'House', value: 'house' },
  { label: 'Office', value: 'office' },
  { label: 'Garage', value: 'garage' }
]

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

// Initialize form data from modelValue
onMounted(() => {
  if (props.modelValue) {
    formData.value = { ...formData.value, ...props.modelValue }
    console.log('RequestForm initialized with data:', formData.value)
    console.log('Addresses:', formData.value.addresses)

    // Parse departure time if it exists
    if (formData.value.departure_time) {
      const date = new Date(formData.value.departure_time)
      departureDate.value = date.getTime()

      let hours = date.getHours()
      const minutes = date.getMinutes()
      const period = hours >= 12 ? 'PM' : 'AM'

      if (hours > 12) hours -= 12
      if (hours === 0) hours = 12

      timeData.value = {
        hour: hours,
        minute: minutes.toString().padStart(2, '0'),
        period: period
      }
    }
  }
})

// Watch for modelValue changes
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    formData.value = { ...formData.value, ...newValue }
  }
}, { deep: true })

// Watch addresses for changes
watch(() => formData.value.addresses, (newAddresses) => {
  console.log('Addresses changed:', newAddresses)
  newAddresses.forEach((addr, idx) => {
    console.log(`Address ${idx}:`, {
      address: addr.address,
      type: addr.type,
      location_type: addr.location_type
    })
  })
}, { deep: true })

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
    
    updateField('departure_time', date.toISOString())
  }
}, { deep: true })

// Update calculated hourly rate when movers count changes
watch(() => formData.value.movers_count, () => {
  updateField('hourly_rate', calculatedHourlyRate.value)
})

// Field update methods
const updateField = (field: string, value: any) => {
  formData.value = { ...formData.value, [field]: value }
  emit('update:modelValue', formData.value)
}

const updateAddress = (index: number, field: string, value: any) => {
  console.log(`updateAddress called: index=${index}, field=${field}, value=${value}`)

  const newAddresses = [...formData.value.addresses]
  newAddresses[index] = { ...newAddresses[index], [field]: value }

  console.log('Updated address:', newAddresses[index])
  updateField('addresses', newAddresses)
}

const updateTime = (field: 'hour' | 'minute' | 'period', value: any) => {
  timeData.value = { ...timeData.value, [field]: value }
}

const updateDepartureDate = (value: number | null) => {
  departureDate.value = value
}

// Disable past dates
const isDateDisabled = (timestamp: number) => {
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  return timestamp < today.getTime()
}

// Get descriptive label for address based on position
const getAddressLabel = (index: number) => {
  if (index === 0) return 'Pickup Location'
  if (index === formData.value.addresses.length - 1) return 'Delivery Location'
  return `Stop ${index}`
}

// Add intermediate address (inserts before last address)
const addIntermediateAddress = () => {
  const addresses = [...formData.value.addresses]
  const lastAddress = addresses.pop()

  addresses.push({
    address: '',
    type: 'intermediate',
    location_type: null,
    order: addresses.length,
    latitude: 0,
    longitude: 0
  })

  if (lastAddress) {
    lastAddress.order = addresses.length
    addresses.push(lastAddress)
  }

  updateField('addresses', addresses)
}

// Remove address and re-index
const removeAddress = (index: number) => {
  const addresses = [...formData.value.addresses]
  addresses.splice(index, 1)

  // Re-order remaining addresses
  addresses.forEach((addr, idx) => {
    addr.order = idx
  })

  updateField('addresses', addresses)
}

// Unified place selection handler
const handlePlaceSelected = (index: number, place: any) => {
  console.log('handlePlaceSelected called:', index, place)

  if (place?.geometry?.location) {
    console.log('Place has geometry and location')

    // Call lat() and lng() functions
    const lat = typeof place.geometry.location.lat === 'function'
      ? place.geometry.location.lat()
      : place.geometry.location.lat

    const lng = typeof place.geometry.location.lng === 'function'
      ? place.geometry.location.lng()
      : place.geometry.location.lng

    console.log('Coordinates:', lat, lng)

    const addresses = [...formData.value.addresses]
    addresses[index] = {
      ...addresses[index],
      address: place.formatted_address,
      latitude: lat,
      longitude: lng
    }

    console.log('Updated addresses:', addresses)
    updateField('addresses', addresses)
    emit('placeSelected', index === 0 ? 'pickup' : 'delivery', place)
  } else {
    console.error('Place missing geometry or location:', place)
  }
}

// Validation
const validate = () => {
  if (!formData.value.property_type) {
    return { valid: false, message: 'Please select a property type' }
  }
  if (!formData.value.departure_time) {
    return { valid: false, message: 'Please select a moving date and time' }
  }

  // Validate all addresses
  for (let i = 0; i < formData.value.addresses.length; i++) {
    if (!formData.value.addresses[i].address) {
      return {
        valid: false,
        message: `Please enter address for ${getAddressLabel(i)}`
      }
    }
    if (!formData.value.addresses[i].type) {
      return {
        valid: false,
        message: `Please select action type for ${getAddressLabel(i)}`
      }
    }
  }

  return { valid: true, message: '' }
}

// Expose validate method
defineExpose({
  validate
})
</script>

<style scoped>
.request-form {
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

.section-header {
  margin-bottom: 16px;
}

.section-subtitle {
  color: #94a3b8;
  font-size: 12px;
  display: block;
  margin-top: 4px;
}

.address-card {
  background: #1e293b;
  border: 1px solid #334155;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 16px;
  transition: all 0.2s ease;
}

.address-card:hover {
  border-color: #3b82f6;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
}

.address-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #334155;
}

.address-number {
  display: flex;
  align-items: center;
  gap: 12px;
}

.number-badge {
  width: 28px;
  height: 28px;
  background: #3b82f6;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 14px;
}

.address-label {
  font-weight: 600;
  font-size: 14px;
  color: #e2e8f0;
}

.address-card-content {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.add-address-container {
  margin-top: 8px;
}

.optional-text {
  font-size: 11px;
  font-weight: 400;
  color: #9ca3af;
}

@media (max-width: 640px) {
  .form-row {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .form-group.full-width {
    grid-column: span 1;
  }

  .address-card {
    padding: 12px;
  }

  .address-card-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
}
</style>