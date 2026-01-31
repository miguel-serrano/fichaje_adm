<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'

const props = defineProps({
    events: {
        type: Array,
        default: () => [],
    },
    totalEvents: {
        type: Number,
        default: 0,
    },
    pendingEvents: {
        type: Number,
        default: 0,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
})

const expandedRow = ref(null)
const syncing = ref(false)

const toggleRow = (eventId) => {
    expandedRow.value = expandedRow.value === eventId ? null : eventId
}

const syncEvents = () => {
    syncing.value = true
    router.post('/backoffice/events/sync', {}, {
        onFinish: () => {
            syncing.value = false
        }
    })
}

const shortName = (eventName) => {
    const parts = eventName.split('.')
    return parts[parts.length - 1] || eventName
}

const formatDate = (dateStr) => {
    if (!dateStr) return '-'
    const d = new Date(dateStr)
    return d.toLocaleString('es-ES', { dateStyle: 'short', timeStyle: 'medium' })
}
</script>

<template>
    <Head title="Eventos de Dominio" />

    <BackofficeLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Eventos de Dominio</h1>
            <p class="mt-1 text-sm text-gray-500">Event store del sistema</p>
        </div>

        <!-- Stats cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total eventos</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ totalEvents }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pendientes</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ pendingEvents }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 flex items-center justify-center">
                <button
                    @click="syncEvents"
                    :disabled="syncing || pendingEvents === 0"
                    :class="[
                        'inline-flex items-center px-4 py-2 text-sm font-medium rounded-md',
                        syncing || pendingEvents === 0
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            : 'bg-indigo-600 text-white hover:bg-indigo-700'
                    ]"
                >
                    <svg v-if="syncing" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ syncing ? 'Sincronizando...' : 'Sincronizar a Elasticsearch' }}
                </button>
            </div>
        </div>

        <!-- Events table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div v-if="events.length === 0" class="p-12 text-center">
                <p class="text-sm text-gray-500">No hay eventos registrados</p>
            </div>

            <table v-else class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aggregate ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template v-for="event in events" :key="event.event_id">
                        <tr
                            class="hover:bg-gray-50 cursor-pointer"
                            @click="toggleRow(event.event_id)"
                        >
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ shortName(event.event_name) }}
                                <p class="text-xs text-gray-400 font-normal">{{ event.event_name }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 font-mono">{{ event.aggregate_id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(event.occurred_on) }}</td>
                            <td class="px-6 py-4">
                                <span
                                    :class="[
                                        'px-2 py-1 text-xs font-medium rounded-full',
                                        event.metadata?.published_at
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-yellow-100 text-yellow-800'
                                    ]"
                                >
                                    {{ event.metadata?.published_at ? 'Publicado' : 'Pendiente' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <svg
                                    :class="['h-5 w-5 text-gray-400 transition-transform', expandedRow === event.event_id ? 'rotate-180' : '']"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </td>
                        </tr>
                        <tr v-if="expandedRow === event.event_id">
                            <td colspan="5" class="px-6 py-4 bg-gray-50">
                                <div class="text-xs font-mono">
                                    <p class="font-medium text-gray-700 mb-2">Payload:</p>
                                    <pre class="bg-gray-900 text-green-400 p-4 rounded-md overflow-x-auto">{{ JSON.stringify(event.payload, null, 2) }}</pre>
                                    <p v-if="event.metadata" class="font-medium text-gray-700 mt-3 mb-2">Metadata:</p>
                                    <pre v-if="event.metadata" class="bg-gray-900 text-green-400 p-4 rounded-md overflow-x-auto">{{ JSON.stringify(event.metadata, null, 2) }}</pre>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </BackofficeLayout>
</template>
