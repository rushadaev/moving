<template>
  <Modal v-model:show="isModalOpen" title="Change number of movers">
    <div class="change-movers-form">
      <div class="form-group">
        <label>New number</label>
        <div class="number-input-container">
          <button 
            type="button"
            class="number-btn decrement"
            @click="decrementMovers"
            :disabled="parseInt(numberOfMovers) <= 1 || !isValid"
          >
            -
          </button>
          <TextField 
            v-model="numberOfMovers" 
            type="number" 
            min="1"
            inputmode="numeric"
            pattern="[0-9]*"
            @input="validateInput"
            @change="validateInput"
            class="number-input"
          />
          <button 
            type="button"
            class="number-btn increment"
            @click="incrementMovers"
          >
            +
          </button>
          <span class="input-label">movers</span>
        </div>
        <div v-if="!isValid && isTouched" class="validation-error">
          Please enter a valid number (minimum 1)
        </div>
      </div>
      
      <div class="action-buttons">
        <GradientButton type="secondary" small-button="true" @click="onCancel">Close</GradientButton>
        <GradientButton small-button="true" @click="onConfirm" :disabled="!isValid">Confirm</GradientButton>
      </div>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import Modal from '@/components/ui/Modal.vue';
import TextField from '@/components/ui/TextField.vue';
import Button from '@/components/ui/Button.vue';
import GradientButton from '@/components/ui/GradientButton.vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  initialCount: {
    type: Number,
    default: 2
  }
});

const emit = defineEmits(['update:show', 'confirm', 'cancel']);

const isModalOpen = ref(props.show);
const numberOfMovers = ref(props.initialCount.toString());
const isValid = ref(true);
const isTouched = ref(false);

// Log when component mounts
onMounted(() => {
  console.log('ChangeMoversModal mounted with props:', 
    { show: props.show, initialCount: props.initialCount }
  );
  validateInput(); // Validate initial value
});

// Watch for changes to the show prop
watch(() => props.show, (newValue) => {
  console.log('show prop changed:', newValue);
  isModalOpen.value = newValue;
  if (newValue) {
    // Reset form when modal opens
    numberOfMovers.value = props.initialCount.toString();
    isTouched.value = false;
    validateInput();
    console.log('Modal opened with initial count:', props.initialCount);
  }
});

// Watch for changes to isModalOpen and emit update:show event
watch(isModalOpen, (newValue) => {
  console.log('isModalOpen changed:', newValue);
  emit('update:show', newValue);
});

// Watch for changes to initialCount prop
watch(() => props.initialCount, (newValue) => {
  console.log('initialCount prop changed:', newValue);
  numberOfMovers.value = newValue.toString();
  validateInput();
});

// Watch for changes to numberOfMovers and validate
watch(numberOfMovers, () => {
  validateInput();
});

const validateInput = () => {
  isTouched.value = true;
  const moversValue = numberOfMovers.value.trim();
  
  // Check if it's a valid number
  if (!moversValue || isNaN(Number(moversValue))) {
    isValid.value = false;
    console.log('Invalid input - not a number:', moversValue);
    return;
  }
  
  // Parse the value
  const movers = parseInt(moversValue);
  
  // Check if it's a positive integer
  isValid.value = Number.isInteger(movers) && movers > 0;
  console.log('Validated input:', { 
    value: moversValue, 
    parsed: movers, 
    isValid: isValid.value 
  });
};

const incrementMovers = () => {
  const currentValue = parseInt(numberOfMovers.value) || 0;
  numberOfMovers.value = (currentValue + 1).toString();
  validateInput();
};

const decrementMovers = () => {
  const currentValue = parseInt(numberOfMovers.value) || 0;
  if (currentValue > 1) {
    numberOfMovers.value = (currentValue - 1).toString();
    validateInput();
  }
};

const onCancel = () => {
  console.log('Cancel clicked');
  isModalOpen.value = false;
  emit('cancel');
};

const onConfirm = () => {
  validateInput(); // Validate one more time before confirming
  
  if (isValid.value) {
    const count = parseInt(numberOfMovers.value);
    console.log('Confirm clicked with value:', count);
    emit('confirm', count);
    isModalOpen.value = false;
  }
};
</script>

<style scoped>
.change-movers-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
  width: 100%;
  max-width: 280px;
  margin: 0 auto;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  width: 100%;
}

.form-group label {
  font-weight: 500;
  color: var(--color-text);
  font-size: 14px;
}

.number-input-container {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
}

.number-input {
  flex: 1;
  min-width: 60px;
  max-width: 80px;
  text-align: center;
}

.number-btn {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
}

.number-btn:hover:not(:disabled) {
  background-color: var(--color-background-mute);
}

.number-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.input-label {
  font-size: 14px;
  color: var(--color-text);
  white-space: nowrap;
  margin-left: 4px;
}

.validation-error {
  color: #f44336;
  font-size: 12px;
  margin-top: 4px;
}

.action-buttons {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  width: 100%;
  margin-top: 16px;
}

.action-buttons > * {
  flex: 1;
}
</style> 