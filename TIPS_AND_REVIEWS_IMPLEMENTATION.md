# Tips and Reviews Implementation Guide

## ✅ Backend Completed

### Database Structure

#### Requests Table (New Fields)
- `tips_amount` - Сумма чаевых
- `tips_percentage` - Процент чаевых
- `tips_distribution` - JSON распределение по муверам
- `tips_payment_status` - Статус оплаты чаевых (pending/paid)
- `tips_stripe_session_id` - Stripe session ID для чаевых
- `completed_at` - Дата завершения мувинга

#### Reviews Table (New)
- `request_id` - ID запроса
- `user_id` - ID клиента
- `rating` - Общий рейтинг (1-5)
- `review_text` - Текст отзыва
- `mover_ratings` - JSON рейтинги каждого мувера
- `is_published` - Опубликован ли отзыв

### API Endpoints Created

#### Tips API (`/api/v1/tips/`)

```php
POST /api/v1/tips/calculate
Body: {
  "request_id": 1,
  "amount": 50 // или "percentage": 15
}
Response: {
  "tips_amount": 50,
  "tips_percentage": 15.5,
  "movers_count": 2,
  "per_mover": 25,
  "distribution": [
    {"mover_number": 1, "amount": 25},
    {"mover_number": 2, "amount": 25}
  ]
}

POST /api/v1/tips/store
Body: {
  "request_id": 1,
  "tips_amount": 50,
  "tips_percentage": 15,
  "tips_distribution": [...]
}

POST /api/v1/tips/create-payment
Body: {"request_id": 1}
Response: {
  "sessionId": "...",
  "redirectUrl": "https://checkout.stripe.com/..."
}

POST /api/v1/tips/confirm-payment
Body: {
  "session_id": "...",
  "request_id": 1
}
```

#### Reviews API (`/api/v1/reviews/`)

```php
POST /api/v1/reviews
Body: {
  "request_id": 1,
  "rating": 5,
  "review_text": "Great service!",
  "mover_ratings": [
    {"mover_number": 1, "rating": 5},
    {"mover_number": 2, "rating": 4}
  ]
}

GET /api/v1/reviews/{requestId}
Response: {
  "id": 1,
  "request_id": 1,
  "rating": 5,
  "review_text": "...",
  ...
}
```

---

## 🎨 Frontend Components to Create

### 1. TipsModal Component (`frontend/src/components/modals/TipsModal.vue`)

**Требования:**
- ✅ Отображение общей суммы мувинга
- ✅ Кнопки с готовыми процентами (10%, 15%, 20%, 25%)
- ✅ Custom опция с двусторонней конвертацией:
  - Ввод суммы → показать %
  - Ввод % → показать сумму
- ✅ Изначально 0% и $0
- ✅ Показать распределение по муверам
- ✅ Кнопка "Pay Tips" → Stripe Checkout

**Пример структуры:**
```vue
<template>
  <Modal v-model:show="showTipsModal" title="Add Tips">
    <!-- Total Moving Cost -->
    <div class="total-cost">
      <span>Moving Cost:</span>
      <span>${{ request.price }}</span>
    </div>

    <!-- Preset Buttons -->
    <div class="tips-presets">
      <button
        v-for="preset in [10, 15, 20, 25]"
        :key="preset"
        @click="selectPreset(preset)"
        :class="{ active: selectedPreset === preset }"
      >
        {{ preset }}%
      </button>
      <button
        @click="selectCustom"
        :class="{ active: isCustom }"
      >
        Custom
      </button>
    </div>

    <!-- Custom Input (if selected) -->
    <div v-if="isCustom" class="custom-inputs">
      <div class="input-group">
        <label>Amount:</label>
        <input
          v-model="customAmount"
          @input="onAmountChange"
          type="number"
          placeholder="0"
        />
        <span class="suffix">$</span>
      </div>

      <div class="input-group">
        <label>Percentage:</label>
        <input
          v-model="customPercentage"
          @input="onPercentageChange"
          type="number"
          placeholder="0"
        />
        <span class="suffix">%</span>
      </div>
    </div>

    <!-- Tips Summary -->
    <div class="tips-summary">
      <div class="summary-row">
        <span>Tips Amount:</span>
        <span class="amount">${{ tipsAmount.toFixed(2) }}</span>
      </div>
      <div class="summary-row">
        <span>Percentage:</span>
        <span class="percentage">{{ tipsPercentage.toFixed(1) }}%</span>
      </div>
    </div>

    <!-- Movers Distribution -->
    <div class="movers-distribution">
      <h4>Distribution per Mover:</h4>
      <div
        v-for="(mover, index) in distribution"
        :key="index"
        class="mover-row"
      >
        <div class="mover-icon">👷</div>
        <span>Mover {{ index + 1 }}</span>
        <span class="mover-amount">${{ mover.amount.toFixed(2) }}</span>
      </div>
    </div>

    <!-- Footer -->
    <template #footer>
      <button @click="close">Cancel</button>
      <button
        @click="payTips"
        :disabled="tipsAmount <= 0"
        class="pay-btn"
      >
        Pay Tips with Stripe
      </button>
    </template>
  </Modal>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { loadStripe } from '@stripe/stripe-js'

const props = defineProps<{
  show: boolean
  request: any
}>()

const emit = defineEmits(['update:show', 'tips-paid'])

const selectedPreset = ref<number | null>(null)
const isCustom = ref(false)
const customAmount = ref(0)
const customPercentage = ref(0)

const tipsAmount = computed(() => {
  if (isCustom.value) {
    return customAmount.value
  } else if (selectedPreset.value) {
    return (props.request.price * selectedPreset.value) / 100
  }
  return 0
})

const tipsPercentage = computed(() => {
  if (isCustom.value) {
    return customPercentage.value
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
}

function onAmountChange() {
  // Recalculate percentage
  if (props.request.price > 0) {
    customPercentage.value = (customAmount.value / props.request.price) * 100
  }
}

function onPercentageChange() {
  // Recalculate amount
  customAmount.value = (props.request.price * customPercentage.value) / 100
}

async function payTips() {
  // 1. Save tips to backend
  await apiService.post('/tips/store', {
    request_id: props.request.id,
    tips_amount: tipsAmount.value,
    tips_percentage: tipsPercentage.value,
    tips_distribution: distribution.value
  })

  // 2. Create Stripe session
  const response = await apiService.post('/tips/create-payment', {
    request_id: props.request.id
  })

  // 3. Redirect to Stripe
  window.location.href = response.data.redirectUrl
}

function close() {
  emit('update:show', false)
}
</script>
```

### 2. ReviewModal Component (`frontend/src/components/modals/ReviewModal.vue`)

**Требования:**
- ✅ Отображается после успешной оплаты чаевых
- ✅ Общий рейтинг компании (1-5 звезд)
- ✅ Текст отзыва
- ✅ Рейтинг каждого мувера (опционально)
- ✅ Кнопка Submit

**Пример структуры:**
```vue
<template>
  <Modal v-model:show="showReviewModal" title="Leave a Review">
    <div class="review-form">
      <!-- Company Rating -->
      <div class="rating-section">
        <h4>Rate Our Service:</h4>
        <div class="stars">
          <button
            v-for="star in 5"
            :key="star"
            @click="rating = star"
            :class="{ filled: star <= rating }"
          >
            ★
          </button>
        </div>
      </div>

      <!-- Review Text -->
      <div class="review-text-section">
        <label>Tell us about your experience:</label>
        <textarea
          v-model="reviewText"
          placeholder="Share your thoughts..."
          rows="4"
        ></textarea>
      </div>

      <!-- Mover Ratings (Optional) -->
      <div class="mover-ratings">
        <h4>Rate Each Mover (Optional):</h4>
        <div
          v-for="(mover, index) in movers"
          :key="index"
          class="mover-rating-row"
        >
          <span>Mover {{ index + 1 }}:</span>
          <div class="stars">
            <button
              v-for="star in 5"
              :key="star"
              @click="mover.rating = star"
              :class="{ filled: star <= mover.rating }"
            >
              ★
            </button>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <button @click="skip">Skip</button>
      <button
        @click="submitReview"
        :disabled="rating === 0"
        class="submit-btn"
      >
        Submit Review
      </button>
    </template>
  </Modal>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  request: any
}>()

const emit = defineEmits(['update:show', 'review-submitted'])

const rating = ref(0)
const reviewText = ref('')
const movers = ref(
  Array(props.request.movers_count || 2).fill(0).map((_, i) => ({
    mover_number: i + 1,
    rating: 0
  }))
)

async function submitReview() {
  await apiService.post('/reviews', {
    request_id: props.request.id,
    rating: rating.value,
    review_text: reviewText.value,
    mover_ratings: movers.value.filter(m => m.rating > 0)
  })

  emit('review-submitted')
  emit('update:show', false)
}

function skip() {
  emit('update:show', false)
}
</script>
```

### 3. Request History Component (Update existing)

**В `RequestsView.vue` или `ProfileView.vue`:**

```vue
<!-- После завершенного мувинга -->
<div v-if="request.status === 'completed'" class="completed-actions">
  <!-- Tips Section -->
  <div v-if="!request.tips_amount" class="tips-section">
    <button
      @click="openTipsModal(request)"
      class="add-tips-btn"
    >
      Add Tips
    </button>
  </div>
  <div v-else class="tips-display">
    <span class="tips-label">Tips:</span>
    <span class="tips-amount">${{ request.tips_amount }}</span>
    <span class="tips-percentage">({{ request.tips_percentage }}%)</span>
    <button @click="openTipsModal(request)" class="edit-btn">
      Edit
    </button>
  </div>

  <!-- Review Section -->
  <div v-if="!request.review" class="review-prompt">
    <button @click="openReviewModal(request)">
      Leave a Review
    </button>
  </div>
  <div v-else class="review-display">
    <span>Review: ★ {{ request.review.rating }}/5</span>
  </div>
</div>
```

---

## 🔄 Payment Flow

### После оплаты чаевых:

1. **User clicks "Pay Tips"** → Redirect to Stripe Checkout
2. **After payment success** → Stripe redirects to `/payment/return?session_id=xxx`
3. **Frontend confirms payment** → Call `/api/v1/tips/confirm-payment`
4. **Show ReviewModal** → User can leave review
5. **After review submitted** → Show success message and redirect to history

### Success/Return Page (`/payment/return`)

```vue
<template>
  <div class="payment-return">
    <div v-if="loading">
      Processing payment...
    </div>
    <div v-else-if="success">
      <h2>✅ Payment Successful!</h2>
      <p>Thank you for your tip!</p>

      <!-- Show Review Modal -->
      <ReviewModal
        v-model:show="showReviewModal"
        :request="request"
        @review-submitted="onReviewSubmitted"
      />
    </div>
  </div>
</template>

<script setup>
const route = useRoute()
const sessionId = route.query.session_id

onMounted(async () => {
  // Confirm payment
  await apiService.post('/tips/confirm-payment', {
    session_id: sessionId,
    request_id: route.query.request_id
  })

  success.value = true
  showReviewModal.value = true
})
</script>
```

---

## 📝 Next Steps

1. ✅ Добавить routes в `backend/routes/api.php`
2. ✅ Создать ReviewController в backend
3. ✅ Создать TipsModal component
4. ✅ Создать ReviewModal component
5. ✅ Обновить RequestsView для отображения tips и reviews
6. ✅ Создать payment return page
7. ✅ Протестировать полный flow

## 🎯 Testing Checklist

- [ ] Завершить мувинг (установить status='completed')
- [ ] Открыть модалку Add Tips
- [ ] Выбрать preset percentage (10%, 15%, etc.)
- [ ] Проверить Custom input (сумма ⟷ проценты)
- [ ] Проверить распределение по муверам
- [ ] Оплатить через Stripe
- [ ] Проверить редирект на success page
- [ ] Оставить отзыв
- [ ] Проверить отображение в History
