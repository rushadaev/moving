<script setup lang="ts">
import RequestsList from '../components/RequestsList.vue'
</script>

<template>
  <main class="p-[5px]">
    <RequestsList />
  </main>
</template>

<style scoped>
main{
  min-height: 100vh;
  display: flex;
  align-items: start;
}
</style>