<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'

const props = defineProps({
    employee: Object,
    workplaces: {
        type: Array,
        default: () => [],
    },
})

// ---- Employee form ----
const isEditing = ref(false)
const form = ref({
    name: props.employee.name,
    last_name: props.employee.last_name,
    email: props.employee.email,
    phone: props.employee.phone ?? '',
    nid: props.employee.nid ?? '',
    code: props.employee.code ?? '',
    is_active: props.employee.is_active,
})

const save = () => {
    router.put(`/backoffice/employees/${props.employee.id}`, form.value, {
        onSuccess: () => {
            isEditing.value = false
        }
    })
}

const cancel = () => {
    form.value = {
        name: props.employee.name,
        last_name: props.employee.last_name,
        email: props.employee.email,
        phone: props.employee.phone ?? '',
        nid: props.employee.nid ?? '',
        code: props.employee.code ?? '',
        is_active: props.employee.is_active,
    }
    isEditing.value = false
}

const deleteEmployee = () => {
    if (confirm('¿Estás seguro de que deseas eliminar este empleado? Esta acción no se puede deshacer.')) {
        router.delete(`/backoffice/employees/${props.employee.id}`)
    }
}

// ---- Contract form ----
const contractTypes = [
    { value: 'indefinite', label: 'Indefinido' },
    { value: 'temporary', label: 'Temporal' },
    { value: 'part_time', label: 'Tiempo parcial' },
    { value: 'internship', label: 'Prácticas' },
    { value: 'freelance', label: 'Autónomo' },
]

const isEditingContract = ref(false)
const contractForm = ref({
    type: props.employee.contract?.type ?? 'indefinite',
    start_date: props.employee.contract?.start_date ?? '',
    end_date: props.employee.contract?.end_date ?? '',
    hours_per_week: props.employee.contract?.hours_per_week ?? 40,
})

const saveContract = () => {
    router.put(`/backoffice/employees/${props.employee.id}/contract`, contractForm.value, {
        onSuccess: () => {
            isEditingContract.value = false
        }
    })
}

const cancelContract = () => {
    contractForm.value = {
        type: props.employee.contract?.type ?? 'indefinite',
        start_date: props.employee.contract?.start_date ?? '',
        end_date: props.employee.contract?.end_date ?? '',
        hours_per_week: props.employee.contract?.hours_per_week ?? 40,
    }
    isEditingContract.value = false
}

const deleteContract = () => {
    if (confirm('¿Estás seguro de que deseas eliminar el contrato?')) {
        router.delete(`/backoffice/employees/${props.employee.id}/contract`)
    }
}

// ---- Planification form ----
const dayNames = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']

const buildScheduleFromEmployee = () => {
    const schedule = {}
    for (let d = 1; d <= 7; d++) {
        const dayData = props.employee.planification?.week_schedule?.[d]
        if (dayData) {
            schedule[d] = {
                is_day_off: dayData.is_day_off ?? false,
                slots: (dayData.slots || []).map(s => ({ start_time: s.start_time, end_time: s.end_time })),
            }
        } else {
            schedule[d] = { is_day_off: d >= 6, slots: [] }
        }
    }
    return schedule
}

const isEditingPlanification = ref(false)
const planificationForm = reactive({
    week_schedule: buildScheduleFromEmployee(),
})

const addSlot = (day) => {
    planificationForm.week_schedule[day].slots.push({ start_time: '09:00', end_time: '14:00' })
}

const removeSlot = (day, index) => {
    planificationForm.week_schedule[day].slots.splice(index, 1)
}

const toggleDayOff = (day) => {
    const d = planificationForm.week_schedule[day]
    d.is_day_off = !d.is_day_off
    if (d.is_day_off) {
        d.slots = []
    }
}

const totalPlanifiedHours = computed(() => {
    let total = 0
    for (let d = 1; d <= 7; d++) {
        const day = planificationForm.week_schedule[d]
        if (!day.is_day_off) {
            for (const slot of day.slots) {
                if (slot.start_time && slot.end_time) {
                    const [sh, sm] = slot.start_time.split(':').map(Number)
                    const [eh, em] = slot.end_time.split(':').map(Number)
                    const diff = (eh * 60 + em) - (sh * 60 + sm)
                    if (diff > 0) total += diff / 60
                }
            }
        }
    }
    return Math.round(total * 100) / 100
})

const savePlanification = () => {
    router.put(`/backoffice/employees/${props.employee.id}/planification`, {
        week_schedule: planificationForm.week_schedule,
    }, {
        onSuccess: () => {
            isEditingPlanification.value = false
        }
    })
}

const cancelPlanification = () => {
    planificationForm.week_schedule = buildScheduleFromEmployee()
    isEditingPlanification.value = false
}

const deletePlanification = () => {
    if (confirm('¿Estás seguro de que deseas eliminar el horario semanal?')) {
        router.delete(`/backoffice/employees/${props.employee.id}/planification`)
    }
}

// ---- Workplaces form ----
const isEditingWorkplaces = ref(false)
const selectedWorkplaceIds = ref([...(props.employee.workplace_ids ?? [])])

const saveWorkplaces = () => {
    router.put(`/backoffice/employees/${props.employee.id}/workplaces`, {
        workplace_ids: selectedWorkplaceIds.value,
    }, {
        onSuccess: () => {
            isEditingWorkplaces.value = false
        }
    })
}

const cancelWorkplaces = () => {
    selectedWorkplaceIds.value = [...(props.employee.workplace_ids ?? [])]
    isEditingWorkplaces.value = false
}

const toggleWorkplace = (wpId) => {
    const idx = selectedWorkplaceIds.value.indexOf(wpId)
    if (idx >= 0) {
        selectedWorkplaceIds.value.splice(idx, 1)
    } else {
        selectedWorkplaceIds.value.push(wpId)
    }
}

const assignedWorkplaces = computed(() => {
    return props.workplaces.filter(wp => (props.employee.workplace_ids ?? []).includes(wp.id))
})
</script>

<template>
    <Head :title="employee.name + ' ' + employee.last_name" />

    <BackofficeLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <Link
                    href="/backoffice/employees"
                    class="text-gray-400 hover:text-gray-500"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ employee.name }} {{ employee.last_name }}
                </h1>
                <span
                    :class="[
                        'px-2 py-1 text-xs font-medium rounded-full',
                        employee.is_active
                            ? 'bg-green-100 text-green-800'
                            : 'bg-gray-100 text-gray-800'
                    ]"
                >
                    {{ employee.is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">Información personal</h2>
                        <button
                            v-if="!isEditing"
                            @click="isEditing = true"
                            class="text-sm text-indigo-600 hover:text-indigo-800"
                        >
                            Editar
                        </button>
                    </div>

                    <div class="p-6">
                        <form v-if="isEditing" @submit.prevent="save" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                    <input
                                        type="text"
                                        v-model="form.name"
                                        required
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                                    <input
                                        type="text"
                                        v-model="form.last_name"
                                        required
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input
                                    type="email"
                                    v-model="form.email"
                                    required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                <input
                                    type="tel"
                                    v-model="form.phone"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">DNI/NIF</label>
                                    <input
                                        type="text"
                                        v-model="form.nid"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                                    <input
                                        type="text"
                                        v-model="form.code"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                </div>
                            </div>

                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.is_active"
                                    id="is_active"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                />
                                <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                    Empleado activo
                                </label>
                            </div>

                            <div class="flex justify-end space-x-3 pt-4">
                                <button
                                    type="button"
                                    @click="cancel"
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

                        <dl v-else class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nombre completo</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ employee.name }} {{ employee.last_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ employee.email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ employee.phone ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">DNI/NIF</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ employee.nid ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Código</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ employee.code ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Planification editor -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">Horario semanal</h2>
                        <div class="flex items-center space-x-3">
                            <button
                                v-if="!isEditingPlanification && employee.planification"
                                @click="deletePlanification"
                                class="text-sm text-red-600 hover:text-red-800"
                            >
                                Eliminar
                            </button>
                            <button
                                v-if="!isEditingPlanification"
                                @click="isEditingPlanification = true"
                                class="text-sm text-indigo-600 hover:text-indigo-800"
                            >
                                {{ employee.planification ? 'Editar' : 'Crear' }}
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <form v-if="isEditingPlanification" @submit.prevent="savePlanification" class="space-y-4">
                            <div
                                v-for="day in 7"
                                :key="day"
                                class="border border-gray-200 rounded-md p-4"
                            >
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">{{ dayNames[day - 1] }}</span>
                                    <label class="inline-flex items-center">
                                        <input
                                            type="checkbox"
                                            :checked="planificationForm.week_schedule[day].is_day_off"
                                            @change="toggleDayOff(day)"
                                            class="h-4 w-4 text-gray-400 border-gray-300 rounded"
                                        />
                                        <span class="ml-2 text-sm text-gray-500">Día libre</span>
                                    </label>
                                </div>

                                <div v-if="!planificationForm.week_schedule[day].is_day_off" class="space-y-2">
                                    <div
                                        v-for="(slot, idx) in planificationForm.week_schedule[day].slots"
                                        :key="idx"
                                        class="flex items-center space-x-2"
                                    >
                                        <input
                                            type="time"
                                            v-model="slot.start_time"
                                            class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        />
                                        <span class="text-gray-400">—</span>
                                        <input
                                            type="time"
                                            v-model="slot.end_time"
                                            class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        />
                                        <button
                                            type="button"
                                            @click="removeSlot(day, idx)"
                                            class="text-red-400 hover:text-red-600"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <button
                                        type="button"
                                        @click="addSlot(day)"
                                        class="text-sm text-indigo-600 hover:text-indigo-800"
                                    >
                                        + Turno
                                    </button>
                                </div>
                                <p v-else class="text-sm text-gray-400 italic">Sin horario</p>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                                <span class="text-sm font-medium text-gray-700">
                                    Total horas: <span class="text-indigo-600">{{ totalPlanifiedHours }}h</span>
                                </span>
                                <div class="flex space-x-3">
                                    <button
                                        type="button"
                                        @click="cancelPlanification"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                    >
                                        Cancelar
                                    </button>
                                    <button
                                        type="submit"
                                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
                                    >
                                        Guardar horario
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div v-else-if="employee.planification" class="space-y-2">
                            <div
                                v-for="day in 7"
                                :key="day"
                                class="flex items-center justify-between text-sm py-1"
                            >
                                <span class="font-medium text-gray-700 w-24">{{ dayNames[day - 1] }}</span>
                                <span v-if="employee.planification.week_schedule[day]?.is_day_off" class="text-gray-400">Libre</span>
                                <span v-else-if="employee.planification.week_schedule[day]?.slots?.length" class="text-gray-900">
                                    <span
                                        v-for="(slot, idx) in employee.planification.week_schedule[day].slots"
                                        :key="idx"
                                    >
                                        {{ slot.start_time }}–{{ slot.end_time }}<span v-if="idx < employee.planification.week_schedule[day].slots.length - 1">, </span>
                                    </span>
                                </span>
                                <span v-else class="text-gray-400">—</span>
                            </div>
                            <div class="flex justify-between text-sm pt-2 border-t border-gray-200">
                                <span class="text-gray-500">Horas totales</span>
                                <span class="font-medium text-gray-900">{{ employee.planification.total_week_hours }}h</span>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-400">Sin horario asignado</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Contract info -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">Contrato</h2>
                        <div class="flex items-center space-x-3">
                            <button
                                v-if="!isEditingContract && employee.contract"
                                @click="deleteContract"
                                class="text-sm text-red-600 hover:text-red-800"
                            >
                                Eliminar
                            </button>
                            <button
                                v-if="!isEditingContract"
                                @click="isEditingContract = true"
                                class="text-sm text-indigo-600 hover:text-indigo-800"
                            >
                                {{ employee.contract ? 'Editar' : 'Crear' }}
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <form v-if="isEditingContract" @submit.prevent="saveContract" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                                <select
                                    v-model="contractForm.type"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option v-for="ct in contractTypes" :key="ct.value" :value="ct.value">{{ ct.label }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha inicio</label>
                                <input
                                    type="date"
                                    v-model="contractForm.start_date"
                                    required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha fin</label>
                                <input
                                    type="date"
                                    v-model="contractForm.end_date"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                                <p class="mt-1 text-xs text-gray-400">Dejar vacío para contrato indefinido</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Horas/semana</label>
                                <input
                                    type="number"
                                    v-model.number="contractForm.hours_per_week"
                                    min="1"
                                    max="168"
                                    step="0.5"
                                    required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                            <div class="flex justify-end space-x-3 pt-2">
                                <button
                                    type="button"
                                    @click="cancelContract"
                                    class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    class="px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
                                >
                                    Guardar
                                </button>
                            </div>
                        </form>

                        <dl v-else-if="employee.contract" class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                                <dd class="mt-1 text-sm text-gray-900 capitalize">{{ employee.contract.type }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Inicio</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ employee.contract.start_date }}</dd>
                            </div>
                            <div v-if="employee.contract.end_date">
                                <dt class="text-sm font-medium text-gray-500">Fin</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ employee.contract.end_date }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Horas/semana</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ employee.contract.hours_per_week }}h</dd>
                            </div>
                        </dl>
                        <p v-else class="text-sm text-gray-400">Sin contrato asignado</p>
                    </div>
                </div>

                <!-- Workplaces -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">Centros de trabajo</h2>
                        <button
                            v-if="!isEditingWorkplaces"
                            @click="isEditingWorkplaces = true"
                            class="text-sm text-indigo-600 hover:text-indigo-800"
                        >
                            Editar
                        </button>
                    </div>
                    <div class="p-6">
                        <form v-if="isEditingWorkplaces" @submit.prevent="saveWorkplaces" class="space-y-3">
                            <div
                                v-for="wp in workplaces"
                                :key="wp.id"
                                class="flex items-center"
                            >
                                <input
                                    type="checkbox"
                                    :id="'wp-' + wp.id"
                                    :checked="selectedWorkplaceIds.includes(wp.id)"
                                    @change="toggleWorkplace(wp.id)"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                />
                                <label :for="'wp-' + wp.id" class="ml-2 block text-sm text-gray-700">
                                    {{ wp.name }}
                                </label>
                            </div>
                            <p v-if="!workplaces.length" class="text-sm text-gray-400">No hay centros de trabajo activos.</p>
                            <div class="flex justify-end space-x-3 pt-2">
                                <button
                                    type="button"
                                    @click="cancelWorkplaces"
                                    class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    class="px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
                                >
                                    Guardar
                                </button>
                            </div>
                        </form>

                        <div v-else-if="assignedWorkplaces.length" class="space-y-2">
                            <Link
                                v-for="wp in assignedWorkplaces"
                                :key="wp.id"
                                :href="`/backoffice/workplaces/${wp.id}`"
                                class="block text-sm text-indigo-600 hover:text-indigo-800"
                            >
                                {{ wp.name }}
                            </Link>
                        </div>
                        <p v-else class="text-sm text-gray-400">Sin centros asignados</p>
                    </div>
                </div>

                <!-- Quick actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Acciones</h3>
                    <div class="space-y-2">
                        <Link
                            :href="`/backoffice/clock-ins?employee_id=${employee.id}`"
                            class="block w-full text-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                        >
                            Ver fichajes
                        </Link>
                        <Link
                            :href="`/backoffice/clock-ins/report?employee_id=${employee.id}`"
                            class="block w-full text-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                        >
                            Informe de horas
                        </Link>
                        <button
                            @click="deleteEmployee"
                            class="block w-full text-center px-4 py-2 text-sm font-medium text-red-700 bg-red-50 rounded-md hover:bg-red-100"
                        >
                            Eliminar empleado
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </BackofficeLayout>
</template>
