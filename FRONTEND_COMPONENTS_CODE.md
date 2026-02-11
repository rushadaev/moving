# Frontend Components - Ready to Use Code

## 1. Create TipsModal.vue

File: `frontend/src/components/modals/TipsModal.vue`

```vue
<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.self="close"
  >
    <div class="bg-[#1e293b] rounded-xl p-6 max-w-lg w-full">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-white">Add Tips</h2>
        <button @click="close" class="text-gray-400 hover:text-white text-2xl">
          ×
        </button>
      </div>

      <!-- Total Cost -->
      <div class="bg-gray-800 rounded-lg p-4 mb-6">
        <div class="flex justify-between text-gray-300">
          <span>Moving Cost:</span>
          <span class="text-xl font-bold text-white">${{ request.price }}</span>
        </div>
      </div>

      <!-- Preset Buttons -->
      <div class="grid grid-cols-5 gap-2 mb-6">
        <button
          v-for="preset in [10, 15, 20, 25]"
          :key="preset"
          @click="selectPreset(preset)"
          :class="[
            'py-3 px-2 rounded-lg font-semibold transition-colors',
            selectedPreset === preset
              ? 'bg-blue-600 text-white'
              : 'bg-gray-700 text-gray-300 hover:bg-gray-600'
          ]"
        >
          {{ preset }}%
        </button>
        <button
          @click="selectCustom"
          :class="[
            'py-3 px-2 rounded-lg font-semibold transition-colors',
            isCustom
              ? 'bg-blue-600 text-white'
              : 'bg-gray-700 text-gray-300 hover:bg-gray-600'
          ]"
        >
          Custom
        </button>
      </div>

      <!-- Custom Input -->
      <div v-if="isCustom" class="grid grid-cols-2 gap-4 mb-6">
        <div>
          <label class="block text-sm text-gray-400 mb-2">Amount ($)</label>
          <input
            v-model.number="customAmount"
            @input="onAmountChange"
            type="number"
            min="0"
            step="0.01"
            class="w-full bg-gray-800 text-white rounded-lg px-4 py-2 border border-gray-600 focus:border-blue-500 focus:outline-none"
            placeholder="0.00"
          />
        </div>
        <div>
          <label class="block text-sm text-gray-400 mb-2">Percentage (%)</label>
          <input
            v-model.number="customPercentage"
            @input="onPercentageChange"
            type="number"
            min="0"
            max="100"
            step="0.1"
            class="w-full bg-gray-800 text-white rounded-lg px-4 py-2 border border-gray-600 focus:border-blue-500 focus:outline-none"
            placeholder="0.0"
          />
        </div>
      </div>

      <!-- Tips Summary -->
      <div class="bg-gray-800 rounded-lg p-4 mb-6">
        <div class="flex justify-between mb-2">
          <span class="text-gray-400">Tips Amount:</span>
          <span class="text-xl font-bold text-green-400">${{ tipsAmount.toFixed(2) }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-400">Percentage:</span>
          <span class="text-lg text-gray-300">{{ tipsPercentage.toFixed(1) }}%</span>
        </div>
      </div>

      <!-- Movers Distribution -->
      <div class="bg-gray-800 rounded-lg p-4 mb-6">
        <h4 class="text-sm font-semibold text-gray-400 mb-3">Distribution per Mover:</h4>
        <div
          v-for="(mover, index) in distribution"
          :key="index"
          class="flex items-center justify-between mb-2 last:mb-0"
        >
          <div class="flex items-center">
            <span class="text-2xl mr-2">👷</span>
            <span class="text-white">Mover {{ index + 1 }}</span>
          </div>
          <span class="text-green-400 font-semibold">${{ mover.amount.toFixed(2) }}</span>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex gap-3">
        <button
          @click="close"
          class="flex-1 py-3 rounded-lg border border-gray-600 text-white hover:bg-gray-700 transition-colors"
          :disabled="loading"
        >
          Cancel
        </button>
        <button
          @click="payTips"
          :disabled="tipsAmount <= 0 || loading"
          class="flex-1 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? 'Processing...' : 'Pay Tips with Stripe' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { loadStripe } from '@stripe/stripe-js'
import { useMessage } from 'naive-ui'

const props = defineProps<{
  show: boolean
  request: any
}>()

const emit = defineEmits(['update:show', 'tips-paid'])

const message = useMessage()
const loading = ref(false)
const selectedPreset = ref<number | null>(null)
const isCustom = ref(false)
const customAmount = ref(0)
const customPercentage = ref(0)

const tipsAmount = computed(() => {
  if (isCustom.value) {
    return customAmount.value || 0
  } else if (selectedPreset.value) {
    return (props.request.price * selectedPreset.value) / 100
  }
  return 0
})

const tipsPercentage = computed(() => {
  if (isCustom.value) {
    return customPercentage.value || 0
  }
  return selectedPreset.value || 0
})

const distribution = computed(() => {
  const moversCount = props.request.movers_count || 2
  const perMover = tipsAmount.value / moversCount
  return Array(moversCount).fill(0).map((_, i) => ({
    mover_number: i + 1,
    amount: perMover
  }))
})

function selectPreset(percentage: number) {
  selectedPreset.value = percentage
  isCustom.value = false
}

function selectCustom() {
  isCustom.value = true
  selectedPreset.value = null
  customAmount.value = 0
  customPercentage.value = 0
}

function onAmountChange() {
  if (props.request.price > 0) {
    customPercentage.value = (customAmount.value / props.request.price) * 100
  }
}

function onPercentageChange() {
  customAmount.value = (props.request.price * customPercentage.value) / 100
}

async function payTips() {
  if (tipsAmount.value <= 0) {
    message.error('Tips amount must be greater than 0')
    return
  }

  loading.value = true

  try {
    const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:80/api'
    const token = localStorage.getItem('token')

    // 1. Save tips to backend
    await fetch(`${apiUrl}/v1/tips/store`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({
        request_id: props.request.id,
        tips_amount: tipsAmount.value,
        tips_percentage: tipsPercentage.value,
        tips_distribution: distribution.value
      })
    })

    // 2. Create Stripe session
    const paymentResponse = await fetch(`${apiUrl}/v1/tips/create-payment`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({
        request_id: props.request.id
      })
    })

    const paymentData = await paymentResponse.json()

    // 3. Redirect to Stripe
    if (paymentData.redirectUrl) {
      window.location.href = paymentData.redirectUrl
    } else {
      throw new Error('No redirect URL received')
    }

  } catch (error: any) {
    console.error('Payment error:', error)
    message.error(error.message || 'Failed to process payment')
  } finally {
    loading.value = false
  }
}

function close() {
  emit('update:show', false)
}
</script>
```

## 2. Create ReviewModal.vue

File: `frontend/src/components/modals/ReviewModal.vue`

```vue
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
```

## 3. Update MovingHistoryView.vue

Add this section to display tips and reviews for completed requests:

```vue
<!-- In the template where you show each request -->
<div v-if="request.status === 'completed'" class="mt-4 border-t border-gray-700 pt-4">
  <!-- Tips Section -->
  <div v-if="!request.tips_amount" class="mb-3">
    <button
      @click="openTipsModal(request)"
      class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-colors"
    >
      + Add Tips
    </button>
  </div>
  <div v-else class="mb-3 bg-gray-800 rounded-lg p-3">
    <div class="flex justify-between items-center">
      <div>
        <span class="text-gray-400 text-sm">Tips:</span>
        <span class="text-green-400 text-lg font-bold ml-2">
          ${{ request.tips_amount }}
        </span>
        <span class="text-gray-400 text-sm ml-1">
          ({{ request.tips_percentage }}%)
        </span>
      </div>
      <button
        @click="openTipsModal(request)"
        class="text-blue-400 hover:text-blue-300 text-sm font-medium"
      >
        Edit
      </button>
    </div>
  </div>

  <!-- Review Section -->
  <div v-if="!request.review">
    <button
      @click="openReviewModal(request)"
      class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors"
    >
      Leave a Review
    </button>
  </div>
  <div v-else class="bg-gray-800 rounded-lg p-3">
    <div class="flex items-center">
      <span class="text-gray-400 text-sm mr-2">Your Review:</span>
      <div class="flex">
        <span
          v-for="star in 5"
          :key="star"
          class="text-lg"
          :class="star <= request.review.rating ? 'text-yellow-400' : 'text-gray-600'"
        >
          ★
        </span>
      </div>
    </div>
  </div>
</div>

<!-- Add modals at the end -->
<TipsModal
  v-model:show="showTipsModal"
  :request="selectedRequest"
  @tips-paid="onTipsPaid"
/>

<ReviewModal
  v-model:show="showReviewModal"
  :request="selectedRequest"
  @review-submitted="onReviewSubmitted"
/>
```

Add to script:

```typescript
import TipsModal from '@/components/modals/TipsModal.vue'
import ReviewModal from '@/components/modals/ReviewModal.vue'

const showTipsModal = ref(false)
const showReviewModal = ref(false)
const selectedRequest = ref(null)

function openTipsModal(request: any) {
  selectedRequest.value = request
  showTipsModal.value = true
}

function openReviewModal(request: any) {
  selectedRequest.value = request
  showReviewModal.value = true
}

function onTipsPaid() {
  // Refresh requests
  requestsStore.fetchUserRequests()
  showReviewModal.value = true // Show review modal after tips
}

function onReviewSubmitted() {
  // Refresh requests
  requestsStore.fetchUserRequests()
}
```

## 4. Update DetailsView.vue - Add Financial Summary

Add this section after the RequestForm:

```vue
<!-- Financial Summary Section -->
<n-card v-if="requestsStore.selectedRequest" class="mt-4">
  <template #header>
    <h3 class="text-xl font-bold">Financial Summary</h3>
  </template>

  <div class="space-y-3">
    <!-- Base Price -->
    <div class="flex justify-between items-center py-2 border-b border-gray-700">
      <span class="text-gray-400">Base Price:</span>
      <span class="text-xl font-bold text-white">
        ${{ requestsStore.selectedRequest.price }}
      </span>
    </div>

    <!-- Payment Status -->
    <div class="flex justify-between items-center py-2 border-b border-gray-700">
      <span class="text-gray-400">Payment Status:</span>
      <span
        :class="[
          'px-3 py-1 rounded-full text-sm font-semibold',
          requestsStore.selectedRequest.payment_status === 'paid'
            ? 'bg-green-600 text-white'
            : 'bg-yellow-600 text-white'
        ]"
      >
        {{ requestsStore.selectedRequest.payment_status }}
      </span>
    </div>

    <!-- Tips (if added) -->
    <div
      v-if="requestsStore.selectedRequest.tips_amount"
      class="flex justify-between items-center py-2 border-b border-gray-700"
    >
      <span class="text-gray-400">Tips:</span>
      <span class="text-lg font-semibold text-green-400">
        ${{ requestsStore.selectedRequest.tips_amount }}
        <span class="text-sm text-gray-400">
          ({{ requestsStore.selectedRequest.tips_percentage }}%)
        </span>
      </span>
    </div>

    <!-- Tips Payment Status -->
    <div
      v-if="requestsStore.selectedRequest.tips_amount"
      class="flex justify-between items-center py-2 border-b border-gray-700"
    >
      <span class="text-gray-400">Tips Payment:</span>
      <span
        :class="[
          'px-3 py-1 rounded-full text-sm font-semibold',
          requestsStore.selectedRequest.tips_payment_status === 'paid'
            ? 'bg-green-600 text-white'
            : 'bg-yellow-600 text-white'
        ]"
      >
        {{ requestsStore.selectedRequest.tips_payment_status }}
      </span>
    </div>

    <!-- Total -->
    <div class="flex justify-between items-center py-3 bg-gray-800 rounded-lg px-4 mt-3">
      <span class="text-lg font-semibold text-gray-300">Total:</span>
      <span class="text-2xl font-bold text-green-400">
        ${{
          (parseFloat(requestsStore.selectedRequest.price) +
            parseFloat(requestsStore.selectedRequest.tips_amount || 0)).toFixed(2)
        }}
      </span>
    </div>
  </div>
</n-card>
```

## 5. Update TrackingView.vue - Add Request Selector

Add this at the top of the page (after header):

```vue
<!-- Request Selector -->
<div class="bg-gray-800 rounded-lg p-4 mb-4 mx-4">
  <label class="block text-sm font-medium text-gray-400 mb-2">
    Select Request:
  </label>
  <n-select
    v-model:value="selectedRequestId"
    :options="requestOptions"
    placeholder="Choose a request..."
    @update:value="onRequestChange"
    class="w-full"
  />
</div>
```

Add to script:

```typescript
const selectedRequestId = ref<number | null>(null)

const requestOptions = computed(() => {
  return requestsStore.requests.map(req => ({
    label: `#${req.request_number} - ${req.property_type} (${new Date(req.departure_time).toLocaleDateString()})`,
    value: req.id
  }))
})

async function onRequestChange(id: number) {
  await requestsStore.getRequestById(id)
  router.push({ path: '/tracking', query: { id } })
}

onMounted(async () => {
  // Load all user requests for selector
  await requestsStore.fetchUserRequests()

  // Load specific request from URL
  const requestId = Number(route.query.id)
  if (requestId) {
    selectedRequestId.value = requestId
    await requestsStore.getRequestById(requestId)
  }
})
```
