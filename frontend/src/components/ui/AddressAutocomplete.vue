<template>
  <div class="address-autocomplete">
    <input
      ref="inputRef"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :placeholder="placeholder"
      class="w-full p-2 border rounded text-gray-900 bg-white"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue'
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
let autocomplete: any = null
const { loadGoogleMaps } = useGoogleMaps()

onMounted(async () => {
  if (!inputRef.value) return

  try {
    await loadGoogleMaps()
    
    // Create autocomplete instance
    autocomplete = new window.google.maps.places.Autocomplete(inputRef.value, {
      componentRestrictions: { country: 'us' },
      fields: ['formatted_address', 'geometry', 'place_id']
    })

    // Listen for place selection
    autocomplete.addListener('place_changed', () => {
      const place = autocomplete?.getPlace()
      if (place && place.formatted_address) {
        emit('update:modelValue', place.formatted_address)
        emit('placeSelected', place)
      }
    })
  } catch (error) {
    console.error('Failed to initialize Google Maps autocomplete:', error)
  }
})

onUnmounted(() => {
  if (autocomplete && window.google) {
    window.google.maps.event.clearInstanceListeners(autocomplete)
  }
})

// Update input value when modelValue changes externally
watch(() => props.modelValue, (newValue) => {
  if (inputRef.value && inputRef.value.value !== newValue) {
    inputRef.value.value = newValue
  }
})
</script>

<style scoped>
.address-autocomplete {
  width: 100%;
}
</style>