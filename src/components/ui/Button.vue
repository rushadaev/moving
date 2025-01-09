<template>
  <n-button
      :circle="circle"
      :quaternary="quaternary"
      :type="type"
      :size="size"
      :disabled="disabled">
    <slot></slot>
  </n-button>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';
import { NButton } from 'naive-ui';
import type { PropType } from 'vue';
import type { Size } from 'naive-ui'; // Import the Size type from Naive UI


defineProps({
  circle: {
    type: Boolean,
    default: false
  },
  quaternary: {
    type: Boolean,
    default: false
  },
  type: {
    type: String as PropType<'default' | 'primary' | 'info' | 'success' | 'warning' | 'error'>,
    default: 'default'
  },
  size: {
    type: String as PropType<Size>, // Use PropType<Size> for stricter typing
    default: 'medium'
  },
  disabled: {
    type: Boolean,
    default: false
  }
});
</script>

<style scoped>
/* Additional styles can be added here if needed */
</style>
