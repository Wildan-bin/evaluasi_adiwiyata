<script setup>
import Card from "@/Components/Card.vue";
import MainLayout from '@/Layouts/MainLayout.vue';
import { Home, BarChart3, Users, Settings } from "lucide-vue-next";

const handleCardClick = (action) => {
    console.log("Card clicked:", action);
};
</script>

<template>
    <MainLayout>
        <h1 class="text-6xl font-semibold p-5">
          Contoh Penggunaan Card
        </h1>
        <h1 class="text-2xl font-light p-5">
            Halaman ini memuat contoh penggunaan komponen cards
        </h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-6">
            <!-- Card Dasar -->
            <Card
                title="Dashboard"
                description="Lihat ringkasan data sistem"
                color="green"
                :icon="Home"
            />

            <!-- Card Clickable -->
            <Card
                title="Laporan"
                description="Buat laporan Greenedu"
                color="emerald"
                :icon="BarChart3"
                :clickable="true"
                link="/reports"
                @action="handleCardClick"
            />

            <!-- Card dengan Custom Content -->
            <Card
                title="Pengguna Aktif"
                description="Kelola pengguna sistem"
                color="teal"
                :icon="Users"
            >
                <div class="text-3xl font-bold text-teal-600">245</div>
                <p class="text-sm text-gray-600 mt-2">
                    +12 pengguna baru minggu ini
                </p>
            </Card>

            <!-- Card dengan Footer -->
            <Card
                title="Pengaturan"
                description="Konfigurasi aplikasi"
                color="green"
                :icon="Settings"
                :clickable="true"
            >
                <template #footer>
                    <div class="flex gap-2 mt-4 pt-4 border-t border-green-200">
                        <button
                            class="flex-1 px-3 py-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium"
                        >
                            Edit
                        </button>
                        <button
                            class="flex-1 px-3 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium"
                        >
                            Reset
                        </button>
                    </div>
                </template>
            </Card>

            <!-- Card Loading State -->
            <Card
                title="Loading Data"
                description="Sedang memproses..."
                color="orange"
                :loading="true"
            />

            <!-- Card Outlined Variant -->
            <Card
                title="Outline Style"
                description="Varian dengan border"
                color="purple"
                variant="outlined"
            />

            <!-- Card Filled Variant -->
            <Card
                title="Filled Style"
                description="Varian dengan background"
                color="red"
                variant="filled"
            />
        </div>
    </MainLayout>
</template>
