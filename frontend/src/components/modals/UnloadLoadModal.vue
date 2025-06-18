<template>
  <Modal v-model:show="isModalOpen" title="Unload/Load">
    <div class="unload-load-form">
      <div class="status-message">
        <p>Select an operation to perform:</p>
      </div>
      
      <div class="operation-buttons">
        <GradientButton small-button="true" @click="onUnload" class="mb-3">
          Unload at current location
        </GradientButton>
        
        <GradientButton small-button="true" @click="onLoad">
          Load from current location
        </GradientButton>
      </div>
      
      <div class="cancel-button mt-4">
        <GradientButton type="secondary" small-button="true" @click="onCancel" :full-width="true">Close</GradientButton>
      </div>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import Modal from '@/components/ui/Modal.vue';
import GradientButton from '@/components/ui/GradientButton.vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'unload', 'load', 'cancel']);

const isModalOpen = ref(props.modelValue);

watch(() => props.modelValue, (newValue) => {
  isModalOpen.value = newValue;
});

watch(isModalOpen, (newValue) => {
  emit('update:modelValue', newValue);
});

const onUnload = () => {
  emit('unload');
  isModalOpen.value = false;
};

const onLoad = () => {
  emit('load');
  isModalOpen.value = false;
};

const onCancel = () => {
  emit('cancel');
  isModalOpen.value = false;
};
</script>

<style scoped>
.unload-load-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.status-message {
  text-align: center;
  margin-bottom: 8px;
  color: var(--color-text);
}

.operation-buttons {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.cancel-button {
  margin-top: 8px;
}
</style> 