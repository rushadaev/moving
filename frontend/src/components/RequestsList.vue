<script setup lang="ts">
import RequestItem from './RequestItem.vue'

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
</script>

<template>
  <div
      class="grid gap-5 w-full justify-center items-center grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    <RequestItem
      v-for="item in items"
  :key="item.requestNumber"
  :item="item"
  >
  Request
  </RequestItem>
  </div>
</template>
