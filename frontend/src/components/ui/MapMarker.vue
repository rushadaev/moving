<template>
  <div class="marker-container">
    <slot name="icon">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z" fill="currentColor"/>
      </svg>
    </slot>
    <div v-if="label" class="marker-label">{{ label }}</div>
  </div>
</template>

<script setup lang="ts">
defineProps({
  label: {
    type: String,
    default: ''
  },
  color: {
    type: String,
    default: '#5D87EE'
  },
});
</script>

<style scoped>
.marker-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  color: v-bind('color');
}

.marker-label {
  margin-top: 4px;
  background-color: white;
  color: #333;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  white-space: nowrap;
}
</style> 