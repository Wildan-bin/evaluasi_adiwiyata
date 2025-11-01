<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue'
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <MainLayout>
        <Header
            title="Hasil Evaluasi PPEPP Adiwiyata"
            description="Kelola dan monitor program lingkungan sekolah Anda"
            color="green"
        />
        <h1>Hello</h1>
    </MainLayout>
</template>