<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue'
import { Head } from '@inertiajs/vue3';

// (activity add/remove handlers intentionally removed to avoid runtime errors)
</script>

<template>
    
    <MainLayout>
        <Head title="Dashboard" />
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <Header
            title="Dashboard Adiwiyata"
            description="Kelola dan monitor program lingkungan sekolah Anda"
            color="green"
        />

      <body class="bg-gray-100">

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Welcome Section - Text Left, Image Right -->
        <section class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center gap-8">
                <div class="md:w-1/2">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Selamat Datang di PPEPP</h2>
                    <p class="text-gray-600">
                        Program Penilaian dan Evaluasi Peringkat PBLHS (PPEPP) adalah sistem evaluasi komprehensif untuk mengukur
                        dan meningkatkan kualitas penerapan program lingkungan hidup di sekolah-sekolah Indonesia.
                    </p>
                </div>
                <div class="md:w-1/2">
                    <img src="path/to/image1.jpg" alt="PPEPP Program" class="rounded-lg shadow-md w-full h-64 object-cover">
                </div>
            </div>
        </section>

        <!-- Welcome Section - Image Left, Text Right -->
        <section class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-col-reverse md:flex-row md:items-center gap-8">
                <div class="md:w-1/2">
                    <img src="path/to/image2.jpg" alt="School Environment" class="rounded-lg shadow-md w-full h-64 object-cover">
                </div>
                <div class="md:w-1/2">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Komitmen Lingkungan</h2>
                    <p class="text-gray-600">
                        Kami berkomitmen untuk mendukung sekolah-sekolah dalam mengembangkan program lingkungan hidup yang
                        berkelanjutan. Melalui PPEPP, sekolah dapat mengevaluasi dan meningkatkan kualitas program mereka
                        secara sistematis.
                    </p>
                </div>
            </div>
        </section>

        <!-- Program Overview -->
        <section class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Tentang Program</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 border rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Evaluasi</h3>
                    <p class="text-gray-600">Penilaian komprehensif terhadap implementasi program lingkungan hidup di sekolah</p>
                </div>
                <div class="p-4 border rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Peringkat</h3>
                    <p class="text-gray-600">Penentuan peringkat berdasarkan kriteria dan standar yang telah ditetapkan</p>
                </div>
                <div class="p-4 border rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Pengembangan</h3>
                    <p class="text-gray-600">Rekomendasi untuk pengembangan dan peningkatan kualitas program</p>
                </div>
            </div>
        </section>

        <!-- Action Button -->
        <div class="text-center">
            <a href="administrasi_sekolah.html" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition duration-300">
                Ajukan PPEPP Sekarang
            </a>
        </div>
    </main>
</body>
    </MainLayout>
</template>
