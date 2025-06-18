<template>
  <Modal v-model="isModalOpen" title="Add material">
    <div class="add-material-form">
      <div class="form-group">
        <label>Material name</label>
        <TextField 
          v-model="materialName" 
          placeholder="Type here"
          @input="validateInput"
        />
      </div>
      
      <div class="action-buttons">
        <GradientButton type="secondary" small-button="true" @click="onCancel">Close</GradientButton>
        <GradientButton small-button="true" @click="onAdd" :disabled="!isValid">Add</GradientButton>
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
  show: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:show', 'add', 'cancel']);

const isModalOpen = ref(props.show);
const materialName = ref('');
const isValid = ref(false);

watch(() => props.show, (newValue) => {
  isModalOpen.value = newValue;
  if (newValue) {
    // Reset form when modal opens
    materialName.value = '';
    validateInput();
  }
});

watch(isModalOpen, (newValue) => {
  emit('update:show', newValue);
});

const validateInput = () => {
  isValid.value = materialName.value.trim().length > 0;
};

const onCancel = () => {
  isModalOpen.value = false;
  emit('cancel');
};

const onAdd = () => {
  if (isValid.value) {
    emit('add', materialName.value.trim());
    isModalOpen.value = false;
  }
};
</script>

<style scoped>
.add-material-form {
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

.action-buttons {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}
</style> 