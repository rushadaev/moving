<script setup lang="ts">
import RequestsList from '../components/RequestsList.vue'
import { useRequestsStore } from '../stores/requests'
import { useAuthStore } from '../stores/auth'
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { NButton, NSpin } from 'naive-ui'
import GradientButton from '../components/ui/GradientButton.vue'
import Header from '../components/Header.vue'
const requestsStore = useRequestsStore()
const authStore = useAuthStore()
const router = useRouter()
const isLoading = ref(false)

onMounted(async () => {
  if (authStore.isAuthenticated) {
    isLoading.value = true
    await requestsStore.fetchRequests()
    isLoading.value = false
  }
})
</script>

<template>
  <main class="requests-page">
    <Header title="Moving App"/>
    <div v-if="!authStore.isAuthenticated" class="auth-required">
      <h2>Please login to view your requests</h2>
      <GradientButton @click="router.push('/auth')">
        Login
      </GradientButton>
    </div>
    <div v-else-if="isLoading" class="loading-container">
      <n-spin size="medium" />
      <p>Loading requests...</p>
    </div>
    <div v-else class="requests-container">
      <RequestsList :requests="requestsStore.requests" />
    </div>
  </main>
</template>

<style scoped>
.requests-page {
  min-height: calc(100vh - 125px); /* Adjusted for header and footer */
  width: 100%;
  max-width: 768px;
  margin: 0 auto;
  padding: 20px;
}

.auth-required {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 20px;
  min-height: 400px;
  text-align: center;
}

.auth-required h2 {
  font-size: 20px;
  font-weight: 600;
  color: var(--color-text);
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
  min-height: 400px;
}

.loading-container p {
  color: var(--color-text-secondary);
}

.requests-container {
  width: 100%;
}

@media (max-width: 640px) {
  .requests-page {
    padding: 10px;
  }
}
</style>