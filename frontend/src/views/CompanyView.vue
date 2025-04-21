<template>
  <div class="company-view">
    <Header title="Company Dashboard" />
    
    <main class="p-5 w-full pb-24">
      <!-- Request Summary Cards -->
      <section class="request-summary mb-6">
        <h2 class="section-title mb-4">Current Requests</h2>
        
        <!-- Filters -->
        <RequestFilterBar 
          @search="handleSearch"
          @filter="handleFilter"
          @sort="handleSort"
        />
        
        <div class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
          <div 
            v-for="request in filteredRequests" 
            :key="request.id"
            class="cursor-pointer hover:shadow-md transition-shadow"
            @click="openRequestDetails(request)"
          >
            <Card :with-header="true" :title="'Request #' + request.id" :price="request.price + '$'">
              <div class="details-grid">
                <div class="detail-group">
                  <span class="detail-label">Type</span>
                  <span class="detail-value">{{ request.type }}</span>
                </div>
                <div class="detail-group">
                  <span class="detail-label">Time</span>
                  <span class="detail-value">{{ request.time }}</span>
                </div>
                
                <!-- Status Badge -->
                <div class="detail-group status-wrapper" v-if="request.status">
                  <span class="detail-label">Status</span>
                  <StatusBadge :status="request.status" />
                </div>
              </div>
            </Card>
          </div>
        </div>
      </section>
      
      <!-- Map Section -->
      <section class="map-section mb-6">
        <h2 class="section-title mb-4">Request Locations</h2>
        <Map :height="300" />
      </section>
      
      <!-- Selected Request Details -->
      <section v-if="selectedRequest" class="request-details-section mb-6">
        <h2 class="section-title mb-4">
          Request Details 
          <StatusBadge 
            v-if="selectedRequest.status" 
            :status="selectedRequest.status" 
            class="ml-2"
          />
        </h2>
        <RequestDetails :request="selectedRequest" />
        
        <RequestActionsBar
          @add-point="showAddPointModal = true"
          @contact="contactCustomer"
        />
      </section>
    </main>
    
    <!-- Add Unloading Point Modal -->
    <AddUnloadingPoint 
      v-model="showAddPointModal"
      @add="handleAddUnloadingPoint"
      @cancel="showAddPointModal = false"
    />
    
    <!-- Footer Navigation -->
    <Footer />
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import Map from '@/components/ui/Map.vue';
import Button from '@/components/ui/Button.vue';
import GradientButton from '@/components/ui/GradientButton.vue';
import RequestDetails from '@/components/RequestDetails.vue';
import AddUnloadingPoint from '@/components/AddUnloadingPoint.vue';
import Card from '@/components/ui/Card.vue';
import RequestActionsBar from '@/components/ui/RequestActionsBar.vue';
import RequestFilterBar from '@/components/RequestFilterBar.vue';
import StatusBadge from '@/components/ui/StatusBadge.vue';

// Type definition
interface Request {
  id: number;
  price: number;
  type: string;
  time: string;
  loadingAddress: string;
  unloadingAddress: string;
  status?: string;
}

// Sample data for requests
const requests = ref<Request[]>([
  {
    id: 1,
    price: 12,
    type: "3 bedroom",
    time: "12/11/24 | 12:00",
    loadingAddress: "123 Main St, City A",
    unloadingAddress: "456 Elm St, City B",
    status: "active"
  },
  {
    id: 2,
    price: 15,
    type: "2 bedroom",
    time: "12/12/24 | 14:00",
    loadingAddress: "789 Oak St, City C",
    unloadingAddress: "321 Pine St, City D",
    status: "pending"
  },
  {
    id: 3,
    price: 18,
    type: "1 bedroom",
    time: "12/13/24 | 10:30",
    loadingAddress: "456 Maple St, City E",
    unloadingAddress: "654 Birch St, City F",
    status: "urgent"
  },
  {
    id: 4,
    price: 20,
    type: "4 bedroom",
    time: "12/14/24 | 09:00",
    loadingAddress: "101 Walnut St, City G",
    unloadingAddress: "202 Cedar St, City H",
    status: "completed"
  },
]);

// Filter state
const searchTerm = ref('');
const activeFilters = ref<string[]>([]);
const activeSortBy = ref('date-new');

// Filtered requests
const filteredRequests = computed(() => {
  let result = [...requests.value];
  
  // Apply search filter
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase();
    result = result.filter(request => 
      request.loadingAddress.toLowerCase().includes(term) ||
      request.unloadingAddress.toLowerCase().includes(term) ||
      request.type.toLowerCase().includes(term) ||
      request.status?.toLowerCase().includes(term) ||
      `request #${request.id}`.toLowerCase().includes(term)
    );
  }
  
  // Apply type filters
  if (activeFilters.value.length > 0) {
    result = result.filter(request => {
      // Check if any of the active filters match the request type or status
      return activeFilters.value.some(filter => {
        if (filter === '1-bedroom') return request.type.includes('1 bedroom');
        if (filter === '2-bedroom') return request.type.includes('2 bedroom');
        if (filter === '3-bedroom') return 
          request.type.includes('3 bedroom') || 
          request.type.includes('4 bedroom') ||
          parseInt(request.type) >= 3;
        if (filter === 'urgent') return request.status === 'urgent';
        return false;
      });
    });
  }
  
  // Apply sorting
  result.sort((a, b) => {
    switch (activeSortBy.value) {
      case 'date-new':
        return new Date(b.time).getTime() - new Date(a.time).getTime();
      case 'date-old':
        return new Date(a.time).getTime() - new Date(b.time).getTime();
      case 'price-high':
        return Number(b.price) - Number(a.price);
      case 'price-low':
        return Number(a.price) - Number(b.price);
      default:
        return 0;
    }
  });
  
  return result;
});

// State management
const selectedRequest = ref<Request | null>(null);
const showAddPointModal = ref(false);

// Methods
const openRequestDetails = (request: Request) => {
  selectedRequest.value = request;
};

const contactCustomer = () => {
  // Implementation for contacting customer
  if (selectedRequest.value) {
    console.log('Contact customer for request:', selectedRequest.value.id);
  }
};

const handleAddUnloadingPoint = (data: { address: string }) => {
  if (selectedRequest.value) {
    console.log('Adding unloading point to request:', selectedRequest.value.id, data);
    // Here you would update the unloading address or add it to a list
  }
};

// Filter methods
const handleSearch = (query: string) => {
  searchTerm.value = query;
};

const handleFilter = (filters: string[]) => {
  activeFilters.value = filters;
};

const handleSort = (sortBy: string) => {
  activeSortBy.value = sortBy;
};
</script>

<style scoped>
.company-view {
  min-height: 100vh;
  padding-top: 55px; /* Height of the header */
}

.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--color-text);
  display: flex;
  align-items: center;
}

.details-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.status-wrapper {
  grid-column: span 2;
  margin-top: 4px;
}

.detail-group {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.detail-label {
  font-size: 12px;
  color: var(--color-text);
  opacity: 0.6;
  font-weight: 600;
}

.detail-value {
  font-size: 14px;
  color: var(--color-text);
}
</style> 