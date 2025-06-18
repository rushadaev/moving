<template>
  <div class="request-actions-bar">
    <div class="action-buttons-container">
      <slot>
        <GradientButton 
          small-button="true"
          v-if="showAddPoint" 
          @click="$emit('addPoint')" 
          class="action-button"
        >
          Add Unloading Point
        </GradientButton>
        
        <Button 
          v-if="showContact" 
          type="primary" 
          @click="$emit('contact')" 
          class="action-button"
        >
          Contact Customer
        </Button>
        
        <Button 
          v-if="showTrack" 
          @click="$emit('track')" 
          class="action-button"
        >
          Track Order
        </Button>
      </slot>
    </div>
  </div>
</template>

<script setup lang="ts">
import Button from './Button.vue';
import GradientButton from './GradientButton.vue';

defineProps({
  showAddPoint: {
    type: Boolean,
    default: true
  },
  showContact: {
    type: Boolean,
    default: true
  },
  showTrack: {
    type: Boolean,
    default: false
  }
});

defineEmits(['addPoint', 'contact', 'track']);
</script>

<style scoped>
.request-actions-bar {
  margin-top: 1rem;
  padding: 1rem 0;
}

.action-buttons-container {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.action-button {
  flex: 1;
  min-width: 120px;
}

@media (max-width: 640px) {
  .action-buttons-container {
    flex-direction: column;
  }
  
  .action-button {
    width: 100%;
  }
}
</style> 