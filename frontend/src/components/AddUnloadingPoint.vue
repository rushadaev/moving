<template>
  <Modal v-model="isModalOpen" :title="title">
    <div class="unloading-point-form">
      <div class="form-group">
        <label>{{ addressLabel }}</label>
        <TextField v-model="address" placeholder="Address" />
      </div>
      
      <div v-if="showConfirmation" class="confirmation-message">
        {{ confirmationMessage }}
      </div>
      
      <div class="form-actions">
        <slot name="footer">
          <div class="button-group">
            <Button @click="onCancel">Cancel</Button>
            <GradientButton small-button="true" @click="onAdd">Add</GradientButton>
          </div>
        </slot>
      </div>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { ref, defineEmits, defineProps } from 'vue';
import Modal from '@/components/ui/Modal.vue';
import TextField from '@/components/ui/TextField.vue';
import Button from '@/components/ui/Button.vue';
import GradientButton from '@/components/ui/GradientButton.vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Add unloading point'
  },
  addressLabel: {
    type: String,
    default: 'Address'
  },
  confirmationMessage: {
    type: String,
    default: 'Are you sure that you want to change number of movers?'
  },
  showConfirmation: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'cancel', 'add']);

const isModalOpen = ref(props.modelValue);
const address = ref('');

const onCancel = () => {
  isModalOpen.value = false;
  address.value = '';
  emit('update:modelValue', false);
  emit('cancel');
};

const onAdd = () => {
  if (address.value.trim()) {
    emit('add', { address: address.value });
    address.value = '';
    isModalOpen.value = false;
    emit('update:modelValue', false);
  }
};
</script>

<style scoped>
.unloading-point-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 500;
  color: var(--color-text);
}

.form-actions {
  padding-top: 12px;
}

.button-group {
  display: flex;
  justify-content: space-between;
  gap: 12px;
}

.confirmation-message {
  text-align: center;
  padding: 12px;
  background: rgba(93, 135, 238, 0.1);
  border-radius: 8px;
  color: var(--color-text);
}
</style> 