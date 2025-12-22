<template>
  <div class="min-h-screen mt-[-60px] flex flex-col items-center bg-[#144560]">
    <!-- Loading state -->
    <div v-if="loading" class="flex items-center justify-center min-h-screen">
      <div class="text-white text-xl">Loading...</div>
    </div>

    <template v-else>
      <!-- Header -->
      <header class="p-6 flex flex-col items-center bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl m-4 w-full max-w-3xl border border-gray-400">
        <img :src="getImageUrl(settings?.logo) || logoIcon" alt="Company Logo" class="w-24 mb-2" />
        <h1 class="text-2xl font-bold text-gray-800">{{ settings?.company_name || 'MOOWEE' }}</h1>
        <p v-if="settings?.tagline" class="text-center text-gray-800 mt-2 font-semibold">{{ settings.tagline }}</p>
        <p v-if="settings?.description" class="text-xs text-gray-700 mt-1" v-html="settings.description"></p>
      </header>

      <!-- Services Grid -->
      <section v-if="services.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 m-4 w-full max-w-6xl">
        <div
          v-for="service in services"
          :key="service.id"
          class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl p-6 flex flex-col items-center text-center shadow border border-gray-400 gap-3 cursor-pointer hover:shadow-lg transition-shadow"
          @click="handleBookMoving(service)"
        >
          <img
            :src="getImageUrl(service.icon)"
            :alt="service.title"
            class="w-20 h-20 object-contain"
          />
          <div class="font-bold text-base text-gray-800">{{ service.title }}</div>
          <div class="text-xs text-gray-700 line-clamp-3" v-html="service.description"></div>
        </div>
      </section>

      <!-- Кнопки -->
      <div class="flex justify-center gap-4 m-4 w-full max-w-3xl">
        <button
          class="btn bg-blue-600 text-white hover:bg-blue-700 cursor-pointer"
          @click="handleBookMoving(null)"
          type="button"
        >
          BOOK MOVING
        </button>
        <button
          class="btn bg-green-500 text-white hover:bg-green-600 cursor-pointer"
          @click="handleLeaveRequest"
          type="button"
        >
          LEAVE REQUEST
        </button>
        <button
          class="btn bg-purple-600 text-white hover:bg-purple-700 cursor-pointer"
          @click="showPricesModal = true"
          type="button"
        >
          PRICES
        </button>
      </div>

      <!-- Prices Modal -->
      <div
        v-if="showPricesModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        @click.self="showPricesModal = false"
      >
        <div class="bg-white rounded-xl p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Our Prices</h2>
            <button
              @click="showPricesModal = false"
              class="text-gray-500 hover:text-gray-700 text-2xl"
            >
              ×
            </button>
          </div>

          <div class="space-y-6">
            <!-- Hourly Rate -->
            <div class="border-b pb-4">
              <h3 class="font-bold text-lg text-gray-800 mb-2">Base Rates</h3>
              <div class="flex justify-between items-center">
                <span class="text-gray-700">You'll only pay per hour:</span>
                <span class="font-bold text-blue-600">${{ settings?.hourly_rate || '100.00' }} / hour</span>
              </div>
            </div>

            <!-- Additional Fees -->
            <div class="border-b pb-4" v-if="settings?.floor_fee > 0 || settings?.transportation_fee_per_mile > 0">
              <h3 class="font-bold text-lg text-gray-800 mb-2">Additional Fees</h3>
              <div class="space-y-2">
                <div v-if="settings?.floor_fee > 0" class="flex justify-between items-center">
                  <span class="text-gray-700">Additional Fee per Floor:</span>
                  <span class="font-bold text-blue-600">${{ settings.floor_fee }} / floor</span>
                </div>
                <div v-if="settings?.transportation_fee_per_mile" class="flex justify-between items-center">
                  <span class="text-gray-700">Transportation Fee:</span>
                  <span class="font-bold text-blue-600">${{ settings.transportation_fee_per_mile }} / mile</span>
                </div>
              </div>
            </div>

            <!-- Packing Materials -->
            <div class="border-b pb-4">
              <h3 class="font-bold text-lg text-gray-800 mb-2">Packing Materials Prices</h3>
              <div class="space-y-2">
                <div class="flex justify-between items-center">
                  <span class="text-gray-700">Small Box:</span>
                  <span class="font-bold text-blue-600">${{ settings?.small_box_price || '3.00' }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-700">Medium Box:</span>
                  <span class="font-bold text-blue-600">${{ settings?.medium_box_price || '5.00' }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-700">Large Box:</span>
                  <span class="font-bold text-blue-600">${{ settings?.large_box_price || '7.00' }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-700">Wardrobe Box:</span>
                  <span class="font-bold text-blue-600">${{ settings?.wardrobe_box_price || '12.00' }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-700">Paper:</span>
                  <span class="font-bold text-blue-600">${{ settings?.paper_price || '6.00' }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-700">Plastic Tape:</span>
                  <span class="font-bold text-blue-600">${{ settings?.plastic_tape_price || '4.00' }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-700">Bubble Wrap:</span>
                  <span class="font-bold text-blue-600">${{ settings?.bubble_wrap_price || '10.00' }}</span>
                </div>
              </div>
            </div>

            <!-- Special Services -->
            <div v-if="settings?.piano_fee > 0 || settings?.gun_safe_fee > 0">
              <h3 class="font-bold text-lg text-gray-800 mb-2">Special Services</h3>
              <div class="space-y-2">
                <div v-if="settings?.piano_fee > 0" class="flex justify-between items-center">
                  <span class="text-gray-700">Piano Fee:</span>
                  <span class="font-bold text-blue-600">${{ settings.piano_fee }}</span>
                </div>
                <div v-if="settings?.gun_safe_fee > 0" class="flex justify-between items-center">
                  <span class="text-gray-700">Gun Safe Fee:</span>
                  <span class="font-bold text-blue-600">${{ settings.gun_safe_fee }}</span>
                </div>
              </div>
            </div>
          </div>

          <button
            @click="showPricesModal = false"
            class="mt-6 w-full btn bg-blue-600 text-white hover:bg-blue-700"
          >
            Close
          </button>
        </div>
      </div>

      <!-- Leave Request Modal -->
      <div
        v-if="showLeaveRequestModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        @click.self="showLeaveRequestModal = false"
      >
        <div class="bg-[#1e293b] rounded-xl p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-white">Leave a Request</h2>
            <button
              @click="showLeaveRequestModal = false"
              class="text-gray-400 hover:text-white text-2xl"
            >
              ×
            </button>
          </div>

          <RequestForm
            v-if="authStore.isAuthenticated"
            ref="requestFormRef"
            v-model="requestFormData"
            mode="create"
          />
          <div v-else class="text-white text-center py-8">
            <p class="mb-4">Please login to leave a request</p>
            <button
              @click="router.push('/auth')"
              class="btn bg-blue-600 text-white hover:bg-blue-700"
            >
              Login
            </button>
          </div>
        </div>
      </div>

      <!-- Фото -->
      <section
        v-if="settings?.photo_url"
        class="m-4 flex flex-col items-center w-full max-w-3xl"
      >
        <img :src="getImageUrl(settings.photo_url)" :alt="settings.photo_title" class="rounded-lg w-full aspect-video object-cover" />
      </section>

      <!-- Видео -->
      <section
        v-if="settings?.video_url"
        class="m-4 flex flex-col items-center w-full max-w-3xl"
      >
        <!-- YouTube/Embedded video -->
        <iframe
          v-if="isYouTubeOrEmbedUrl(settings.video_url)"
          :src="getVideoEmbedUrl(settings.video_url)"
          class="rounded-lg w-full aspect-video"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          allowfullscreen
          referrerpolicy="strict-origin-when-cross-origin"
        ></iframe>
        <!-- Direct video file -->
        <video v-else controls class="rounded-lg w-full aspect-video">
          <source :src="settings.video_url" type="video/mp4" />
        </video>
      </section>

      <!-- Отзывы -->
      <section v-if="reviews.length > 0" class="flex flex-row justify-center gap-2 m-4 w-full max-w-3xl overflow-x-auto">
        <div
          v-for="review in reviews"
          :key="review.id"
          class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl p-4 min-w-[200px] shadow border border-gray-400"
        >
          <div class="flex items-center gap-1 mb-2">
            <span
              v-for="(filled, index) in getStars(review.rating)"
              :key="index"
              :class="filled ? 'text-yellow-400' : 'text-gray-400'"
            >
              ★
            </span>
          </div>
          <div class="text-xs text-gray-700">"{{ review.review_text }}"</div>
          <div class="text-xs mt-2 font-bold text-gray-800">— {{ review.customer_name }}</div>
        </div>
      </section>

      <!-- Контакты -->
      <footer class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl m-4 p-4 text-gray-800 w-full max-w-3xl border border-gray-400">
        <h2 class="font-bold text-xl">Contacts</h2>
        <div class="flex flex-col gap-1 mt-2">
          <div v-if="settings?.phone" class="flex items-center gap-2">
            <img :src="phoneIcon" alt="Phone" class="w-5 h-5" />
            <span>{{ settings.phone }}</span>
          </div>
          <div v-if="settings?.email" class="flex items-center gap-2">
            <img :src="mailIcon" alt="Mail" class="w-5 h-5" />
            <span>{{ settings.email }}</span>
          </div>
          <div class="flex gap-2 mt-2">
            <a v-if="settings?.instagram_url" :href="settings.instagram_url" target="_blank" class="icon flex items-center gap-1">
              <img :src="instagramIcon" alt="Instagram" class="w-5 h-5" />Instagram
            </a>
            <a v-if="settings?.facebook_url" :href="settings.facebook_url" target="_blank" class="icon flex items-center gap-1">
              <img :src="facebookIcon" alt="Facebook" class="w-5 h-5" />Facebook
            </a>
            <a v-if="settings?.youtube_url" :href="settings.youtube_url" target="_blank" class="icon flex items-center gap-1">
              <img :src="youtubeIcon" alt="YouTube" class="w-5 h-5" />YouTube
            </a>
          </div>
        </div>
      </footer>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import logoIcon from '@/assets/icons/Logo.svg'
import instagramIcon from '@/assets/icons/Instagram.svg'
import facebookIcon from '@/assets/icons/Facebook.svg'
import youtubeIcon from '@/assets/icons/YouTube.svg'
import mailIcon from '@/assets/icons/Mail.svg'
import phoneIcon from '@/assets/icons/Phone.svg'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import RequestForm from '@/components/forms/RequestForm.vue'

const router = useRouter()
const authStore = useAuthStore()

// Reactive state for landing page data
const settings = ref(null)
const services = ref([])
const reviews = ref([])
const loading = ref(true)

// Modal states
const showPricesModal = ref(false)
const showLeaveRequestModal = ref(false)

// Request form data
const requestFormData = ref({
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
const requestFormRef = ref(null)

// Fetch landing page data from API
async function fetchLandingData() {
  try {
    const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:80/api'
    const response = await fetch(`${apiUrl}/landing`)
    const data = await response.json()

    settings.value = data.settings
    services.value = data.services
    reviews.value = data.reviews

    console.log('Landing page data loaded:', data)
  } catch (error) {
    console.error('Error loading landing page data:', error)
  } finally {
    loading.value = false
  }
}

// Get full URL for uploaded images
function getImageUrl(path) {
  if (!path) return null
  if (path.startsWith('http')) return path

  // Use environment variable or fallback to localhost in development
  const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:80'
  const baseUrl = apiUrl.replace('/api', '')

  return `${baseUrl}/storage/${path}`
}

// Detect if URL is YouTube or embed video
function isYouTubeOrEmbedUrl(url) {
  if (!url) return false
  return url.includes('youtube.com') ||
         url.includes('youtu.be') ||
         url.includes('embed')
}

// Convert YouTube watch URL to embed URL
function getVideoEmbedUrl(url) {
  if (!url) return null

  // Already an embed URL
  if (url.includes('/embed/')) return url

  // Convert youtube.com/watch?v=VIDEO_ID to youtube.com/embed/VIDEO_ID
  if (url.includes('youtube.com/watch')) {
    const videoId = url.split('v=')[1]?.split('&')[0]
    if (videoId) return `https://www.youtube.com/embed/${videoId}`
  }

  // Convert youtu.be/VIDEO_ID to youtube.com/embed/VIDEO_ID
  if (url.includes('youtu.be/')) {
    const videoId = url.split('youtu.be/')[1]?.split('?')[0]
    if (videoId) return `https://www.youtube.com/embed/${videoId}`
  }

  // Return original URL if no conversion needed
  return url
}

// Generate star rating
function getStars(rating) {
  return Array(5).fill(0).map((_, i) => i < rating)
}

function handleLeaveRequest() {
  console.log('LEAVE REQUEST button clicked!')
  // Open modal with request form
  showLeaveRequestModal.value = true
}

function handleBookMoving(service) {
  console.log('BOOK MOVING button clicked for service:', service)
  // Redirect to requests page
  try {
    if (!authStore.isAuthenticated) {
      router.push('/auth')
    } else {
      router.push('/requests')
    }
  } catch (error) {
    console.error('Error in handleBookMoving:', error)
    window.location.href = authStore.isAuthenticated ? '/requests' : '/auth'
  }
}

onMounted(() => {
  fetchLandingData()
})
</script>

<style scoped>
.btn {
  @apply px-4 py-2 rounded-lg border border-gray-400 font-bold shadow bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 text-gray-800;
}

.btn-book-moving {
  @apply px-6 py-3 rounded-lg font-bold shadow bg-blue-600 text-white hover:bg-blue-700 transition-colors cursor-pointer border-none;
}

.icon {
  @apply underline;
}
</style>
