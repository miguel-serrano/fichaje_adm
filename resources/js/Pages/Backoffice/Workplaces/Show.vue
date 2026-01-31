<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'

const props = defineProps({
    workplace: Object,
    employees: {
        type: Array,
        default: () => [],
    },
})

const isEditing = ref(false)
const form = ref({
    name: props.workplace.name,
    address: props.workplace.address ?? '',
    city: props.workplace.city ?? '',
    postal_code: props.workplace.postal_code ?? '',
    latitude: props.workplace.latitude,
    longitude: props.workplace.longitude,
    radius: props.workplace.radius ?? 100,
    is_active: props.workplace.is_active,
})

const save = () => {
    router.put(`/backoffice/workplaces/${props.workplace.id}`, form.value, {
        onSuccess: () => {
            isEditing.value = false
        }
    })
}

const cancel = () => {
    form.value = {
        name: props.workplace.name,
        address: props.workplace.address ?? '',
        city: props.workplace.city ?? '',
        postal_code: props.workplace.postal_code ?? '',
        latitude: props.workplace.latitude,
        longitude: props.workplace.longitude,
        radius: props.workplace.radius ?? 100,
        is_active: props.workplace.is_active,
    }
    isEditing.value = false
}

const getLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
            form.value.latitude = position.coords.latitude
            form.value.longitude = position.coords.longitude
        })
    }
}

const deleteWorkplace = () => {
    if (confirm('¿Estás seguro de que deseas eliminar este centro de trabajo? Esta acción no se puede deshacer.')) {
        router.delete(`/backoffice/workplaces/${props.workplace.id}`)
    }
}
</script>

<template>
    <Head :title="workplace.name" />

    <BackofficeLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <Link 
                    href="/backoffice/workplaces"
                    class="text-gray-400 hover:text-gray-500"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h1 class="text-2xl font-semibold text-gray-900">{{ workplace.name }}</h1>
                <span 
                    :class="[
                        'px-2 py-1 text-xs font-medium rounded-full',
                        workplace.is_active 
                            ? 'bg-green-100 text-green-800' 
                            : 'bg-gray-100 text-gray-800'
                    ]"
                >
                    {{ workplace.is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main info -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">Información del centro</h2>
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                                <input 
                                    type="text"
                                    v-model="form.address"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                                    <input 
                                        type="text"
                                        v-model="form.city"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Código postal</label>
                                    <input 
                                        type="text"
                                        v-model="form.postal_code"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-medium text-gray-700">Geolocalización</span>
                                    <button 
                                        type="button"
                                        @click="getLocation"
                                        class="text-sm text-indigo-600 hover:text-indigo-800"
                                    >
                                        Usar mi ubicación
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Latitud</label>
                                        <input 
                                            type="number"
                                            step="any"
                                            v-model="form.latitude"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Longitud</label>
                                        <input 
                                            type="number"
                                            step="any"
                                            v-model="form.longitude"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        />
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Radio de geofence (metros)
                                    </label>
                                    <input 
                                        type="number"
                                        v-model="form.radius"
                                        min="0"
                                        max="10000"
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
                                    Centro activo
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
                            <div class="col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ workplace.name }}</dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Dirección</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ workplace.address ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ciudad</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ workplace.city ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Código postal</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ workplace.postal_code ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Employees assigned -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Empleados asignados</h2>
                    </div>
                    <div class="p-6">
                        <div v-if="employees.length" class="space-y-3">
                            <Link
                                v-for="emp in employees"
                                :key="emp.id"
                                :href="`/backoffice/employees/${emp.id}`"
                                class="flex items-center justify-between py-1"
                            >
                                <span class="text-sm text-indigo-600 hover:text-indigo-800">
                                    {{ emp.name }} {{ emp.last_name }}
                                </span>
                                <span
                                    :class="[
                                        'px-1.5 py-0.5 text-xs font-medium rounded-full',
                                        emp.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800'
                                    ]"
                                >
                                    {{ emp.is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </Link>
                        </div>
                        <p v-else class="text-sm text-gray-400">No hay empleados asignados a este centro.</p>
                    </div>
                </div>

                <!-- Geofence info -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Geofence</h2>
                    </div>
                    <div class="p-6">
                        <div v-if="workplace.has_geofence" class="space-y-3">
                            <div class="flex items-center text-green-600">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Geofence activo
                            </div>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Coordenadas</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ workplace.latitude?.toFixed(6) }}, {{ workplace.longitude?.toFixed(6) }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Radio</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ workplace.radius }} metros</dd>
                                </div>
                            </dl>
                        </div>
                        <div v-else class="text-center py-4">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Sin geofence configurado</p>
                            <p class="text-xs text-gray-400">Los empleados podrán fichar desde cualquier ubicación.</p>
                        </div>
                    </div>
                </div>

                <!-- Map placeholder -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Ubicación</h2>
                    </div>
                    <div class="p-6">
                        <div 
                            v-if="workplace.latitude && workplace.longitude"
                            class="aspect-video bg-gray-100 rounded-lg flex items-center justify-center"
                        >
                            <a 
                                :href="`https://www.google.com/maps?q=${workplace.latitude},${workplace.longitude}`"
                                target="_blank"
                                class="text-indigo-600 hover:text-indigo-800 text-sm"
                            >
                                Ver en Google Maps →
                            </a>
                        </div>
                        <p v-else class="text-sm text-gray-400 text-center">Sin coordenadas</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Acciones</h3>
                    <button
                        @click="deleteWorkplace"
                        class="block w-full text-center px-4 py-2 text-sm font-medium text-red-700 bg-red-50 rounded-md hover:bg-red-100"
                    >
                        Eliminar centro
                    </button>
                </div>
            </div>
        </div>
    </BackofficeLayout>
</template>
