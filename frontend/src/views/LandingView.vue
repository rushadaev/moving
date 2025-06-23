<template>
  <div class="min-h-screen flex flex-col items-center bg-blue-600">
    <!-- Header -->
    <header class="p-6 flex flex-col items-center bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl m-4 w-full max-w-3xl border border-gray-400">
      <img :src="logoIcon" alt="Moowee Logo" class="w-24 mb-2" />
      <h1 class="text-2xl font-bold text-gray-800">MOOWEE</h1>
      <p class="text-center text-gray-800 mt-2 font-semibold">More than a moving company — we're your moving partner</p>
      <p class="text-xs text-gray-700 mt-1">Moowee is your trusted partner for stress-free moves.<br>
        We specialize in residential, commercial, and local moving services, delivering reliability, care, and professionalism with every box we lift. Whether you're relocating your home, office, or apartment, Moowee gets you there — smoothly, safely, and on time.
      </p>
    </header>

    <!-- Services Grid -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4 m-4 w-full max-w-3xl">
      <div class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl p-4 flex flex-col items-center shadow border border-gray-400">
        <img :src="residentMovingIcon" alt="Residential Moving" class="w-10 h-10 mb-2" />
        <div class="font-bold mt-2 text-gray-800">RESIDENTIAL MOVING</div>
        <div class="text-xs text-center text-gray-700">Apartment moving<br>House moving</div>
      </div>
      <div class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl p-4 flex flex-col items-center shadow border border-gray-400">
        <img :src="commercialMovingIcon" alt="Commercial Moving" class="w-10 h-10 mb-2" />
        <div class="font-bold mt-2 text-gray-800">COMMERCIAL MOVING</div>
        <div class="text-xs text-center text-gray-700">Corporate relocations<br>Office relocations</div>
      </div>
      <div class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl p-4 flex flex-col items-center shadow border border-gray-400">
        <img :src="packingIcon" alt="Packing Service" class="w-10 h-10 mb-2" />
        <div class="font-bold mt-2 text-gray-800">PACKING SERVICE</div>
        <div class="text-xs text-center text-gray-700">Packing service<br>Packing supplies<br>Unpacking services</div>
      </div>
      <div class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl p-4 flex flex-col items-center shadow border border-gray-400">
        <img :src="storagingIcon" alt="Storage Solution" class="w-10 h-10 mb-2" />
        <div class="font-bold mt-2 text-gray-800">STORAGE SOLUTION</div>
        <div class="text-xs text-center text-gray-700">Short-term storage<br>Long-term storage</div>
      </div>
      <div class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl p-4 flex flex-col items-center shadow border border-gray-400">
        <img :src="pianoIcon" alt="Special Movings" class="w-10 h-10 mb-2" />
        <div class="font-bold mt-2 text-gray-800">SPECIAL MOVINGS</div>
        <div class="text-xs text-center text-gray-700">Piano moving<br>Antique handling<br>Artwork and fragile items</div>
      </div>
      <div class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl p-4 flex flex-col items-center shadow border border-gray-400">
        <img :src="laborIcon" alt="Labor Services" class="w-10 h-10 mb-2" />
        <div class="font-bold mt-2 text-gray-800">LABOR SERVICES</div>
        <div class="text-xs text-center text-gray-700">Loading and unloading services<br>Furniture assembly and disassembly<br>Handyman service</div>
      </div>
      <div class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl p-4 flex flex-col items-center shadow border border-gray-400">
        <img :src="cleaningIcon" alt="Cleaning Services" class="w-10 h-10 mb-2" />
        <div class="font-bold mt-2 text-gray-800">CLEANING SERVICES</div>
        <div class="text-xs text-center text-gray-700">Pre-move cleaning<br>Post-move cleaning<br>Deep cleaning</div>
      </div>
      <div class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl p-4 flex flex-col items-center shadow border border-gray-400">
        <img :src="lastCallIcon" alt="Last Minute Services" class="w-10 h-10 mb-2" />
        <div class="font-bold mt-2 text-gray-800">LAST MINUTE SERVICES</div>
        <div class="text-xs text-center text-gray-700">Emergency moving services<br>Same-day moving options</div>
      </div>
      <div class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl p-4 flex flex-col items-center shadow border border-gray-400">
        <img :src="insuranceIcon" alt="Insurance" class="w-10 h-10 mb-2" />
        <div class="font-bold mt-2 text-gray-800">INSURANCE</div>
        <div class="text-xs text-center text-gray-700">Basic coverage<br>Full value protection<br>Additional insurance options</div>
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
    <section class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl m-4 p-4 flex flex-col items-center w-full max-w-3xl border border-gray-400">
      <h2 class="font-bold text-xl mb-2 text-gray-800">Unloaded photo</h2>
      <img src="https://placehold.co/400x400?text=Photo" alt="Unloaded photo" class="rounded-lg w-full max-w-md" />
    </section>

    <!-- Видео -->
    <section class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl m-4 p-4 flex flex-col items-center w-full max-w-3xl border border-gray-400">
      <h2 class="font-bold text-xl mb-2 text-gray-800">Unloaded video</h2>
      <video controls class="rounded-lg w-full max-w-md">
        <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4" />
      </video>
    </section>

    <!-- Отзывы -->
    <section class="flex flex-row justify-center gap-2 m-4 w-full max-w-3xl overflow-x-auto">
      <div v-for="i in 5" :key="i" class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl p-4 min-w-[200px] shadow border border-gray-400">
        <div class="flex items-center gap-2 mb-2">
          <span class="text-yellow-400">★</span>
          <span class="text-yellow-400">★</span>
          <span class="text-yellow-400">★</span>
          <span class="text-yellow-400">★</span>
          <span class="text-yellow-400">★</span>
        </div>
        <div class="text-xs text-gray-700">"Great service! Highly recommend."</div>
        <div class="text-xs mt-2 font-bold text-gray-800">— Customer {{ i }}</div>
      </div>
    </section>

    <!-- Контакты -->
    <footer class="bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 rounded-xl m-4 p-4 text-gray-800 w-full max-w-3xl border border-gray-400">
      <h2 class="font-bold text-xl">Contacts</h2>
      <div class="flex flex-col gap-1 mt-2">
        <div class="flex items-center gap-2">
          <img :src="phoneIcon" alt="Phone" class="w-5 h-5" />
          <span>+1(310) 753-42-48</span>
        </div>
        <div class="flex items-center gap-2">
          <img :src="mailIcon" alt="Mail" class="w-5 h-5" />
          <span>mooweemoving@gmail.com</span>
        </div>
        <div class="flex gap-2 mt-2">
          <a href="#" class="icon flex items-center gap-1"><img :src="instagramIcon" alt="Instagram" class="w-5 h-5" />Instagram</a>
          <a href="#" class="icon flex items-center gap-1"><img :src="facebookIcon" alt="Facebook" class="w-5 h-5" />Facebook</a>
          <a href="#" class="icon flex items-center gap-1"><img :src="youtubeIcon" alt="YouTube" class="w-5 h-5" />YouTube</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import logoIcon from '@/assets/icons/Logo.svg'
import residentMovingIcon from '@/assets/icons/Resident Moving.svg'
import commercialMovingIcon from '@/assets/icons/Commercial Moving.svg'
import packingIcon from '@/assets/icons/Packing.svg'
import storagingIcon from '@/assets/icons/Storaging.svg'
import pianoIcon from '@/assets/icons/Piano.svg'
import laborIcon from '@/assets/icons/Labor.svg'
import cleaningIcon from '@/assets/icons/Cleaning.svg'
import lastCallIcon from '@/assets/icons/Last call.svg'
import insuranceIcon from '@/assets/icons/Insurance.svg'
import instagramIcon from '@/assets/icons/Instagram.svg'
import facebookIcon from '@/assets/icons/Facebook.svg'
import youtubeIcon from '@/assets/icons/YouTube.svg'
import mailIcon from '@/assets/icons/Mail.svg'
import phoneIcon from '@/assets/icons/Phone.svg'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

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
    // Fallback navigation
    window.location.href = authStore.isAuthenticated ? '/requests' : '/auth'
  }
}
</script>

<style scoped>
.btn {
  @apply px-4 py-2 rounded-lg border border-gray-400 font-bold shadow bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 text-gray-800;
}
.icon {
  @apply underline;
}
</style>
