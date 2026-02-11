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
