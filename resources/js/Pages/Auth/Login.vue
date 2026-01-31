<script setup>
import { ref } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'

const page = usePage()

const form = ref({
    email: '',
    password: '',
    remember: false,
})

const processing = ref(false)

const submit = () => {
    processing.value = true
    router.post('/login', form.value, {
        onFinish: () => {
            processing.value = false
        },
    })
}
</script>

<template>
    <Head title="Iniciar sesión" />

    <div class="min-h-screen bg-gradient-to-br from-blue-600 to-indigo-900 flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <svg class="w-16 h-16 mx-auto text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h1 class="mt-4 text-3xl font-bold text-white">Fichajes App</h1>
            </div>

            <div class="bg-white rounded-lg shadow-xl p-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Iniciar sesión</h2>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autofocus
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            :class="{ 'border-red-500': page.props.errors?.email }"
                        />
                        <p v-if="page.props.errors?.email" class="mt-1 text-sm text-red-600">
                            {{ page.props.errors.email }}
                        </p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            :class="{ 'border-red-500': page.props.errors?.password }"
                        />
                        <p v-if="page.props.errors?.password" class="mt-1 text-sm text-red-600">
                            {{ page.props.errors.password }}
                        </p>
                    </div>

                    <div class="flex items-center">
                        <input
                            id="remember"
                            type="checkbox"
                            v-model="form.remember"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        />
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Recordarme
                        </label>
                    </div>

                    <button
                        type="submit"
                        :disabled="processing"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ processing ? 'Entrando...' : 'Entrar' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
