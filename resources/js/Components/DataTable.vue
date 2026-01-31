<script setup>
import { computed } from 'vue'

const props = defineProps({
    columns: {
        type: Array,
        required: true,
        // [{ key: 'name', label: 'Name', sortable: true, align: 'left' }]
    },
    rows: {
        type: Array,
        default: () => [],
    },
    loading: {
        type: Boolean,
        default: false,
    },
    emptyText: {
        type: String,
        default: 'No hay datos para mostrar.',
    },
    striped: {
        type: Boolean,
        default: false,
    },
    hoverable: {
        type: Boolean,
        default: true,
    },
})

const emit = defineEmits(['row-click'])

const alignmentClass = (align) => {
    return {
        left: 'text-left',
        center: 'text-center',
        right: 'text-right',
    }[align || 'left']
}
</script>

<template>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            v-for="column in columns"
                            :key="column.key"
                            :class="[
                                'px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider',
                                alignmentClass(column.align),
                            ]"
                        >
                            {{ column.label }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Loading state -->
                    <tr v-if="loading">
                        <td :colspan="columns.length" class="px-6 py-12 text-center">
                            <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Cargando...</p>
                        </td>
                    </tr>

                    <!-- Empty state -->
                    <tr v-else-if="!rows.length">
                        <td :colspan="columns.length" class="px-6 py-12 text-center text-gray-500">
                            <slot name="empty">
                                {{ emptyText }}
                            </slot>
                        </td>
                    </tr>

                    <!-- Data rows -->
                    <tr
                        v-else
                        v-for="(row, index) in rows"
                        :key="row.id || index"
                        :class="[
                            striped && index % 2 === 1 ? 'bg-gray-50' : '',
                            hoverable ? 'hover:bg-gray-50 cursor-pointer' : '',
                        ]"
                        @click="emit('row-click', row)"
                    >
                        <td
                            v-for="column in columns"
                            :key="column.key"
                            :class="[
                                'px-6 py-4 whitespace-nowrap text-sm',
                                alignmentClass(column.align),
                            ]"
                        >
                            <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
                                <span :class="column.class">{{ row[column.key] }}</span>
                            </slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
