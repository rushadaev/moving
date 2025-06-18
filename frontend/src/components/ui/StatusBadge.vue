<template>
  <div 
    class="status-badge"
    :class="[
      status.toLowerCase(),
      { 'small': size === 'small' }
    ]"
  >
    <span class="badge-text">{{ label || status }}</span>
  </div>
</template>

<script setup lang="ts">
defineProps({
  status: {
    type: String,
    required: true,
    validator: (value: string) => 
      ['active', 'completed', 'pending', 'cancelled', 'urgent'].includes(value.toLowerCase())
  },
  label: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'default',
    validator: (value: string) => ['small', 'default'].includes(value)
  }
});
</script>

<style scoped>
.status-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.25rem 0.5rem;
  border-radius: 1rem;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: capitalize;
}

.status-badge.small {
  padding: 0.125rem 0.375rem;
  font-size: 0.625rem;
}

.active {
  background-color: rgba(93, 135, 238, 0.15);
  color: #5D87EE;
}

.completed {
  background-color: rgba(76, 175, 80, 0.15);
  color: #4CAF50;
}

.pending {
  background-color: rgba(255, 193, 7, 0.15);
  color: #FFC107;
}

.cancelled {
  background-color: rgba(244, 67, 54, 0.15);
  color: #F44336;
}

.urgent {
  background-color: rgba(233, 30, 99, 0.15);
  color: #E91E63;
}
</style> 