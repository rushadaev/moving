<template>
  <div class="request-details">
    <Card :with-header="true" :title="'Request #' + request.id" :price="request.price + '$'">
      <div class="details-grid">
        <!-- Type and Time Section -->
        <div class="detail-group">
          <span class="detail-label">Type</span>
          <span class="detail-value">{{ request.type }}</span>
        </div>
        
        <div class="detail-group">
          <span class="detail-label">Time</span>
          <span class="detail-value">{{ request.time }}</span>
        </div>
        
        <!-- Loading Address Section -->
        <div class="detail-group full-width">
          <span class="detail-label">Loading address</span>
          <span class="detail-value">{{ request.loadingAddress }}</span>
        </div>
        
        <!-- Unloading Address Section -->
        <div class="detail-group full-width">
          <span class="detail-label">Unloading address</span>
          <span class="detail-value">{{ request.unloadingAddress }}</span>
        </div>
      </div>
    </Card>
  </div>
</template>

<script setup lang="ts">
import Card from '@/components/ui/Card.vue';

interface RequestDetail {
  id: string | number;
  price: string | number;
  type: string;
  time: string;
  loadingAddress: string;
  unloadingAddress: string;
}

defineProps<{
  request: RequestDetail;
}>();
</script>

<style scoped>
.request-details {
  width: 100%;
}

.details-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.detail-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.detail-group.full-width {
  grid-column: span 2;
}

.detail-label {
  font-size: 14px;
  color: var(--color-text);
  opacity: 0.6;
  font-weight: 600;
}

.detail-value {
  font-size: 16px;
  color: var(--color-text);
}
</style> 