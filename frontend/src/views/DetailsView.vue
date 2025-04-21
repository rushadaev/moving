<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Card from '@/components/ui/Card.vue';
import GradientButton from '@/components/ui/GradientButton.vue';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';

const router = useRouter();
const request = ref(null);

onMounted(() => {
  // Get selected request from localStorage
  const selectedRequest = localStorage.getItem('selectedRequest');
  if (selectedRequest) {
    request.value = JSON.parse(selectedRequest);
  } else {
    // If no request is found, redirect back to the requests page
    router.push('/');
  }
});

const takeRequest = () => {
  console.log('Taking request:', request.value?.requestNumber);
  // Implement your logic for taking the request
};
</script>

<template>
  <div class="details-view">
    <Header title="Request details" />
    
    <main v-if="request" class="p-5 pb-24">
      <!-- Request Number Section -->
      <div class="bg-white rounded-lg p-4 mb-4 shadow-sm">
        <h2 class="text-gray-500 text-sm">Request number</h2>
        <p class="font-medium">{{ request.requestNumber.replace('Request #', '') }}</p>
      </div>
      
      <!-- Type Section -->
      <div class="bg-white rounded-lg p-4 mb-4 shadow-sm">
        <h2 class="text-gray-500 text-sm">Type</h2>
        <p class="font-medium">Residential - {{ request.type }}</p>
      </div>
      
      <!-- Date and Time Section -->
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="bg-white rounded-lg p-4 shadow-sm">
          <h2 class="text-gray-500 text-sm">Date</h2>
          <p class="font-medium">{{ request.time.split('|')[0].trim() }}</p>
        </div>
        <div class="bg-white rounded-lg p-4 shadow-sm">
          <h2 class="text-gray-500 text-sm">Time</h2>
          <p class="font-medium">{{ request.time.split('|')[1].trim() }}</p>
        </div>
      </div>
      
      <!-- Loading Address Section -->
      <div class="bg-white rounded-lg p-4 mb-4 shadow-sm">
        <h2 class="text-gray-500 text-sm">Loading address</h2>
        <p class="font-medium">{{ request.loadingAddress }}</p>
      </div>
      
      <!-- Unloading Address Section -->
      <div class="bg-white rounded-lg p-4 mb-4 shadow-sm">
        <h2 class="text-gray-500 text-sm">Unloading address</h2>
        <p class="font-medium">{{ request.unloadingAddress }}</p>
      </div>
      
      <!-- Price Section -->
      <div class="mb-6">
        <h2 class="text-lg font-semibold mb-2">Approximate price</h2>
        <div class="bg-white rounded-lg p-4 shadow-sm">
          <h2 class="text-gray-500 text-sm">Price</h2>
          <p class="font-medium">{{ request.price }}</p>
        </div>
      </div>
      
      <!-- Action Button -->
      <GradientButton @click="takeRequest" class="w-full">
        Take request
      </GradientButton>
    </main>
    
    <Footer />
  </div>
</template>

<style scoped>
.details-view {
  min-height: 100vh;
  padding-top: 55px; /* Height of the header */
  background-color: #f5f5f7;
}
</style>
