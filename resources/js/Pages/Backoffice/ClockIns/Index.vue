<script setup>
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'

const props = defineProps({
    clockIns: Array,
    filters: Object,
    employees: {
        type: Array,
        default: () => []
    },
    workplaces: {
        type: Array,
        default: () => []
    },
})

const form = ref({
    employee_id: props.filters.employee_id ?? '',
    start_date: props.filters.start_date ?? '',
    end_date: props.filters.end_date ?? '',
})

const applyFilters = () => {
    router.get('/backoffice/clock-ins', form.value, {
        preserveState: true,
        preserveScroll: true,
    })
}

const showCreateModal = ref(false)
const createForm = ref({
    employee_id: '',
    type: 'entry',
    timestamp: new Date().toISOString().slice(0, 16),
    latitude: null,
    longitude: null,
    workplace_id: '',
    notes: '',
})

const getLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
            createForm.value.latitude = position.coords.latitude
            createForm.value.longitude = position.coords.longitude
        })
    }
}

const submitClockIn = () => {
    router.post('/backoffice/clock-ins', createForm.value, {
        onSuccess: () => {
            showCreateModal.value = false
            createForm.value = {
                employee_id: '',
                type: 'entry',
                timestamp: new Date().toISOString().slice(0, 16),
                latitude: null,
                longitude: null,
                workplace_id: '',
                notes: '',
            }
        }
    })
}

const typeLabels = {
    entry: 'Entrada',
    exit: 'Salida',
    break_start: 'Inicio pausa',
    break_end: 'Fin pausa',
}

const typeColors = {
    entry: 'bg-green-100 text-green-800',
    exit: 'bg-red-100 text-red-800',
    break_start: 'bg-yellow-100 text-yellow-800',
    break_end: 'bg-blue-100 text-blue-800',
}
</script>

<template>
    <Head title="Fichajes" />

    <BackofficeLayout>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Fichajes</h1>
                <p class="mt-1 text-sm text-gray-500">Registro de entradas y salidas</p>
            </div>
            <button
                @click="showCreateModal = true; getLocation()"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700"
            >
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo fichaje
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Empleado</label>
                    <select 
                        v-model="form.employee_id"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">Todos</option>
                        <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                            {{ emp.full_name }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha inicio</label>
                    <input 
                        type="date" 
                        v-model="form.start_date"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha fin</label>
                    <input 
                        type="date" 
                        v-model="form.end_date"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    />
                </div>
                <div class="flex items-end">
                    <button
                        @click="applyFilters"
                        class="w-full px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700"
                    >
                        Filtrar
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Empleado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Hora
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ubicación
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="clockIn in clockIns" :key="clockIn.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ clockIn.employee_name ?? `#${clockIn.employee_id}` }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="['px-2 py-1 text-xs font-medium rounded-full', typeColors[clockIn.type]]">
                                {{ typeLabels[clockIn.type] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ clockIn.date }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ clockIn.time }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span v-if="clockIn.latitude && clockIn.longitude" class="text-green-600">
                                ✓ GPS
                            </span>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                    </tr>
                    <tr v-if="!clockIns?.length">
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No hay fichajes para mostrar. Selecciona un empleado y rango de fechas.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Create Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-30" @click="showCreateModal = false"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Nuevo fichaje</h3>
                    
                    <form @submit.prevent="submitClockIn" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Empleado</label>
                            <select 
                                v-model="createForm.employee_id"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">Seleccionar...</option>
                                <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                                    {{ emp.full_name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                            <select 
                                v-model="createForm.type"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="entry">Entrada</option>
                                <option value="exit">Salida</option>
                                <option value="break_start">Inicio pausa</option>
                                <option value="break_end">Fin pausa</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y hora</label>
                            <input 
                                type="datetime-local"
                                v-model="createForm.timestamp"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Latitud</label>
                                <input 
                                    type="number"
                                    step="any"
                                    v-model="createForm.latitude"
                                    required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Longitud</label>
                                <input 
                                    type="number"
                                    step="any"
                                    v-model="createForm.longitude"
                                    required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Centro de trabajo</label>
                            <select
                                v-model="createForm.workplace_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">Sin centro</option>
                                <option v-for="wp in workplaces" :key="wp.id" :value="wp.id">
                                    {{ wp.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                            <textarea
                                v-model="createForm.notes"
                                rows="2"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            ></textarea>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button
                                type="button"
                                @click="showCreateModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
                            >
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </BackofficeLayout>
</template>
