<template>
  <Modal v-model:show="isModalOpen" title="Add unloading point">
    <div class="add-point-form">
      <n-form ref="formRef" :model="formData" :rules="rules">
        <n-form-item label="Address" path="address">
          <n-input 
            v-model:value="formData.address" 
            placeholder="Address"
            :disabled="loading"
          />
        </n-form-item>
      </n-form>
    </div>
    
    <template #footer>
      <div class="modal-footer">
        <GradientButton 
          type="secondary" 
          @click="handleCancel"
          :disabled="loading"
        >
          Cancel
        </GradientButton>
        <GradientButton 
          @click="handleAdd"
          :disabled="loading || !formData.address"
          :loading="loading"
        >
          Add
        </GradientButton>
      </div>
    </template>
  </Modal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { NForm, NFormItem, NInput } from 'naive-ui';
import Modal from '@/components/ui/Modal.vue';
import GradientButton from '@/components/ui/GradientButton.vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'add', 'cancel']);

const isModalOpen = ref(props.modelValue);
const loading = ref(false);
const formRef = ref();

const formData = ref({
  address: ''
});

const rules = {
  address: {
    required: true,
    message: 'Please enter an address',
    trigger: ['blur', 'change']
  }
};

watch(() => props.modelValue, (newValue) => {
  isModalOpen.value = newValue;
  if (newValue) {
    // Reset form when opening
    formData.value.address = '';
  }
});

watch(isModalOpen, (newValue) => {
  emit('update:modelValue', newValue);
});

const handleCancel = () => {
  isModalOpen.value = false;
  emit('cancel');
};

const handleAdd = async () => {
  try {
    await formRef.value?.validate();
    emit('add', formData.value.address);
    isModalOpen.value = false;
  } catch (e) {
    // Validation failed
  }
};
</script>

<style scoped>
.add-point-form {
  padding: 16px 0;
}

.modal-footer {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
}
</style>