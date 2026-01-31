<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    href: {
        type: String,
        default: null,
    },
    as: {
        type: String,
        default: 'a', // a, button, Link
    },
    method: {
        type: String,
        default: 'get',
    },
    danger: {
        type: Boolean,
        default: false,
    },
})
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        :method="method"
        :class="[
            'block w-full px-4 py-2 text-left text-sm transition-colors',
            danger 
                ? 'text-red-700 hover:bg-red-50' 
                : 'text-gray-700 hover:bg-gray-100',
        ]"
    >
        <slot />
    </Link>
    <button
        v-else
        type="button"
        :class="[
            'block w-full px-4 py-2 text-left text-sm transition-colors',
            danger 
                ? 'text-red-700 hover:bg-red-50' 
                : 'text-gray-700 hover:bg-gray-100',
        ]"
    >
        <slot />
    </button>
</template>
