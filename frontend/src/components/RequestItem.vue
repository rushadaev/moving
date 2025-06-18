<template>
  <div>
    <div
        class="w-full p-4 bg-[var(--color-background-mute)] rounded-lg shadow-md flex flex-col gap-4 cursor-pointer hover:opacity-75"
    >
      <!-- Header Section -->
      <div class="w-full flex items-center justify-between bg-[var(--color-background)] rounded-lg p-2">
        <div class="text-[var(--color-text)] text-base font-medium font-montserrat">
          {{ item.requestNumber }}
        </div>
        <div class="flex items-center gap-2 bg-[var(--color-background-mute)] rounded-lg px-3 py-1">
          <div class="opacity-60 text-sm text-[var(--color-text)] font-semibold font-montserrat">
            Price
          </div>
          <div class="text-[var(--color-text)] text-base font-normal font-montserrat">
            {{ item.price }}
          </div>
        </div>
      </div>

      <!-- Type and Time Section -->
      <div class="w-full flex justify-between gap-4">
        <div class="flex flex-col gap-1">
          <div class="text-sm text-[var(--color-text)] opacity-60 font-semibold font-montserrat">
            Type
          </div>
          <div class="text-[var(--color-text)] text-base font-normal font-montserrat">
            {{ item.type }}
          </div>
        </div>
        <div class="flex flex-col gap-1">
          <div class="text-sm text-[var(--color-text)] opacity-60 font-semibold font-montserrat">
            Time
          </div>
          <div class="text-[var(--color-text)] text-base font-normal font-montserrat">
            {{ item.time }}
          </div>
        </div>
      </div>

      <!-- Loading Address Section -->
      <div class="w-full flex flex-col gap-1">
        <div class="text-sm text-[var(--color-text)] opacity-60 font-semibold font-montserrat">
          Loading address
        </div>
        <div class="text-[var(--color-text)] text-base font-normal font-montserrat">
          {{ item.loadingAddress }}
        </div>
      </div>

      <!-- Unloading Address Section -->
      <div class="w-full flex flex-col gap-1">
        <div class="text-sm text-[var(--color-text)] opacity-60 font-semibold font-montserrat">
          Unloading address
        </div>
        <div class="text-[var(--color-text)] text-base font-normal font-montserrat">
          {{ item.unloadingAddress }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface RequestItem {
  requestNumber: string;
  price: string | number;
  type: string;
  time: string;
  loadingAddress: string;
  unloadingAddress: string;
}

defineProps<{
  item: RequestItem
}>()
</script>

<style scoped>

</style>
