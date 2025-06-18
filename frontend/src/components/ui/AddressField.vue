<template>
  <div class="address-field">
    <TextField 
      v-model="addressValue" 
      :placeholder="placeholder" 
      :disabled="disabled"
      @input="handleInput"
      @blur="handleBlur"
    />
    <button 
      v-if="showRemove" 
      class="remove-button"
      @click="handleRemove"
      type="button"
      :disabled="disabled"
    >
      ×
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import TextField from './TextField.vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Address'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  showRemove: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['update:modelValue', 'remove', 'blur', 'input']);
const addressValue = ref(props.modelValue);

watch(() => props.modelValue, (newValue) => {
  addressValue.value = newValue;
});

watch(addressValue, (newValue) => {
  emit('update:modelValue', newValue);
});

const handleRemove = () => {
  emit('remove');
};

const handleBlur = (e: Event) => {
  emit('blur', e);
};

const handleInput = (e: Event) => {
  emit('input', e);
};
</script>

<style scoped>
.address-field {
  display: flex;
  align-items: center;
  width: 100%;
  position: relative;
}

.remove-button {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #f5f5f5;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  position: absolute;
  right: 8px;
  color: #888;
  font-size: 18px;
  line-height: 1;
  transition: all 0.2s;
}

.remove-button:hover {
  background: #e0e0e0;
  color: #555;
}

.remove-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style> 