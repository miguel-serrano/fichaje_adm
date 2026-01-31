<script setup>
import { computed } from 'vue'

const props = defineProps({
    variant: {
        type: String,
        default: 'default', // default, success, warning, danger, info
    },
    size: {
        type: String,
        default: 'md', // sm, md, lg
    },
    rounded: {
        type: Boolean,
        default: true,
    },
    dot: {
        type: Boolean,
        default: false,
    },
})

const variantClasses = {
    default: 'bg-gray-100 text-gray-800',
    success: 'bg-green-100 text-green-800',
    warning: 'bg-yellow-100 text-yellow-800',
    danger: 'bg-red-100 text-red-800',
    info: 'bg-blue-100 text-blue-800',
    indigo: 'bg-indigo-100 text-indigo-800',
}

const sizeClasses = {
    sm: 'px-2 py-0.5 text-xs',
    md: 'px-2.5 py-0.5 text-xs',
    lg: 'px-3 py-1 text-sm',
}

const dotColors = {
    default: 'bg-gray-400',
    success: 'bg-green-400',
    warning: 'bg-yellow-400',
    danger: 'bg-red-400',
    info: 'bg-blue-400',
    indigo: 'bg-indigo-400',
}

const classes = computed(() => [
    'inline-flex items-center font-medium',
    props.rounded ? 'rounded-full' : 'rounded',
    variantClasses[props.variant],
    sizeClasses[props.size],
])
</script>

<template>
    <span :class="classes">
        <svg 
            v-if="dot" 
            :class="['mr-1.5 h-2 w-2', dotColors[variant]]" 
            fill="currentColor" 
            viewBox="0 0 8 8"
        >
            <circle cx="4" cy="4" r="3" />
        </svg>
        <slot />
    </span>
</template>
