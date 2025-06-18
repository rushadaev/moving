<template>
  <div class="moving-history-view">
    <Header title="Moving history" />
    
    <main class="history-content">
      <div class="history-list">
        <div v-for="event in historyEvents" :key="event.id" class="history-item">
          <div class="event-info">
            <span class="event-type">{{ event.type }}</span>
            <span class="event-description" v-if="event.description">{{ event.description }}</span>
          </div>
          <span class="event-time">{{ formatTime(event.timestamp) }}</span>
        </div>
      </div>
    </main>
    
    <Footer />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useRequestsStore } from '@/stores/requests';
import { useAuthStore } from '@/stores/auth';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';

const router = useRouter();
const requestsStore = useRequestsStore();
const authStore = useAuthStore();

interface HistoryEvent {
  id: number;
  type: string;
  description?: string;
  timestamp: Date;
}

const historyEvents = ref<HistoryEvent[]>([
  {
    id: 1,
    type: 'Departure',
    timestamp: new Date('2024-01-15T12:00:00')
  },
  {
    id: 2,
    type: 'Break',
    timestamp: new Date('2024-01-15T13:00:00')
  },
  {
    id: 3,
    type: 'New unloading point added',
    description: 'Address of new point here',
    timestamp: new Date('2024-01-15T14:00:00')
  },
  {
    id: 4,
    type: 'End of unloading',
    timestamp: new Date('2024-01-15T15:00:00')
  }
]);

const formatTime = (date: Date) => {
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push('/');
    return;
  }
  
  // Load history events for the selected request
  if (requestsStore.selectedRequest) {
    // TODO: Load actual history from API
    console.log('Loading history for request:', requestsStore.selectedRequest.id);
  }
});
</script>

<style scoped>
.moving-history-view {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: var(--color-background);
}

.history-content {
  flex: 1;
  padding: 16px;
  padding-top: 71px; /* Header height + padding */
  padding-bottom: 86px; /* Footer height + padding */
}

.history-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.history-item {
  background: var(--color-background-mute);
  border-radius: 12px;
  padding: 16px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.event-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.event-type {
  font-size: 16px;
  font-weight: 500;
  color: var(--color-text);
}

.event-description {
  font-size: 14px;
  color: var(--color-text-secondary);
}

.event-time {
  font-size: 14px;
  color: var(--color-text-secondary);
  white-space: nowrap;
}

/* Dark mode adjustments */
@media (prefers-color-scheme: dark) {
  .history-item {
    background: var(--color-background-soft);
  }
}
</style>