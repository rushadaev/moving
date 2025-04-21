<template>
  <div class="input-wrapper">
    <n-input
      v-model:value="inputValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :type="type"
      :clearable="clearable"
      :size="size"
      :maxlength="maxlength"
      :show-count="showCount"
      @blur="handleBlur"
      @input="handleInput"
    >
      <template #suffix v-if="unit">
        <span class="unit">{{ unit }}</span>
      </template>
    </n-input>
  </div>
</template>

<script setup lang="ts">
import { NInput } from 'naive-ui';
import { ref, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'text'
  },
  clearable: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'medium'
  },
  unit: {
    type: String,
    default: ''
  },
  maxlength: {
    type: Number,
    default: undefined
  },
  showCount: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'blur', 'input']);
const inputValue = ref(props.modelValue);

watch(() => props.modelValue, (newValue) => {
  inputValue.value = newValue;
});

watch(inputValue, (newValue) => {
  emit('update:modelValue', newValue);
});

const handleBlur = (e: Event) => {
  emit('blur', e);
};

const handleInput = (e: Event) => {
  emit('input', e);
};
</script>

<style scoped>
.input-wrapper {
  width: 100%;
}

.unit {
  margin-left: 4px;
  color: var(--color-text);
  opacity: 0.6;
}
</style> 