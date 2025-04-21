<script setup lang="ts">
import RequestItem from './RequestItem.vue'
import { useRouter } from 'vue-router';

const router = useRouter();

const items = [
  {
    requestNumber: "Request #1",
    price: "12$",
    type: "3 bedroom",
    time: "12/11/24 | 12:00",
    loadingAddress: "123 Main St, City A",
    unloadingAddress: "456 Elm St, City B",
  },
  {
    requestNumber: "Request #2",
    price: "15$",
    type: "2 bedroom",
    time: "12/12/24 | 14:00",
    loadingAddress: "789 Oak St, City C",
    unloadingAddress: "321 Pine St, City D",
  },
  {
    requestNumber: "Request #3",
    price: "18$",
    type: "1 bedroom",
    time: "12/13/24 | 10:30",
    loadingAddress: "456 Maple St, City E",
    unloadingAddress: "654 Birch St, City F",
  },
  {
    requestNumber: "Request #4",
    price: "20$",
    type: "4 bedroom",
    time: "12/14/24 | 09:00",
    loadingAddress: "101 Walnut St, City G",
    unloadingAddress: "202 Cedar St, City H",
  },
]

// Navigate to details view when a request is clicked
const showRequestDetails = (item) => {
  // Store the selected request in localStorage to access it on the details page
  localStorage.setItem('selectedRequest', JSON.stringify(item));
  router.push('/details');
};
</script>

<template>
  <div class="w-full">
    <h1 class="text-2xl font-bold mb-4 ml-2">Requests</h1>
    <div class="grid gap-5 w-full justify-center items-center grid-cols-1">
      <RequestItem
        v-for="item in items"
        :key="item.requestNumber"
        :item="item"
        @click="showRequestDetails(item)"
        class="cursor-pointer"
      >
        Request
      </RequestItem>
    </div>
  </div>
</template>
