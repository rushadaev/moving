<template>
  <Modal v-model:show="isModalOpen" :title="title">
    <div class="approval-content">
      <p class="approval-message">{{ message }}</p>
    </div>
    
    <template #footer>
      <div class="modal-footer">
        <GradientButton 
          type="secondary" 
          @click="handleCancel"
        >
          Cancel
        </GradientButton>
        <GradientButton 
          @click="handleConfirm"
        >
          Confirm
        </GradientButton>
      </div>
    </template>
  </Modal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import Modal from '@/components/ui/Modal.vue';
import GradientButton from '@/components/ui/GradientButton.vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Approver action'
  },
  message: {
    type: String,
    default: 'Are you sure that you want to change number of movers?'
  }
});

const emit = defineEmits(['update:show', 'confirm', 'cancel']);

const isModalOpen = ref(props.show);

watch(() => props.show, (newValue) => {
  isModalOpen.value = newValue;
});

watch(isModalOpen, (newValue) => {
  emit('update:show', newValue);
});

const handleCancel = () => {
  emit('update:show', false);
  emit('cancel');
};

const handleConfirm = () => {
  emit('confirm');
  emit('update:show', false);
};
</script>

<style scoped>
.approval-content {
  padding: 20px 0;
  text-align: center;
}

.approval-message {
  font-size: 16px;
  color: var(--color-text);
  line-height: 1.5;
}

.modal-footer {
  display: flex;
  gap: 12px;
  justify-content: center;
}
</style>