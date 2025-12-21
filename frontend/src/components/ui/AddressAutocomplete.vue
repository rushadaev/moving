<template>
  <div class="address-autocomplete">
    <input
      ref="inputRef"
      :value="modelValue"
      @input="handleInput"
      @focus="handleFocus"
      @blur="handleBlur"
      :placeholder="placeholder"
      class="w-full p-2 border border-gray-300 rounded text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
    />
    <div 
      v-if="showSuggestions && suggestions.length > 0" 
      class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-auto"
    >
      <div
        v-for="(suggestion, index) in suggestions"
        :key="suggestion.place_id"
        @click="selectSuggestion(suggestion)"
        @mouseenter="highlightedIndex = index"
        :class="[
          'px-4 py-2 cursor-pointer transition-colors',
          highlightedIndex === index ? 'bg-gray-100' : 'hover:bg-gray-50'
        ]"
      >
        <div class="font-medium text-gray-900">{{ suggestion.structured_formatting.main_text }}</div>
        <div class="text-sm text-gray-600">{{ suggestion.structured_formatting.secondary_text }}</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useGoogleMaps } from '@/composables/useGoogleMaps'

const props = defineProps<{
  modelValue: string
  placeholder?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
  'placeSelected': [place: any]
}>()

const inputRef = ref<HTMLInputElement>()
const suggestions = ref<any[]>([])
const showSuggestions = ref(false)
const highlightedIndex = ref(-1)
let autocompleteService: any = null
let placesService: any = null
let sessionToken: any = null
const { loadGoogleMaps } = useGoogleMaps()

const handleInput = async (event: Event) => {
  const value = (event.target as HTMLInputElement).value
  emit('update:modelValue', value)
  
  if (!value || value.length < 2) {
    suggestions.value = []
    showSuggestions.value = false
    return
  }
  
  if (!autocompleteService) return
  
  // Create new session token for billing optimization
  if (!sessionToken) {
    sessionToken = new window.google.maps.places.AutocompleteSessionToken()
  }
  
  // Get predictions
  autocompleteService.getPlacePredictions({
    input: value,
    componentRestrictions: { country: 'us' },
    types: ['address'],
    sessionToken: sessionToken
  }, (predictions: any, status: any) => {
    if (status === window.google.maps.places.PlacesServiceStatus.OK && predictions) {
      suggestions.value = predictions
      showSuggestions.value = true
      highlightedIndex.value = -1
    } else {
      suggestions.value = []
      showSuggestions.value = false
    }
  })
}

const handleFocus = () => {
  if (suggestions.value.length > 0) {
    showSuggestions.value = true
  }
}

const handleBlur = () => {
  // Delay to allow click on suggestion
  setTimeout(() => {
    showSuggestions.value = false
  }, 200)
}

const selectSuggestion = async (suggestion: any) => {
  console.log('selectSuggestion called with:', suggestion)

  // Get place details
  const placeId = suggestion.place_id

  placesService.getDetails({
    placeId: placeId,
    fields: ['formatted_address', 'geometry', 'name'],
    sessionToken: sessionToken
  }, (place: any, status: any) => {
    console.log('getDetails response:', status, place)

    if (status === window.google.maps.places.PlacesServiceStatus.OK && place) {
      const address = place.formatted_address || place.name
      console.log('Emitting update:modelValue with:', address)
      emit('update:modelValue', address)

      const placeData = {
        formatted_address: address,
        geometry: {
          location: {
            lat: () => place.geometry.location.lat(),
            lng: () => place.geometry.location.lng()
          }
        }
      }
      console.log('Emitting placeSelected with:', placeData)
      emit('placeSelected', placeData)

      // Reset session token after place selection
      sessionToken = null
      suggestions.value = []
      showSuggestions.value = false
    } else {
      console.error('Failed to get place details:', status)
    }
  })
}

onMounted(async () => {
  console.log('AddressAutocomplete mounted')

  if (!inputRef.value) {
    console.error('Input ref not available')
    return
  }

  try {
    console.log('Loading Google Maps...')
    await loadGoogleMaps()
    console.log('Google Maps loaded successfully')

    // Wait a bit for places library to be fully loaded
    await new Promise(resolve => setTimeout(resolve, 100))

    // Check if places library is loaded
    if (!window.google?.maps?.places) {
      console.error('Google Maps Places library not loaded')
      return
    }

    console.log('Initializing autocomplete services...')
    // Initialize services
    autocompleteService = new window.google.maps.places.AutocompleteService()
    placesService = new window.google.maps.places.PlacesService(document.createElement('div'))
    console.log('Autocomplete services initialized successfully')

  } catch (error) {
    console.error('Failed to initialize Google Maps autocomplete:', error)
  }
})
</script>

<style scoped>
.address-autocomplete {
  width: 100%;
  position: relative;
}
</style>