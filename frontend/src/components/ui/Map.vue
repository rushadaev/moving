<template>
  <div class="map-container" :style="{ height: height + 'px' }">
    <div class="map-placeholder">
      <div class="map-placeholder-content">
        <slot name="placeholder-content">
          <p>Map goes here</p>
          <p class="map-note">This is a placeholder for an actual map integration</p>
        </slot>
      </div>
    </div>
    <slot></slot>
  </div>
</template>

<script setup lang="ts">
defineProps({
  height: {
    type: Number,
    default: 300
  }
});
</script>

<style scoped>
.map-container {
  width: 100%;
  border-radius: 8px;
  overflow: hidden;
  position: relative;
  background: #E5E3E8;
}

.map-placeholder {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: #555;
  background-image: 
    linear-gradient(45deg, #E5E3E8 25%, transparent 25%), 
    linear-gradient(-45deg, #E5E3E8 25%, transparent 25%), 
    linear-gradient(45deg, transparent 75%, #E5E3E8 75%), 
    linear-gradient(-45deg, transparent 75%, #E5E3E8 75%);
  background-size: 20px 20px;
  background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
  background-color: #D8D6DD;
}

.map-placeholder-content {
  background: rgba(255, 255, 255, 0.8);
  padding: 20px;
  border-radius: 8px;
}

.map-note {
  font-size: 12px;
  opacity: 0.6;
  margin-top: 4px;
}
</style> 