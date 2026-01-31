<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    align: {
        type: String,
        default: 'right', // left, right
    },
    width: {
        type: String,
        default: '48', // 48, 56, 64, etc.
    },
})

const open = ref(false)
const dropdownRef = ref(null)

const toggle = () => {
    open.value = !open.value
}

const close = () => {
    open.value = false
}

const handleClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        close()
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})

const alignmentClasses = {
    left: 'left-0 origin-top-left',
    right: 'right-0 origin-top-right',
}
</script>

<template>
    <div ref="dropdownRef" class="relative">
        <!-- Trigger -->
        <div @click="toggle">
            <slot name="trigger" />
        </div>

        <!-- Dropdown menu -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-show="open"
                :class="[
                    'absolute z-50 mt-2 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5',
                    alignmentClasses[align],
                    `w-${width}`,
                ]"
                :style="{ minWidth: `${width * 4}px` }"
            >
                <div class="py-1" @click="close">
                    <slot />
                </div>
            </div>
        </Transition>
    </div>
</template>
