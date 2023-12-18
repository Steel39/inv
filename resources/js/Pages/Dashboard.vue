<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { formToJSON } from 'axios';

defineProps({
    shares: {
        type: Object,
        default: () => "empty stock",
    },
    buyTrades: {
        type: Object,
        default: () => "empty buy"
    },
    sellTrades: {
        type: Object,
        default: () => "empty sell"
    }
});

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>

        <template #header>
            <Link :href="route('dashboard.update')">
                <PrimaryButton>Обновить</PrimaryButton>
            </Link>
        </template>

        <div class="grid grid-cols-2 gap-4 ml-10 mr-10 mt-10">
            <div v-for="(share, figi) in shares">
                <button
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-10 rounded inline-flex items-left">
                    <svg class="fill-current w-4 h-4 mr-2" viewBox="0 0 20 20">
                    </svg>
                    <span>{{ share }}</span>
                    <span class="ml-5 text-green-800">{{ buyTrades[figi] }}</span>
                    <span class="ml-5 text-red-800">{{ sellTrades[figi] }}</span>
                </button>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
