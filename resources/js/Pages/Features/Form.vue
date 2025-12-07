<script setup>
import { ref } from 'vue';
import Card from "@/Components/Card.vue";
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import { Home, BarChart3, Users, Settings, ChevronRight, Save, AlertCircle } from "lucide-vue-next";

const handleCardClick = (action) => {
    console.log("Card clicked:", action);
};
</script>

<template>
    <MainLayout>
        <Header
            title="Form Evaluasi Adiwiyata"
            description="Kelola dan monitor program lingkungan sekolah Anda"
            color="green"
        />

        <div class="container">
            <h1>Pengisian Kuisioner PPEPP Adiwiyata</h1>
        </div>
    </MainLayout>
</template>
