<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import Header from '@/components/Header.vue'
import Footer from '@/components/Footer.vue'
import GradientButton from '@/components/ui/GradientButton.vue'

const router = useRouter()
const authStore = useAuthStore()

const isLogin = ref(true)
const loading = ref(false)
const errorMessage = ref('')

// Form data
const loginForm = ref({
  email: '',
  password: ''
})

const registerForm = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

// Toggle between login and register forms
function toggleForm() {
  isLogin.value = !isLogin.value
  errorMessage.value = ''
}

// Form validation
const isLoginValid = computed(() => {
  return loginForm.value.email.trim() !== '' && 
         loginForm.value.password.trim() !== ''
})

const isRegisterValid = computed(() => {
  return registerForm.value.name.trim() !== '' && 
         registerForm.value.email.trim() !== '' && 
         registerForm.value.password.trim() !== '' &&
         registerForm.value.password_confirmation.trim() !== '' &&
         registerForm.value.password === registerForm.value.password_confirmation
})

// Handle login
async function handleLogin() {
  if (!isLoginValid.value || loading.value) return
  
  loading.value = true
  errorMessage.value = ''
  
  try {
    const success = await authStore.login(loginForm.value)
    if (success) {
      // Check if we have a redirect query parameter
      const redirectPath = router.currentRoute.value.query.redirect
      if (redirectPath && typeof redirectPath === 'string') {
        router.push(redirectPath)
      } else {
        router.push('/')
      }
    } else {
      errorMessage.value = authStore.error || 'Login failed. Please try again.'
    }
  } catch (error) {
    errorMessage.value = 'An unexpected error occurred. Please try again later.'
    console.error('Login error:', error)
  } finally {
    loading.value = false
  }
}

// Handle registration
async function handleRegister() {
  if (!isRegisterValid.value || loading.value) return
  
  loading.value = true
  errorMessage.value = ''
  
  try {
    const success = await authStore.register(registerForm.value)
    if (success) {
      // Check if we have a redirect query parameter
      const redirectPath = router.currentRoute.value.query.redirect
      if (redirectPath && typeof redirectPath === 'string') {
        router.push(redirectPath)
      } else {
        router.push('/')
      }
    } else {
      errorMessage.value = authStore.error || 'Registration failed. Please try again.'
    }
  } catch (error) {
    errorMessage.value = 'An unexpected error occurred. Please try again later.'
    console.error('Register error:', error)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-view">
    <Header :title="isLogin ? 'Login' : 'Register'" />
    
    <main class="p-5 pb-24">
      <!-- Error Message -->
      <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <p>{{ errorMessage }}</p>
      </div>
      
      <!-- Login Form -->
      <form v-if="isLogin" @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Email</label>
          <input 
            type="email" 
            v-model="loginForm.email" 
            class="w-full p-2 border rounded" 
            placeholder="you@example.com"
            required
          />
        </div>
        
        <div>
          <label class="block text-sm font-medium mb-1">Password</label>
          <input 
            type="password" 
            v-model="loginForm.password" 
            class="w-full p-2 border rounded"
            placeholder="••••••••"
            required
          />
        </div>
        
        <GradientButton 
          small-button="true"
          type="submit" 
          class="w-full" 
          :disabled="!isLoginValid || loading"
        >
          {{ loading ? 'Logging in...' : 'Login' }}
        </GradientButton>
        
        <div class="text-center mt-4">
          <p>
            Don't have an account?
            <button 
              type="button" 
              @click="toggleForm" 
              class="text-blue-500 hover:text-blue-700 ml-1"
            >
              Register
            </button>
          </p>
        </div>
      </form>
      
      <!-- Register Form -->
      <form v-else @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Name</label>
          <input 
            type="text" 
            v-model="registerForm.name" 
            class="w-full p-2 border rounded" 
            placeholder="Your Name"
            required
          />
        </div>
        
        <div>
          <label class="block text-sm font-medium mb-1">Email</label>
          <input 
            type="email" 
            v-model="registerForm.email" 
            class="w-full p-2 border rounded" 
            placeholder="you@example.com"
            required
          />
        </div>
        
        <div>
          <label class="block text-sm font-medium mb-1">Password</label>
          <input 
            type="password" 
            v-model="registerForm.password" 
            class="w-full p-2 border rounded"
            placeholder="••••••••"
            required
          />
        </div>
        
        <div>
          <label class="block text-sm font-medium mb-1">Confirm Password</label>
          <input 
            type="password" 
            v-model="registerForm.password_confirmation" 
            class="w-full p-2 border rounded"
            placeholder="••••••••"
            required
          />
          <p v-if="registerForm.password !== registerForm.password_confirmation && registerForm.password_confirmation" 
             class="text-red-500 text-sm mt-1">
            Passwords do not match
          </p>
        </div>
        
        <GradientButton 
          type="submit" 
          class="w-full" 
          :disabled="!isRegisterValid || loading"
        >
          {{ loading ? 'Creating account...' : 'Register' }}
        </GradientButton>
        
        <div class="text-center mt-4">
          <p>
            Already have an account?
            <button 
              type="button" 
              @click="toggleForm" 
              class="text-blue-500 hover:text-blue-700 ml-1"
            >
              Login
            </button>
          </p>
        </div>
      </form>
    </main>
    
    <Footer />
  </div>
</template>

<style scoped>
.auth-view {
  min-height: 100vh;
  padding-top: 55px; /* Height of the header */
  background-color: var(--color-background-soft);
}
</style> 