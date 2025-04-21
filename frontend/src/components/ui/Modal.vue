<template>
  <div>
    <n-modal 
      v-model:show="showModal"
      :mask-closable="maskClosable"
      class="custom-modal"
    >
      <n-card
        class="modal-card"
        :title="title"
        :bordered="false"
        size="huge"
      >
        <template #header>
          <div class="modal-header">{{ title }}</div>
        </template>
        
        <div class="modal-content">
          <slot></slot>
        </div>
        
        <template #action v-if="$slots.footer">
          <div class="modal-footer">
            <slot name="footer"></slot>
          </div>
        </template>
      </n-card>
    </n-modal>
  </div>
</template>

<script setup lang="ts">
import { NModal, NCard } from 'naive-ui';
import { ref, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  maskClosable: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['update:modelValue']);
const showModal = ref(props.modelValue);

watch(() => props.modelValue, (newValue) => {
  showModal.value = newValue;
});

watch(showModal, (newValue) => {
  emit('update:modelValue', newValue);
});
</script>

<style scoped>
.custom-modal {
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-card {
  max-width: 90vw;
  width: 100%;
  border-radius: 12px;
  background: var(--color-background-soft);
}

.modal-header {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 8px;
}

.modal-content {
  padding: 16px 0;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  padding-top: 16px;
}
</style> 