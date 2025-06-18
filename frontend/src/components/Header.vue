<template>
  <header class="header">
    <button @click="goBack" class="back-button">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M15.41 16.59L10.83 12L15.41 7.41L14 6L8 12L14 18L15.41 16.59Z" fill="currentColor"/>
      </svg>
    </button>
    
    <h1 class="title">{{ title || 'Moving App' }}</h1>
    
    <div class="auth-controls">
      <button v-if="isAuthenticated" @click="logout" class="auth-button">
        <span class="hidden sm:inline">Logout</span>
        
      </button>
      <button v-else @click="goToAuth" class="auth-button">
        <span class="hidden sm:inline">Login</span>
        
      </button>
    </div>
  </header>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

defineProps<{
  title?: string
}>()

const router = useRouter()
const authStore = useAuthStore()

const isAuthenticated = computed(() => authStore.isAuthenticated)
const userName = computed(() => authStore.user?.name || 'User')

const goBack = () => {
  router.back()
}

const goToAuth = () => {
  router.push('/auth')
}

const logout = async () => {
  await authStore.logout()
  router.push('/auth')
}
</script>

<style scoped>
.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  background-color: var(--color-background-mute);
  height: 55px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 15px;
  z-index: 1000;
  border-bottom: 1px solid var(--color-border);
}

.back-button {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-text);
  border: none;
  background: transparent;
  cursor: pointer;
}

.title {
  font-size: 18px;
  font-weight: 600;
  color: var(--color-text);
  flex: 1;
  text-align: center;
}

.auth-controls {
  width: 40px;
  display: flex;
  justify-content: flex-end;
}

.auth-button {
  display: flex;
  align-items: center;
  color: var(--color-text);
  background: transparent;
  border: none;
  cursor: pointer;
  font-size: 14px;
}

@media (min-width: 640px) {
  .auth-controls,
  .auth-button {
    width: auto;
  }
}
</style>