<template>
  <button 
    :class="[
      'gradient-button',
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
.gradient-button {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 16px;
  gap: 10px;
  height: 48px;
  cursor: pointer;
  border: none;
  background: linear-gradient(90deg, #004AFF 0%, #5D87EE 50%, #FF5400 100%);
  box-shadow: 0px 4px 4px rgba(0, 55, 189, 0.25);
  border-radius: 10px;
  font-weight: 500;
  font-size: 14px;
  color: white;
  transition: all 0.2s ease;
}

.gradient-button.full-width {
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