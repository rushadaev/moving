import { ref } from 'vue'

const isLoaded = ref(false)
const isLoading = ref(false)

export function useGoogleMaps() {
  const loadGoogleMaps = async () => {
    if (isLoaded.value) return
    if (isLoading.value) {
      // Wait for ongoing loading
      await new Promise<void>((resolve) => {
        const checkInterval = setInterval(() => {
          if (isLoaded.value) {
            clearInterval(checkInterval)
            resolve()
          }
        }, 100)
      })
      return
    }

    isLoading.value = true

    return new Promise<void>((resolve, reject) => {
      if (window.google && window.google.maps) {
        isLoaded.value = true
        isLoading.value = false
        resolve()
        return
      }

      // Create callback function for Google Maps
      const callbackName = 'initGoogleMaps'
      ;(window as any)[callbackName] = () => {
        isLoaded.value = true
        isLoading.value = false
        resolve()
        delete (window as any)[callbackName]
      }

      const script = document.createElement('script')
      script.src = `https://maps.googleapis.com/maps/api/js?key=${import.meta.env.VITE_GOOGLE_MAPS_API_KEY}&libraries=places&callback=${callbackName}`
      script.async = true
      script.defer = true

      script.onerror = () => {
        isLoading.value = false
        reject(new Error('Failed to load Google Maps'))
      }

      document.head.appendChild(script)
    })
  }

  return {
    loadGoogleMaps,
    isLoaded
  }
}