<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    icon: {
        type: String,
        default: null,
    },
    iconColor: {
        type: String,
        default: 'indigo', // indigo, green, blue, yellow, red
    },
    trend: {
        type: Number,
        default: null,
    },
    trendLabel: {
        type: String,
        default: 'vs mes anterior',
    },
})

const iconColors = {
    indigo: 'bg-indigo-100 text-indigo-600',
    green: 'bg-green-100 text-green-600',
    blue: 'bg-blue-100 text-blue-600',
    yellow: 'bg-yellow-100 text-yellow-600',
    red: 'bg-red-100 text-red-600',
}
</script>

<template>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div v-if="icon || $slots.icon" :class="['p-3 rounded-full', iconColors[iconColor]]">
                <slot name="icon">
                    <!-- Clock -->
                    <svg v-if="icon === 'clock'" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <!-- Users -->
                    <svg v-else-if="icon === 'users'" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Building -->
                    <svg v-else-if="icon === 'building'" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <!-- Warning -->
                    <svg v-else-if="icon === 'warning'" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <!-- Chart -->
                    <svg v-else-if="icon === 'chart'" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </slot>
            </div>
            <div :class="icon || $slots.icon ? 'ml-4' : ''">
                <p class="text-sm font-medium text-gray-500">{{ title }}</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ value }}</p>
            </div>
        </div>
        
        <!-- Trend -->
        <div v-if="trend !== null" class="mt-4 flex items-center text-sm">
            <span 
                :class="[
                    'inline-flex items-center',
                    trend >= 0 ? 'text-green-600' : 'text-red-600',
                ]"
            >
                <svg 
                    v-if="trend >= 0"
                    class="h-4 w-4 mr-1" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
                <svg 
                    v-else
                    class="h-4 w-4 mr-1" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
                {{ Math.abs(trend) }}%
            </span>
            <span class="ml-2 text-gray-500">{{ trendLabel }}</span>
        </div>
    </div>
</template>
