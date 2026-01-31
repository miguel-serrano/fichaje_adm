<script setup>
import { Head, router } from '@inertiajs/vue3'
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    unread_count: {
        type: Number,
        default: 0,
    },
})

const severityColors = {
    info: 'bg-blue-100 text-blue-600',
    warning: 'bg-yellow-100 text-yellow-600',
    error: 'bg-red-100 text-red-600',
    success: 'bg-green-100 text-green-600',
}

const timeAgo = (dateStr) => {
    const date = new Date(dateStr)
    const now = new Date()
    const seconds = Math.floor((now - date) / 1000)

    if (seconds < 60) return 'hace unos segundos'
    const minutes = Math.floor(seconds / 60)
    if (minutes < 60) return `hace ${minutes}m`
    const hours = Math.floor(minutes / 60)
    if (hours < 24) return `hace ${hours}h`
    const days = Math.floor(hours / 24)
    if (days < 7) return `hace ${days}d`
    return date.toLocaleDateString('es-ES')
}

const markAsRead = (id) => {
    router.post(`/backoffice/notifications/${id}/read`)
}

const markAllAsRead = () => {
    router.post('/backoffice/notifications/read-all')
}
</script>

<template>
    <Head title="Notificaciones" />

    <BackofficeLayout>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Notificaciones</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ unread_count }} sin leer
                </p>
            </div>
            <button
                v-if="unread_count > 0"
                @click="markAllAsRead"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100"
            >
                Marcar todas como leídas
            </button>
        </div>

        <div v-if="items.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p class="mt-4 text-sm text-gray-500">No tienes notificaciones</p>
        </div>

        <div v-else class="space-y-3">
            <div
                v-for="item in items"
                :key="item.id"
                :class="[
                    'bg-white rounded-lg shadow p-4 flex items-start space-x-4',
                    !item.is_read ? 'ring-1 ring-indigo-200' : ''
                ]"
            >
                <!-- Icon -->
                <div :class="['flex-shrink-0 p-2 rounded-full', severityColors[item.severity] || 'bg-gray-100 text-gray-600']">
                    <!-- Bell icon -->
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-medium text-gray-400">{{ item.type_label }}</span>
                        <span v-if="!item.is_read" class="inline-block w-2 h-2 bg-indigo-500 rounded-full"></span>
                    </div>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ item.title }}</p>
                    <p class="text-sm text-gray-500 mt-0.5">{{ item.body }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ timeAgo(item.created_at) }}</p>
                </div>

                <!-- Actions -->
                <div class="flex-shrink-0">
                    <button
                        v-if="!item.is_read"
                        @click="markAsRead(item.id)"
                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
                    >
                        Marcar leída
                    </button>
                    <span v-else class="text-xs text-gray-400">Leída</span>
                </div>
            </div>
        </div>
    </BackofficeLayout>
</template>
