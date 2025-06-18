<template>
  <div class="info-window">
    <div class="info-window-header">
      <h3 class="info-window-title">{{ title }}</h3>
      <button v-if="closable" @click="$emit('close')" class="close-button">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="currentColor"/>
        </svg>
      </button>
    </div>
    <div class="info-window-content">
      <slot></slot>
    </div>
    <div v-if="$slots.actions" class="info-window-actions">
      <slot name="actions"></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps({
  title: {
    type: String,
    default: '',
  },
  closable: {
    type: Boolean,
    default: true,
  }
});

defineEmits(['close']);
</script>

<style scoped>
.info-window {
  background: var(--color-background);
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
  width: 100%;
  max-width: 300px;
  overflow: hidden;
  font-size: 14px;
}

.info-window-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid var(--color-border, #eee);
}

.info-window-title {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: var(--color-text);
}

.close-button {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--color-text-secondary, #666);
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.close-button:hover {
  background-color: var(--color-background-mute);
}

.info-window-content {
  padding: 16px;
  color: var(--color-text);
}

.info-window-actions {
  display: flex;
  justify-content: flex-end;
  padding: 8px 16px 16px;
  gap: 8px;
}
</style> 