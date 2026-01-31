<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const unreadNotificationCount = computed(() => page.props.unreadNotificationCount ?? 0)

const sidebarOpen = ref(true)

const navigation = [
    { name: 'Dashboard', href: '/backoffice', icon: 'home' },
    { name: 'Fichajes', href: '/backoffice/clock-ins', icon: 'clock' },
    { name: 'Empleados', href: '/backoffice/employees', icon: 'users' },
    { name: 'Centros', href: '/backoffice/workplaces', icon: 'building' },
    { name: 'Notificaciones', href: '/backoffice/notifications', icon: 'bell' },
    { name: 'Eventos', href: '/backoffice/events', icon: 'database' },
]

const isCurrentRoute = (href) => {
    return page.url.startsWith(href) && (href !== '/backoffice' || page.url === '/backoffice')
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside 
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-200 ease-in-out',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 bg-indigo-600">
                <span class="text-xl font-bold text-white">Fichajes App</span>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-3">
                <div class="space-y-1">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        :class="[
                            isCurrentRoute(item.href)
                                ? 'bg-indigo-50 text-indigo-600'
                                : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900',
                            'group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors'
                        ]"
                    >
                        <!-- Icons -->
                        <svg 
                            v-if="item.icon === 'home'"
                            class="mr-3 h-5 w-5" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <svg 
                            v-else-if="item.icon === 'clock'"
                            class="mr-3 h-5 w-5" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg 
                            v-else-if="item.icon === 'users'"
                            class="mr-3 h-5 w-5" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg
                            v-else-if="item.icon === 'building'"
                            class="mr-3 h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <svg
                            v-else-if="item.icon === 'bell'"
                            class="mr-3 h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <svg
                            v-else-if="item.icon === 'database'"
                            class="mr-3 h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                        {{ item.name }}
                        <span
                            v-if="item.icon === 'bell' && unreadNotificationCount > 0"
                            class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full"
                        >
                            {{ unreadNotificationCount }}
                        </span>
                    </Link>
                </div>
            </nav>
        </aside>

        <!-- Main content -->
        <div :class="['transition-all duration-200', sidebarOpen ? 'ml-64' : 'ml-0']">
            <!-- Top bar -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between h-16 px-4">
                    <!-- Toggle sidebar -->
                    <button 
                        @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- User menu -->
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-700">{{ user?.name }}</span>
                        <Link 
                            href="/logout" 
                            method="post" 
                            as="button"
                            class="text-sm text-gray-500 hover:text-gray-700"
                        >
                            Cerrar sesión
                        </Link>
                    </div>
                </div>
            </header>

            <!-- Flash messages -->
            <div v-if="$page.props.flash?.success" class="mx-4 mt-4">
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                    {{ $page.props.flash.success }}
                </div>
            </div>
            <div v-if="$page.props.flash?.error" class="mx-4 mt-4">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                    {{ $page.props.flash.error }}
                </div>
            </div>

            <!-- Page content -->
            <main class="p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
