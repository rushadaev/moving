<template>
  <div class="checkbox-wrapper">
    <n-checkbox v-model:checked="checkedValue" :disabled="disabled" @update:checked="handleChange">
      <span :class="{ 'checked-label': checkedValue }">{{ label }}</span>
    </n-checkbox>
  </div>
</template>

<script setup lang="ts">
import { NCheckbox } from 'naive-ui';
import { ref, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  label: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'change']);
const checkedValue = ref(props.modelValue);

watch(() => props.modelValue, (newValue) => {
  checkedValue.value = newValue;
});

watch(checkedValue, (newValue) => {
  emit('update:modelValue', newValue);
});

const handleChange = (checked: boolean) => {
  emit('change', checked);
};
</script>

<style scoped>
.checkbox-wrapper {
  display: flex;
  align-items: center;
}

.checked-label {
  font-weight: 500;
  color: var(--color-text);
}
</style> 