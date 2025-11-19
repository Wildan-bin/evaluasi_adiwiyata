<script setup>
import Header from "@/Components/Header.vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import {
    CheckCircle,
    BarChart3,
    Home,
    FileText,
    Eye,
    Settings,
} from "lucide-vue-next";

const breadcrumbs = [
    { name: "Dashboard", href: "/" },
    { name: "Evaluasi", href: "/evaluasi" },
    { name: "Detail Evaluasi", href: null },
];
</script>

<template>
    <MainLayout class="pb-10">
        <!-- Header Dasar -->
        <h1 class="text-2xl semi-bold italic p-6 py-10 text-gray-500">
            Header Dasar
        </h1>

        <Header
            title="Dashboard Greenedu"
            description="Kelola dan monitor program lingkungan sekolah Anda"
            :icon="BarChart3"
            color="green"
        />

        <!-- Header dengan Badge -->
        <h1 class="text-2xl semi-bold italic p-6 py-10 text-gray-500">
            Header dengan Badge
        </h1>
        <Header
            title="Evaluasi Program Greenedu"
            description="Pantau perkembangan program lingkungan dan keberlanjutan sekolah"
            subtitle="Tahun Akademik 2024/2025"
            :icon="CheckCircle"
            color="emerald"
            size="lg"
            badge="Aktif"
            badgeColor="green"
        />

        <!-- Header dengan Custom Action Button -->
        <h1 class="text-2xl semi-bold italic p-6 py-10 text-gray-500">
            Header dengan Custom Action Button
        </h1>
        <Header
            title="Laporan Greenedu"
            description="Lihat laporan komprehensif program lingkungan sekolah"
            :icon="FileText"
            color="teal"
            size="md"
        >
            <!-- Slot untuk custom content di sebelah kanan -->
            <div class="flex gap-2">
                <button
                    class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors text-sm font-medium"
                >
                    📥 Export PDF
                </button>
                <button
                    class="px-4 py-2 bg-white text-teal-600 border border-teal-200 rounded-lg hover:bg-teal-50 transition-colors text-sm font-medium"
                >
                    🔄 Refresh
                </button>
            </div>
        </Header>

        <!-- Header dengan Breadcrumb -->
        <h1 class="text-2xl semi-bold italic p-6 py-10 text-gray-500">
            Header dengan Breadcrumb
        </h1>
        <Header
            title="Detail Evaluasi Program"
            description="Review lengkap hasil evaluasi lingkungan sekolah"
            :icon="Eye"
            color="blue"
            variant="minimal"
            :showBreadcrumb="true"
            :breadcrumbs="breadcrumbs"
        />

        <!-- Header dengan Gradient Background -->
        <h1 class="text-2xl semi-bold italic p-6 py-10 text-gray-500">
            Header dengan Gradient Background
        </h1>
        <Header
            title="Pengaturan Sistem"
            description="Konfigurasi dan kelola setting aplikasi Greenedu"
            :icon="Settings"
            color="purple"
            size="lg"
            variant="gradient"
            badge="Beta"
            badgeColor="purple"
        />

        <div class="p-6">
            <!-- Konten page -->
            <h1 class="text-center italic">Ini adalah bagian konten halaman dengan berbagai header yang telah ditambahkan.</h1>
        </div>
    </MainLayout>
</template>
