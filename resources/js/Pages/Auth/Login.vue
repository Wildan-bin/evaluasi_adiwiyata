<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Login - Greenedu" />

    <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-emerald-50 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <!-- Background decorative elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-green-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s"></div>
        </div>

        <div class="relative w-full max-w-md">
            <!-- Main Login Card -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header Section with Gradient -->
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-12">
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            🌿
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-white text-center">Greenedu</h1>
                    <p class="text-green-100 text-center text-sm mt-2">Masuk ke Akun Anda</p>
                </div>

                <!-- Form Section -->
                <div class="px-8 py-8">
                    <!-- Status Message -->
                    <div v-if="status" class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded">
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Email Field -->
                        <div>
                            <InputLabel for="email" value="Email" />
                            <div class="relative mt-2">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    ✉️
                                </div>
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="pl-10 w-full border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition"
                                    v-model="form.email"
                                    placeholder="Masukkan email Anda"
                                    required
                                    autofocus
                                    autocomplete="username"
                                />
                            </div>
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <!-- Submit Button -->
                        <PrimaryButton
                            class="w-full justify-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Sedang masuk...' : 'Masuk' }}
                        </PrimaryButton>

                        <!-- Divider 
                        <div class="text-center">
                            <p class="text-gray-600 text-sm">
                                Belum punya akun?
                                <Link :href="route('register')" class="text-green-600 hover:text-green-700 font-semibold underline">
                                    Daftar di sini
                                </Link>
                            </p>
                        </div> -->
                    </form>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 text-center text-xs text-gray-500">
                    © 2026 Greenedu UM. Semua hak cipta dilindungi.
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    @keyframes pulse {
        0%, 100% {
            opacity: 0.2;
        }
        50% {
            opacity: 0.3;
        }
    }

    .animate-pulse {
        animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>