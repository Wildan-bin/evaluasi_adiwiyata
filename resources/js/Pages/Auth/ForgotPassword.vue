<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Leaf, Mail, Send } from 'lucide-vue-next';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Lupa Password - Greenedu" />

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
                            <Leaf class="w-8 h-8 text-white" />
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-white text-center">Greenedu</h1>
                    <p class="text-green-100 text-center text-sm mt-2">Reset Password Anda</p>
                </div>

                <!-- Form Section -->
                <div class="px-8 py-8">
                    <!-- Info Message -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-700">
                            Lupa password Anda? Tidak masalah. Cukup beri tahu kami alamat email Anda dan kami akan mengirimkan tautan reset password ke email Anda.
                        </p>
                    </div>

                    <!-- Status Message -->
                    <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm font-medium text-green-700">{{ status }}</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        <!-- Email Field -->
                        <div>
                            <InputLabel for="email" value="Email" />
                            <div class="relative mt-2">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <Mail class="w-5 h-5" />
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

                        <!-- Send Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-3 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 group mt-6"
                        >
                            <Send class="w-5 h-5 group-hover:translate-x-1 transition" />
                            <span v-if="!form.processing">Kirim Link Reset</span>
                            <span v-else>Mengirim...</span>
                        </button>

                        <!-- Divider -->
                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">atau</span>
                            </div>
                        </div>

                        <!-- Back to Login -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600">
                                <Link
                                    :href="route('login')"
                                    class="text-green-600 hover:text-green-700 font-semibold transition"
                                >
                                    Kembali ke halaman masuk
                                </Link>
                            </p>
                        </div>
                    </form>
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
