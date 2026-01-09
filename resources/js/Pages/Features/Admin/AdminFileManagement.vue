<template>
    <AuthenticatedLayout
        :user="auth.user"
    >
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola File Upload</h2>
        </template>

        <Head title="Kelola File Upload" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <AdminUserFileSelector />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminUserFileSelector from '@/Components/AdminUserFileSelector.vue';

defineProps({
    auth: {
        type: Object,
        required: true
    }
});
</script>
