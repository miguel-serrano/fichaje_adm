<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    options: {
        type: Array,
        default: () => [],
        // [{ value: 1, label: 'Option 1' }] or ['Option 1', 'Option 2']
    },
    label: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Seleccionar...',
    },
    error: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue'])

const value = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
})

const normalizedOptions = computed(() => {
    return props.options.map((opt) => {
        if (typeof opt === 'object') {
            return opt
        }
        return { value: opt, label: opt }
    })
})

const selectClasses = computed(() => [
    'block w-full rounded-md shadow-sm transition-colors',
    'focus:ring-2 focus:ring-offset-0',
    props.error
        ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500'
        : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500',
    props.disabled ? 'bg-gray-50 cursor-not-allowed' : '',
])
</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        
        <select
            v-model="value"
            :required="required"
            :disabled="disabled"
            :class="selectClasses"
        >
            <option value="" disabled>{{ placeholder }}</option>
            <option 
                v-for="option in normalizedOptions" 
                :key="option.value" 
                :value="option.value"
            >
                {{ option.label }}
            </option>
        </select>

        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>
