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
import { ref, watch, computed, onMounted } from 'vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  show: {
    type: Boolean,
    default: undefined
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

const emit = defineEmits(['update:modelValue', 'update:show']);

// Determine which prop to use (prioritize show over modelValue if both are provided)
const isVisible = computed(() => {
  if (props.show !== undefined) return props.show;
  return props.modelValue;
});

const showModal = ref(isVisible.value);

onMounted(() => {
  console.log('Modal mounted with props:', { 
    modelValue: props.modelValue, 
    show: props.show,
    computed: isVisible.value
  });
});

// Watch for changes to either prop
watch(() => props.modelValue, (newValue) => {
  console.log('modelValue changed:', newValue);
  if (props.show === undefined) {
    showModal.value = newValue;
  }
});

watch(() => props.show, (newValue) => {
  console.log('show changed:', newValue);
  if (newValue !== undefined) {
    showModal.value = newValue;
  }
});

// Emit updates for both properties
watch(showModal, (newValue) => {
  console.log('showModal changed:', newValue);
  emit('update:modelValue', newValue);
  if (props.show !== undefined) {
    emit('update:show', newValue);
  }
});
</script>

<style scoped>
.custom-modal {
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-card {
  max-width: 720px;
  width: 90vw;
  border-radius: 12px;
  overflow: hidden;
}

.modal-header {
  font-size: 18px;
  font-weight: 600;
  color: var(--color-text);
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

/* Remove deep overrides - let theme handle it */

@media (max-width: 640px) {
  .modal-card {
    max-width: 95vw;
  }
}
</style> 