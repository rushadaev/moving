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
      <section v-if="services.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-4 m-4 w-full max-w-3xl">
        <div
          v-for="service in services"
          :key="service.id"
          class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl p-4 flex flex-col items-center shadow border border-gray-400"
        >
          <img
            :src="getImageUrl(service.icon)"
            :alt="service.title"
            class="w-10 h-10 mb-2 object-contain"
          />
          <div class="font-bold mt-2 text-gray-800">{{ service.title }}</div>
          <div class="text-xs text-center text-gray-700" v-html="service.description"></div>
        </div>
      </section>

      <!-- Кнопки -->
      <div class="flex justify-center gap-4 m-4 w-full max-w-3xl">
        <button class="btn">GET QUOTE</button>
        <button
          class="btn bg-green-500 text-white hover:bg-green-600 cursor-pointer"
          @click="handleLeaveRequest"
          @mousedown="handleLeaveRequest"
          @touchstart="handleLeaveRequest"
          type="button"
        >
          LEAVE REQUEST
        </button>
        <button class="btn">PRICES</button>
      </div>

      <!-- Фото -->
      <section
        v-if="settings?.photo_url"
        class="rounded-xl m-4 p-4 flex flex-col items-center w-full max-w-3xl"
      >
        <h2 class="font-bold text-xl mb-2 text-white">{{ settings?.photo_title || 'Unloaded photo' }}</h2>
        <img :src="getImageUrl(settings.photo_url)" :alt="settings.photo_title" class="rounded-lg w-full max-w-md" />
      </section>

      <!-- Видео -->
      <section
        v-if="settings?.video_url"
        class="rounded-xl m-4 p-4 flex flex-col items-center w-full max-w-3xl"
      >
        <h2 class="font-bold text-xl mb-2 text-white">{{ settings?.video_title || 'Unloaded video' }}</h2>
        <!-- YouTube/Embedded video -->
        <iframe
          v-if="isYouTubeOrEmbedUrl(settings.video_url)"
          :src="getVideoEmbedUrl(settings.video_url)"
          class="rounded-lg w-full max-w-md aspect-video"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen
        ></iframe>
        <!-- Direct video file -->
        <video v-else controls class="rounded-lg w-full max-w-md">
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

const router = useRouter()
const authStore = useAuthStore()

// Reactive state for landing page data
const settings = ref(null)
const services = ref([])
const reviews = ref([])
const loading = ref(true)

// Fetch landing page data from API
async function fetchLandingData() {
  try {
    const apiUrl = import.meta.env.VITE_API_URL || 'https://mooweemoving.com/api'
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
  const baseUrl = 'https://mooweemoving.com'
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
  console.log('Auth store state:', {
    isAuthenticated: authStore.isAuthenticated,
    user: authStore.user,
    token: authStore.token ? 'exists' : 'missing'
  })

  try {
    if (!authStore.isAuthenticated) {
      console.log('User not authenticated, redirecting to /auth')
      router.push('/auth')
    } else {
      console.log('User authenticated, redirecting to /requests')
      router.push('/requests')
    }
  } catch (error) {
    console.error('Error in handleLeaveRequest:', error)
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
.icon {
  @apply underline;
}
</style>
