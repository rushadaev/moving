<template>
  <div class="material-counter bg-[var(--color-background)]">
    <div class="material-name">{{ name }}</div>
    <div class="counter-controls">
      <button 
        class="counter-btn" 
        @click="incrementCount"
        :disabled="disabled"
      >
        +
      </button>
      <span class="counter-value">{{ count }}</span>
      <button 
        class="counter-btn" 
        @click="decrementCount"
        :disabled="disabled || count <= 1"
      >
        -
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
  name: {
    type: String,
    required: true
  },
  initialCount: {
    type: Number,
    default: 1
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:count']);
const count = ref(Math.max(1, props.initialCount));

onMounted(() => {
  if (props.initialCount < 1) {
    emit('update:count', { name: props.name, count: 1 });
  }
});

watch(() => props.initialCount, (newValue) => {
  count.value = Math.max(1, newValue);
});

const incrementCount = () => {
  count.value++;
  emit('update:count', { name: props.name, count: count.value });
};

const decrementCount = () => {
  if (count.value > 1) {
    count.value--;
    emit('update:count', { name: props.name, count: count.value });
  }
};
</script>

<style scoped>
.material-counter {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  margin-bottom: 8px;
}

.material-name {
  font-size: 14px;
  color: var(--color-text);
}

.counter-controls {
  display: flex;
  align-items: center;
  gap: 10px;
}

.counter-btn {
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f0f0f0;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 18px;
  font-weight: bold;
  color: #555;
}

.counter-btn:hover:not(:disabled) {
  background: #e0e0e0;
}

.counter-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.counter-value {
  min-width: 24px;
  text-align: center;
  font-size: 14px;
  font-weight: 500;
  color: var(--color-text);
}
</style> 