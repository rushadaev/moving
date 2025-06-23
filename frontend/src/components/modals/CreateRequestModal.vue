<template>
  <Modal
    v-model:show="showModal"
    title="Create New Moving Request"
    :mask-closable="false"
  >
    <RequestForm
      ref="requestFormRef"
      v-model="formData"
      mode="create"
      :loading="loading"
      @placeSelected="handlePlaceSelected"
    />

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
import RequestForm from '@/components/forms/RequestForm.vue'
import geocodingService from '@/services/geocoding.service'
import routesService from '@/services/routes.service'

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

const requestFormRef = ref<any>()

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
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

// Handle place selection from RequestForm
const handlePlaceSelected = (type: 'pickup' | 'delivery', place: any) => {
  // The RequestForm already updates the formData, so we don't need to do anything here
  // This handler is just for any additional logic we might need
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
    
    // Add distance to form data (hourly_rate is already calculated in the form)
    const requestData = {
      ...formData.value,
      distance: distanceMiles,
      // Additional data for backend email
      pricing_details: {
        hourly_rate: formData.value.hourly_rate,
        travel_cost: travelCost,
        selected_packing_options: formData.value.packing_options,
        packing_cost: selectedPackingCost,
        packing_prices: packingPrices,
        distance_miles: distanceMiles,
        movers_count: formData.value.movers_count,
        estimated_hours: 3, // Default estimate - backend can adjust
        estimated_total: (formData.value.hourly_rate * 3) + travelCost + selectedPackingCost
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

