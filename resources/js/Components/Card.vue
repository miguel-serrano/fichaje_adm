<script setup>
defineProps({
    title: {
        type: String,
        default: '',
    },
    subtitle: {
        type: String,
        default: '',
    },
    padding: {
        type: Boolean,
        default: true,
    },
    hoverable: {
        type: Boolean,
        default: false,
    },
})
</script>

<template>
    <div 
        :class="[
            'bg-white rounded-lg shadow',
            hoverable ? 'hover:shadow-md transition-shadow cursor-pointer' : '',
        ]"
    >
        <!-- Header -->
        <div 
            v-if="title || $slots.header" 
            class="px-6 py-4 border-b border-gray-200"
        >
            <slot name="header">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
                        <p v-if="subtitle" class="mt-1 text-sm text-gray-500">{{ subtitle }}</p>
                    </div>
                    <div v-if="$slots.actions">
                        <slot name="actions" />
                    </div>
                </div>
            </slot>
        </div>

        <!-- Content -->
        <div :class="padding ? 'p-6' : ''">
            <slot />
        </div>

        <!-- Footer -->
        <div 
            v-if="$slots.footer" 
            class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg"
        >
            <slot name="footer" />
        </div>
    </div>
</template>
