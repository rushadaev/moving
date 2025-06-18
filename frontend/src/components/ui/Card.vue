<template>
  <div class="card" :class="{ 'with-header': withHeader }">
    <div v-if="withHeader" class="card-header">
      <div class="title">{{ title }}</div>
      <div v-if="price" class="price-container">
        <span class="price-label">Price</span>
        <span class="price-value">{{ price }}</span>
      </div>
    </div>
    <div class="card-content">
      <slot></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps({
  withHeader: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  price: {
    type: [String, Number],
    default: ''
  }
});
</script>

<style scoped>
.card {
  width: 100%;
  background: var(--color-background-mute);
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1rem;
}

.with-header {
  padding-top: 0;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem;
  background: var(--color-background);
  border-radius: 0.5rem;
  margin-bottom: 1rem;
}

.title {
  font-weight: 500;
  color: var(--color-text);
}

.price-container {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--color-background-mute);
  border-radius: 0.25rem;
  padding: 0.25rem 0.75rem;
}

.price-label {
  opacity: 0.6;
  font-size: 0.875rem;
  font-weight: 600;
}

.price-value {
  font-size: 1rem;
}

.card-content {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
</style> 