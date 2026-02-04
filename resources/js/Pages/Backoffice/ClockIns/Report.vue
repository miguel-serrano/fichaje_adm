<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'

const props = defineProps({
    report: Object,
    filters: Object,
    employees: {
        type: Array,
        default: () => []
    }
})

const form = ref({
    employee_id: props.filters.employee_id ?? '',
    start_date: props.filters.start_date ?? '',
    end_date: props.filters.end_date ?? '',
})

const applyFilters = () => {
    router.get('/backoffice/clock-ins/report', form.value, {
        preserveState: true,
        preserveScroll: true,
    })
}

const formatHours = (minutes) => {
    if (!minutes) return '0h 0m'
    const hours = Math.floor(minutes / 60)
    const mins = minutes % 60
    return `${hours}h ${mins}m`
}
</script>

<template>
    <Head title="Informe de horas" />

    <BackofficeLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Informe de horas trabajadas</h1>
            <p class="mt-1 text-sm text-gray-500">Comparativa entre horas fichadas y planificadas</p>
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
                        <option value="">Seleccionar...</option>
                        <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                            {{ emp.name }}
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
                        Generar informe
                    </button>
                </div>
            </div>
        </div>

        <!-- Report -->
        <div v-if="report" class="space-y-6">
            <!-- Summary cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-500">Horas trabajadas</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">
                        {{ report.worked_hours?.net_worked_hours?.toFixed(1) ?? 0 }}h
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow p-6" v-if="report.comparison">
                    <h3 class="text-sm font-medium text-gray-500">Horas esperadas</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">
                        {{ report.comparison?.expected_hours?.toFixed(1) ?? 0 }}h
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow p-6" v-if="report.comparison">
                    <h3 class="text-sm font-medium text-gray-500">Diferencia</h3>
                    <p 
                        class="mt-2 text-3xl font-semibold"
                        :class="report.comparison?.difference_hours >= 0 ? 'text-green-600' : 'text-red-600'"
                    >
                        {{ report.comparison?.difference_hours >= 0 ? '+' : '' }}{{ report.comparison?.difference_hours?.toFixed(1) ?? 0 }}h
                    </p>
                </div>
            </div>

            <!-- Issues -->
            <div v-if="report.comparison" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Late arrivals -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">
                        Retrasos 
                        <span class="text-red-600">({{ report.comparison.late_arrivals?.length ?? 0 }})</span>
                    </h3>
                    <ul v-if="report.comparison.late_arrivals?.length" class="space-y-2">
                        <li 
                            v-for="late in report.comparison.late_arrivals" 
                            :key="late.date"
                            class="text-sm text-gray-600"
                        >
                            <span class="font-medium">{{ late.date }}</span>: 
                            {{ late.minutes_late }} min tarde
                            <span class="text-gray-400">({{ late.actual_time }} vs {{ late.expected_time }})</span>
                        </li>
                    </ul>
                    <p v-else class="text-sm text-gray-400">Sin retrasos</p>
                </div>

                <!-- Early departures -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">
                        Salidas anticipadas
                        <span class="text-yellow-600">({{ report.comparison.early_departures?.length ?? 0 }})</span>
                    </h3>
                    <ul v-if="report.comparison.early_departures?.length" class="space-y-2">
                        <li 
                            v-for="early in report.comparison.early_departures" 
                            :key="early.date"
                            class="text-sm text-gray-600"
                        >
                            <span class="font-medium">{{ early.date }}</span>: 
                            {{ early.minutes_early }} min antes
                        </li>
                    </ul>
                    <p v-else class="text-sm text-gray-400">Sin salidas anticipadas</p>
                </div>

                <!-- Missed days -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">
                        Días sin fichar
                        <span class="text-orange-600">({{ report.comparison.missed_days?.length ?? 0 }})</span>
                    </h3>
                    <ul v-if="report.comparison.missed_days?.length" class="space-y-2">
                        <li 
                            v-for="day in report.comparison.missed_days" 
                            :key="day"
                            class="text-sm text-gray-600"
                        >
                            {{ day }}
                        </li>
                    </ul>
                    <p v-else class="text-sm text-gray-400">Todos los días fichados</p>
                </div>
            </div>

            <!-- Daily breakdown -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Desglose diario</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrada</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salida</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horas netas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(day, date) in report.worked_hours?.daily_hours" :key="date">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ date }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ day.first_entry ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ day.last_exit ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ day.net_hours?.toFixed(1) }}h
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span 
                                    v-if="day.has_incomplete_entry"
                                    class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800"
                                >
                                    Incompleto
                                </span>
                                <span 
                                    v-else
                                    class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800"
                                >
                                    Completo
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Sin datos</h3>
            <p class="mt-2 text-sm text-gray-500">Selecciona un empleado y rango de fechas para generar el informe.</p>
        </div>
    </BackofficeLayout>
</template>
