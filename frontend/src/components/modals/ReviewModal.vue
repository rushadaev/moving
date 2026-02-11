<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.self="skipReview"
  >
    <div class="bg-[#1e293b] rounded-xl p-6 max-w-lg w-full">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-white">Leave a Review</h2>
        <button @click="skipReview" class="text-gray-400 hover:text-white text-2xl">
          ×
        </button>
      </div>

      <!-- Company Rating -->
      <div class="mb-6">
        <h4 class="text-lg font-semibold text-white mb-3">Rate Our Service:</h4>
        <div class="flex gap-2">
          <button
            v-for="star in 5"
            :key="star"
            @click="rating = star"
            class="text-4xl transition-all hover:scale-110"
            :class="star <= rating ? 'text-yellow-400' : 'text-gray-600'"
          >
            ★
          </button>
        </div>
      </div>

      <!-- Review Text -->
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-400 mb-2">
          Tell us about your experience:
        </label>
        <textarea
          v-model="reviewText"
          placeholder="Share your thoughts..."
          rows="4"
          class="w-full bg-gray-800 text-white rounded-lg px-4 py-3 border border-gray-600 focus:border-blue-500 focus:outline-none resize-none"
        ></textarea>
      </div>

      <!-- Mover Ratings (Optional) -->
      <div class="mb-6">
        <h4 class="text-sm font-semibold text-gray-400 mb-3">
          Rate Each Mover (Optional):
        </h4>
        <div
          v-for="(mover, index) in movers"
          :key="index"
          class="flex items-center justify-between mb-3 bg-gray-800 rounded-lg p-3"
        >
          <div class="flex items-center">
            <span class="text-xl mr-2">👷</span>
            <span class="text-white">Mover {{ index + 1 }}</span>
          </div>
          <div class="flex gap-1">
            <button
              v-for="star in 5"
              :key="star"
              @click="mover.rating = star"
              class="text-xl transition-all hover:scale-110"
              :class="star <= mover.rating ? 'text-yellow-400' : 'text-gray-600'"
            >
              ★
            </button>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex gap-3">
        <button
          @click="skipReview"
          class="flex-1 py-3 rounded-lg border border-gray-600 text-white hover:bg-gray-700 transition-colors"
          :disabled="submitting"
        >
          Skip
        </button>
        <button
          @click="submitReview"
          :disabled="rating === 0 || submitting"
          class="flex-1 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ submitting ? 'Submitting...' : 'Submit Review' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useMessage } from 'naive-ui'

const props = defineProps<{
  show: boolean
  request: any
}>()

const emit = defineEmits(['update:show', 'review-submitted'])

const message = useMessage()
const submitting = ref(false)
const rating = ref(0)
const reviewText = ref('')
const movers = ref(
  Array(props.request.movers_count || 2).fill(0).map((_, i) => ({
    mover_number: i + 1,
    rating: 0
  }))
)

async function submitReview() {
  if (rating.value === 0) {
    message.warning('Please rate our service')
    return
  }

  submitting.value = true

  try {
    const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:80/api'
    const token = localStorage.getItem('token')

    const response = await fetch(`${apiUrl}/v1/reviews`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({
        request_id: props.request.id,
        rating: rating.value,
        review_text: reviewText.value || null,
        mover_ratings: movers.value.filter(m => m.rating > 0)
      })
    })

    if (response.ok) {
      message.success('Thank you for your review!')
      emit('review-submitted')
      emit('update:show', false)
    } else {
      const error = await response.json()
      throw new Error(error.message || 'Failed to submit review')
    }
  } catch (error: any) {
    console.error('Review error:', error)
    message.error(error.message || 'Failed to submit review')
  } finally {
    submitting.value = false
  }
}

function skipReview() {
  emit('update:show', false)
}
</script>
