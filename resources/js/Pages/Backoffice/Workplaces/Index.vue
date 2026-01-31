<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'

const props = defineProps({
    workplaces: Array,
    filters: Object,
})

const showCreateModal = ref(false)
const createForm = ref({
    name: '',
    address: '',
    city: '',
    postal_code: '',
    latitude: null,
    longitude: null,
    radius: 100,
})

const getLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
            createForm.value.latitude = position.coords.latitude
            createForm.value.longitude = position.coords.longitude
        })
    }
}

const submitWorkplace = () => {
    router.post('/backoffice/workplaces', createForm.value, {
        onSuccess: () => {
            showCreateModal.value = false
            createForm.value = {
                name: '',
                address: '',
                city: '',
                postal_code: '',
                latitude: null,
                longitude: null,
                radius: 100,
            }
        }
    })
}
</script>

<template>
    <Head title="Centros de trabajo" />

    <BackofficeLayout>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Centros de trabajo</h1>
                <p class="mt-1 text-sm text-gray-500">Gestión de ubicaciones para fichajes</p>
            </div>
            <button
                @click="showCreateModal = true"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700"
            >
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo centro
            </button>
        </div>

        <!-- Cards grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
                v-for="workplace in workplaces" 
                :key="workplace.id"
                class="bg-white rounded-lg shadow hover:shadow-md transition-shadow"
            >
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ workplace.name }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ workplace.address ?? 'Sin dirección' }}
                            </p>
                            <p v-if="workplace.city" class="text-sm text-gray-500">
                                {{ workplace.postal_code }} {{ workplace.city }}
                            </p>
                        </div>
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

                    <div class="mt-4 flex items-center space-x-4 text-sm">
                        <div v-if="workplace.has_geofence" class="flex items-center text-green-600">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Geofence ({{ workplace.radius }}m)
                        </div>
                        <div v-else class="flex items-center text-gray-400">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            Sin geofence
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <Link 
                            :href="`/backoffice/workplaces/${workplace.id}`"
                            class="text-sm text-indigo-600 hover:text-indigo-800"
                        >
                            Ver detalles →
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div 
                v-if="!workplaces?.length"
                class="col-span-full bg-white rounded-lg shadow p-12 text-center"
            >
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Sin centros de trabajo</h3>
                <p class="mt-2 text-sm text-gray-500">Comienza creando tu primer centro de trabajo.</p>
            </div>
        </div>

        <!-- Create Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-30" @click="showCreateModal = false"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Nuevo centro de trabajo</h3>
                    
                    <form @submit.prevent="submitWorkplace" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                            <input 
                                type="text"
                                v-model="createForm.name"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                            <input 
                                type="text"
                                v-model="createForm.address"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                                <input 
                                    type="text"
                                    v-model="createForm.city"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Código postal</label>
                                <input 
                                    type="text"
                                    v-model="createForm.postal_code"
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
                                        v-model="createForm.latitude"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Longitud</label>
                                    <input 
                                        type="number"
                                        step="any"
                                        v-model="createForm.longitude"
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
                                    v-model="createForm.radius"
                                    min="0"
                                    max="10000"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Los empleados solo podrán fichar dentro de este radio.
                                </p>
                            </div>
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
