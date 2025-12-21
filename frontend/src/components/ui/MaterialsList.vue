<template>
  <div v-if="materials && materials.length > 0" class="materials-list">
    <h3 class="materials-title">Packing Materials</h3>
    <div class="materials-grid">
      <div
        v-for="material in materials"
        :key="material.name"
        class="material-item"
      >
        <div class="material-info">
          <img
            :src="getMaterialIcon(material.name)"
            :alt="material.name"
            class="material-icon"
          />
          <span class="material-name">{{ formatMaterialName(material.name) }}</span>
        </div>
        <div class="material-quantity">
          <span class="quantity-badge">{{ material.quantity }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  materials: Array<{ name: string; quantity: number }>
}>()

const getMaterialIcon = (name: string): string => {
  const iconMap: Record<string, string> = {
    'full_service': '/Icons/Packing.svg',
    'boxes': '/Icons/Box Moowee.svg',
    'bubble_wrap': '/Icons/Bubble Wrap.svg',
    'packing_tape': '/Icons/Plastic tape.svg'
  }
  return iconMap[name] || '/Icons/Packing.svg'
}

const formatMaterialName = (name: string): string => {
  return name
    .split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}
</script>

<style scoped>
.materials-list {
  margin-top: 16px;
}

.materials-title {
  font-size: 16px;
  font-weight: 600;
  color: var(--color-text);
  margin-bottom: 12px;
}

.materials-grid {
  display: grid;
  gap: 12px;
}

.material-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  background: #303134;
  border: 1px solid #334155;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.material-item:hover {
  border-color: #3b82f6;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
}

.material-info {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.material-icon {
  width: 32px;
  height: 32px;
  object-fit: contain;
}

.material-name {
  font-size: 14px;
  font-weight: 500;
  color: #e2e8f0;
}

.material-quantity {
  display: flex;
  align-items: center;
}

.quantity-badge {
  background: #3b82f6;
  color: white;
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 700;
  min-width: 40px;
  text-align: center;
}
</style>
