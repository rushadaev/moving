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
      <h4 class="section-title">Addresses</h4>
      
      <div class="form-group full-width">
        <label>Pickup Location</label>
        <AddressAutocomplete
          :model-value="formData.addresses[0].address"
          @update:model-value="updateAddress(0, 'address', $event)"
          placeholder="Enter pickup address"
          @placeSelected="handlePickupPlaceSelected"
        />
      </div>

      <div class="form-group full-width">
        <label>Delivery Location</label>
        <AddressAutocomplete
          :model-value="formData.addresses[1].address"
          @update:model-value="updateAddress(1, 'address', $event)"
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
  NSpace
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
  const newAddresses = [...formData.value.addresses]
  newAddresses[index] = { ...newAddresses[index], [field]: value }
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

// Handle place selection from Google Maps autocomplete
const handlePickupPlaceSelected = (place: any) => {
  if (place.geometry && place.geometry.location) {
    const newAddresses = [...formData.value.addresses]
    newAddresses[0] = {
      ...newAddresses[0],
      latitude: place.geometry.location.lat(),
      longitude: place.geometry.location.lng(),
      address: place.formatted_address
    }
    updateField('addresses', newAddresses)
    emit('placeSelected', 'pickup', place)
  }
}

const handleDeliveryPlaceSelected = (place: any) => {
  if (place.geometry && place.geometry.location) {
    const newAddresses = [...formData.value.addresses]
    newAddresses[1] = {
      ...newAddresses[1],
      latitude: place.geometry.location.lat(),
      longitude: place.geometry.location.lng(),
      address: place.formatted_address
    }
    updateField('addresses', newAddresses)
    emit('placeSelected', 'delivery', place)
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
  if (!formData.value.addresses[0].address) {
    return { valid: false, message: 'Please enter a pickup address' }
  }
  if (!formData.value.addresses[1].address) {
    return { valid: false, message: 'Please enter a delivery address' }
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

@media (max-width: 640px) {
  .form-row {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .form-group.full-width {
    grid-column: span 1;
  }
}
</style>