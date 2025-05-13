<script setup lang="ts">
import RequestsList from '../components/RequestsList.vue'
import { useRequestsStore } from '../stores/requests'
import { useAuthStore } from '../stores/auth'
import { onMounted, ref } from 'vue'

const requestsStore = useRequestsStore()
const authStore = useAuthStore()
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
  <main class="p-[5px] w-full">
    <div v-if="!authStore.isAuthenticated" class="text-center p-6">
      <h2 class="text-xl font-bold mb-4">Please login to view your requests</h2>
      <!-- Login form could be added here or as a separate component -->
    </div>
    <div v-else-if="isLoading" class="text-center p-6">
      <p>Loading requests...</p>
    </div>
    <div v-else>
      <RequestsList :requests="requestsStore.requests" />
    </div>
  </main>
</template>

<style scoped>
main{
  min-height: 100vh;
  display: flex;
  align-items: start;
}
</style>