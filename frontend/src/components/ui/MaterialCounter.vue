<template>
  <div class="material-counter">
    <div class="material-name">{{ name }}</div>
    <div class="counter-controls">
      <button 
        class="counter-btn" 
        @click="decrementCount"
        :disabled="disabled || count <= 1"
      >
        -
      </button>
      <span class="counter-value">{{ count }}</span>
      <button 
        class="counter-btn" 
        @click="incrementCount"
        :disabled="disabled"
      >
        +
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
  padding: 12px 16px;
  background: var(--color-background-soft);
  border-radius: 8px;
  border: 1px solid var(--color-border);
}

.material-name {
  font-size: 16px;
  font-weight: 500;
  color: var(--color-text);
  flex: 1;
  margin-right: 16px;
}

.counter-controls {
  display: flex;
  align-items: center;
  gap: 16px;
}

.counter-btn {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-background-mute);
  border: 1px solid var(--color-border);
  border-radius: 6px;
  cursor: pointer;
  font-size: 20px;
  font-weight: 500;
  color: var(--color-text);
  transition: all 0.2s ease;
}

.counter-btn:hover:not(:disabled) {
  background: var(--color-primary);
  color: white;
  border-color: var(--color-primary);
  transform: scale(1.05);
}

.counter-btn:active:not(:disabled) {
  transform: scale(0.95);
}

.counter-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.counter-value {
  min-width: 32px;
  text-align: center;
  font-size: 18px;
  font-weight: 600;
  color: var(--color-text);
}
</style> 