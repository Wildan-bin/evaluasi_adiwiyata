<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Leaf, Mail, Send, LogOut as LogOutIcon } from 'lucide-vue-next';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <Head title="Verifikasi Email - Greenedu" />

    <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-emerald-50 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <!-- Background decorative elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-green-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse delay-2000"></div>
        </div>

        <div class="relative w-full max-w-md">
            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header Section with Gradient -->
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-12">
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <Mail class="w-8 h-8 text-white" />
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-white text-center">Verifikasi Email</h1>
                    <p class="text-green-100 text-center text-sm mt-2">Konfirmasi alamat email Anda</p>
                </div>

                <!-- Form Section -->
                <div class="px-8 py-8">
                    <!-- Info Message -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-700">
                            Terima kasih telah mendaftar! Sebelum memulai, apakah Anda dapat memverifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan? Jika Anda tidak menerima email, kami akan dengan senang hati mengirimkan email lagi.
                        </p>
                    </div>

                    <!-- Success Message -->
                    <div
                        v-if="verificationLinkSent"
                        class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg"
                    >
                        <p class="text-sm font-medium text-green-700">
                            Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat mendaftar.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Resend Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-3 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 group"
                        >
                            <Send class="w-5 h-5 group-hover:translate-x-1 transition" />
                            <span v-if="!form.processing">Kirim Ulang Email Verifikasi</span>
                            <span v-else>Mengirim...</span>
                        </button>

                        <!-- Logout Button -->
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 rounded-lg transition duration-200 flex items-center justify-center gap-2 group"
                        >
                            <LogOutIcon class="w-5 h-5" />
                            Keluar
                        </Link>
                    </form>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">atau</span>
                        </div>
                    </div>

                    <!-- Help Text -->
                    <div class="text-center text-sm text-gray-600">
                        <p>Tidak menemukan email verifikasi?</p>
                        <p class="mt-2">Periksa folder spam atau coba minta verifikasi ulang.</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500 text-center">
                        © 2024 Program Greenedu. Semua hak dilindungi.
                    </p>
                </div>
            </div>

            <!-- Bottom Info -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    Kembali ke
                    <Link
                        href="/"
                        class="text-green-600 hover:text-green-700 font-semibold transition"
                    >
                        beranda
                    </Link>
                </p>
            </div>
        </div>
    </div>
</template>
