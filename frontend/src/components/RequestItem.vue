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

      <!-- Tips and Review Section (for completed requests) -->
      <div v-if="item.status === 'completed' && item.fullRequest" class="w-full pt-3 border-t border-gray-700" @click.stop>
        <!-- Tips Section -->
        <div v-if="!item.fullRequest.tips_amount" class="mb-2">
          <button
            @click.stop="$emit('open-tips', item.fullRequest)"
            class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-colors text-sm"
          >
            + Add Tips
          </button>
        </div>
        <div v-else class="mb-2 bg-gray-800 rounded-lg p-2">
          <div class="flex justify-between items-center">
            <div>
              <span class="text-gray-400 text-xs">Tips:</span>
              <span class="text-green-400 text-sm font-bold ml-2">
                ${{ item.fullRequest.tips_amount }}
              </span>
              <span class="text-gray-400 text-xs ml-1">
                ({{ item.fullRequest.tips_percentage }}%)
              </span>
            </div>
            <button
              @click.stop="$emit('open-tips', item.fullRequest)"
              class="text-blue-400 hover:text-blue-300 text-xs font-medium"
            >
              Edit
            </button>
          </div>
        </div>

        <!-- Review Section -->
        <div v-if="!item.fullRequest.review">
          <button
            @click.stop="$emit('open-review', item.fullRequest)"
            class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors text-sm"
          >
            Leave a Review
          </button>
        </div>
        <div v-else class="bg-gray-800 rounded-lg p-2">
          <div class="flex items-center">
            <span class="text-gray-400 text-xs mr-2">Your Review:</span>
            <div class="flex">
              <span
                v-for="star in 5"
                :key="star"
                class="text-sm"
                :class="star <= item.fullRequest.review.rating ? 'text-yellow-400' : 'text-gray-600'"
              >
                ★
              </span>
            </div>
          </div>
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
  status?: string;
  fullRequest?: any;
}

defineProps<{
  item: RequestItem
}>()

defineEmits<{
  'open-tips': [request: any]
  'open-review': [request: any]
}>()
</script>

<style scoped>

</style>
