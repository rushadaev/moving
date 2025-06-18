<template>
  <div class="filter-bar">
    <div class="search-container">
      <TextField
        v-model="searchQuery"
        placeholder="Search requests..."
        @input="handleSearch"
        clearable
      />
    </div>
    
    <div class="filters-container">
      <div class="filter-group">
        <Checkbox 
          v-for="(option, index) in filterOptions" 
          :key="index"
          v-model="option.selected"
          :label="option.label"
          @change="applyFilters"
        />
      </div>
      
      <div class="sort-control">
        <label class="sort-label">Sort by:</label>
        <select 
          v-model="sortBy" 
          class="sort-select"
          @change="applyFilters"
        >
          <option value="date-new">Newest first</option>
          <option value="date-old">Oldest first</option>
          <option value="price-high">Price (high to low)</option>
          <option value="price-low">Price (low to high)</option>
        </select>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import TextField from './ui/TextField.vue';
import Checkbox from './ui/Checkbox.vue';

interface FilterOption {
  label: string;
  value: string;
  selected: boolean;
}

const props = defineProps({
  initialFilters: {
    type: Array as () => FilterOption[],
    default: () => []
  }
});

const emit = defineEmits(['filter', 'search', 'sort']);

// State
const searchQuery = ref('');
const sortBy = ref('date-new');
const filterOptions = ref<FilterOption[]>(
  props.initialFilters.length > 0 ? 
  props.initialFilters : 
  [
    { label: '1 Bedroom', value: '1-bedroom', selected: false },
    { label: '2 Bedroom', value: '2-bedroom', selected: false },
    { label: '3+ Bedroom', value: '3-bedroom', selected: false },
    { label: 'Urgent', value: 'urgent', selected: false }
  ]
);

// Methods
const handleSearch = () => {
  emit('search', searchQuery.value);
};

const applyFilters = () => {
  emit('filter', filterOptions.value.filter(option => option.selected).map(option => option.value));
  emit('sort', sortBy.value);
};

// Watch for external filter changes
watch(() => props.initialFilters, (newFilters) => {
  if (newFilters.length > 0) {
    filterOptions.value = newFilters;
  }
}, { deep: true });
</script>

<style scoped>
.filter-bar {
  margin-bottom: 1.5rem;
  padding: 1rem;
  background-color: var(--color-background-soft);
  border-radius: 0.5rem;
}

.search-container {
  margin-bottom: 1rem;
}

.filters-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.filter-group {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.sort-control {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.sort-label {
  font-size: 0.875rem;
  color: var(--color-text);
  opacity: 0.7;
}

.sort-select {
  padding: 0.375rem 0.75rem;
  border-radius: 0.25rem;
  border: 1px solid var(--color-border);
  background-color: var(--color-background);
  color: var(--color-text);
  font-size: 0.875rem;
}

@media (max-width: 768px) {
  .filters-container {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .sort-control {
    margin-top: 0.5rem;
    width: 100%;
  }
  
  .sort-select {
    width: 100%;
  }
}
</style> 