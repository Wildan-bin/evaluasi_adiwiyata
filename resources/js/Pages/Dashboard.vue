<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-green-50 to-teal-50">
        <!-- Header dengan Login & Register Button -->
        <div class="absolute top-0 right-0 p-6 z-50">
            <div class="flex gap-4">
                <Link
                    v-if="canLogin"
                    href="/login"
                    class="px-4 py-2 text-gray-600 hover:text-gray-900 font-medium"
                >
                    Log in
                </Link>
                <Link
                    v-if="canRegister"
                    href="/register"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium"
                >
                    Register
                </Link>
            </div>
        </div>

        <!-- Main Content -->
        <main class="container mx-auto px-4 pt-24 pb-8">
            <!-- Header Section -->
            <div class="mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    Dashboard Adiwiyata
                </h1>
                <p class="text-gray-600 text-lg">
                    Kelola dan monitor program lingkungan sekolah Anda
                </p>
            </div>

            <!-- Welcome Card -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">
                    Selamat Datang di Evaluasi Adiwiyata
                </h2>
                <p class="text-gray-600 mb-8 text-lg">
                    Platform evaluasi program lingkungan berkelanjutan untuk sekolah
                </p>

                <div class="flex gap-4 justify-center">
                    <Link
                        v-if="canLogin"
                        href="/login"
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition"
                    >
                        Masuk Sekarang
                    </Link>
                    <Link
                        v-if="canRegister"
                        href="/register"
                        class="px-6 py-3 border-2 border-green-600 text-green-600 rounded-lg hover:bg-green-50 font-semibold transition"
                    >
                        Daftar Baru
                    </Link>
                </div>
            </div>

            <!-- Features Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                    <div class="text-4xl mb-3">📋</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Evaluasi Komprehensif</h3>
                    <p class="text-gray-600">Evaluasi menyeluruh program lingkungan sekolah Anda</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                    <div class="text-4xl mb-3">📊</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Analisis Data</h3>
                    <p class="text-gray-600">Lihat hasil evaluasi dan rekomendasi perbaikan</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                    <div class="text-4xl mb-3">🌱</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Program Berkelanjutan</h3>
                    <p class="text-gray-600">Kelola program lingkungan berkelanjutan di sekolah</p>
                </div>
            </div>
        </main>
    </div>
</template>
