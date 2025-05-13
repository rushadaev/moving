import { defineStore } from 'pinia'
import { ref } from 'vue'

const apiURL = import.meta.env.VITE_API_URL || '/api'

interface RegisterCredentials {
  name: string
  email: string
  password: string
  password_confirmation: string
}

interface LoginCredentials {
  email: string
  password: string
}

interface User {
  id: number
  name: string
  email: string
  [key: string]: any
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref(localStorage.getItem('token') || '')
  const isAuthenticated = ref(!!localStorage.getItem('token'))
  const loading = ref(false)
  const error = ref('')

  async function register(credentials: RegisterCredentials) {
    loading.value = true
    error.value = ''
    
    try {
      const response = await fetch(`${apiURL}/v1/register`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(credentials)
      })
      
      const data = await response.json()
      
      if (response.ok) {
        token.value = data.token
        localStorage.setItem('token', data.token)
        isAuthenticated.value = true
        await getUser()
        return true
      } else {
        error.value = data.message || 'Registration failed'
        return false
      }
    } catch (err) {
      error.value = 'Registration failed. Please try again.'
      return false
    } finally {
      loading.value = false
    }
  }

  async function login(credentials: LoginCredentials) {
    loading.value = true
    error.value = ''
    
    try {
      const response = await fetch(`${apiURL}/v1/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(credentials)
      })
      
      const data = await response.json()
      
      if (response.ok) {
        token.value = data.token
        localStorage.setItem('token', data.token)
        isAuthenticated.value = true
        await getUser()
        return true
      } else {
        error.value = data.message || 'Login failed'
        return false
      }
    } catch (err) {
      error.value = 'Login failed. Please try again.'
      return false
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    loading.value = true
    
    try {
      if (token.value) {
        await fetch(`${apiURL}/v1/logout`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token.value}`,
            'Accept': 'application/json'
          }
        })
      }
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      token.value = ''
      user.value = null
      isAuthenticated.value = false
      localStorage.removeItem('token')
      loading.value = false
    }
  }

  async function getUser() {
    if (!token.value) return null
    
    loading.value = true
    
    try {
      const response = await fetch(`${apiURL}/v1/user`, {
        headers: {
          'Authorization': `Bearer ${token.value}`,
          'Accept': 'application/json'
        }
      })
      
      if (response.ok) {
        const data = await response.json()
        user.value = data
      } else {
        // If 401 Unauthorized, log out
        if (response.status === 401) {
          logout()
        }
      }
    } catch (err) {
      console.error('Get user error:', err)
    } finally {
      loading.value = false
    }
  }

  // Load user on store initialization if token exists
  if (token.value) {
    getUser()
  }

  return { 
    user, 
    token, 
    isAuthenticated, 
    loading, 
    error, 
    register, 
    login, 
    logout, 
    getUser 
  }
}) 