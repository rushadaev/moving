<template>
  <button 
    :class="[
      'gradient-button',
      {'disabled': disabled},
      {'full-width': fullWidth},
      {'small-button': smallButton},
      {'secondary': type === 'secondary'}
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
  },
  smallButton: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'primary'
  }
});

defineEmits(['click']);
</script>

<style scoped>
.gradient-button {
  display: inline-flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  cursor: pointer;
  border: none;
  background: linear-gradient(90deg, #004AFF 0%, #5D87EE 50%, #FF5400 100%);
  box-shadow: 0px 4px 4px rgba(0, 55, 189, 0.25);
  border-radius: 8px;
  font-weight: 500;
  font-size: 14px;
  color: white;
  transition: all 0.2s ease;
  min-height: 36px;
}

.gradient-button.secondary {
  background: rgba(255, 255, 255, 0.9);
  color: var(--color-text);
  border: 1px solid var(--color-border);
}

.gradient-button.small-button {
  padding: 8px 16px;
  font-size: 12px;
  min-height: 32px;
}

.gradient-button.full-width {
  width: 100%;
}

.gradient-button.small-button.full-width {
  width: 100%;
}

.gradient-button:hover:not(.disabled) {
  transform: translateY(-1px);
  box-shadow: 0px 6px 6px rgba(0, 55, 189, 0.3);
}

.gradient-button:active:not(.disabled) {
  transform: translateY(0);
  box-shadow: 0px 2px 4px rgba(0, 55, 189, 0.2);
}

.gradient-button.disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style> 