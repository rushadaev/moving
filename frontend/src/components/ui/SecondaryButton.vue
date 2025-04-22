<template>
  <button 
    :class="[
      'secondary-button',
      {'disabled': disabled},
      {'full-width': fullWidth}
    ]"
    :disabled="disabled"
    @click="$emit('click', $event)"
  >
    <slot></slot>
  </button>
</template>

<script setup lang="ts">
defineProps({
  disabled: {
    type: Boolean,
    default: false
  },
  fullWidth: {
    type: Boolean,
    default: false
  }
});

defineEmits(['click']);
</script>

<style scoped>
.secondary-button {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 12px 16px;
  gap: 10px;
  height: 44px;
  cursor: pointer;
  border: 1px solid var(--color-text);
  background-color: var(--color-background);
  border-radius: 10px;
  font-weight: 500;
  font-size: 14px;
  color: var(--color-text);
  transition: all 0.2s ease;
}

.secondary-button.full-width {
  width: 100%;
}

.secondary-button:hover:not(.disabled) {
  background: rgba(0, 74, 255, 0.05);
}

.secondary-button:active:not(.disabled) {
  background: rgba(0, 74, 255, 0.1);
}

.secondary-button.disabled {
  opacity: 0.6;
  cursor: not-allowed;
  border-color: #ccc;
  color: #888;
}
</style> 