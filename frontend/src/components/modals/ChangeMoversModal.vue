<template>
  <Modal v-model="isModalOpen" title="Change number of movers">
    <div class="change-movers-form">
      <div class="form-group">
        <label>New number</label>
        <div class="input-with-label">
          <TextField 
            v-model="numberOfMovers" 
            type="number" 
            min="1"
            @input="validateInput"
          />
          <span class="input-label">movers</span>
        </div>
      </div>
      
      <div class="action-buttons">
        <Button @click="onCancel">Cancel</Button>
        <GradientButton @click="onConfirm" :disabled="!isValid">Confirm</GradientButton>
      </div>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import Modal from '@/components/ui/Modal.vue';
import TextField from '@/components/ui/TextField.vue';
import Button from '@/components/ui/Button.vue';
import GradientButton from '@/components/ui/GradientButton.vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  currentMovers: {
    type: Number,
    default: 2
  }
});

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel']);

const isModalOpen = ref(props.modelValue);
const numberOfMovers = ref(props.currentMovers.toString());
const isValid = ref(true);

watch(() => props.modelValue, (newValue) => {
  isModalOpen.value = newValue;
  if (newValue) {
    // Reset form when modal opens
    numberOfMovers.value = props.currentMovers.toString();
    validateInput();
  }
});

watch(isModalOpen, (newValue) => {
  emit('update:modelValue', newValue);
});

const validateInput = () => {
  const movers = parseInt(numberOfMovers.value);
  isValid.value = !isNaN(movers) && movers > 0;
};

const onCancel = () => {
  isModalOpen.value = false;
  emit('cancel');
};

const onConfirm = () => {
  if (isValid.value) {
    emit('confirm', parseInt(numberOfMovers.value));
    isModalOpen.value = false;
  }
};
</script>

<style scoped>
.change-movers-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 500;
  color: var(--color-text);
  font-size: 14px;
}

.input-with-label {
  display: flex;
  align-items: center;
  gap: 8px;
}

.input-label {
  font-size: 14px;
  color: var(--color-text);
}

.action-buttons {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}
</style> 