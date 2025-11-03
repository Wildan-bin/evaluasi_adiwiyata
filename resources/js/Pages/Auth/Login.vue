<script setup>
import { ref } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Leaf, Mail, Lock, LogIn as LoginIcon, Eye, EyeOff } from 'lucide-vue-next';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk - Adiwiyata" />

    <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-emerald-50 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <!-- Background decorative elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-green-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse delay-2000"></div>
        </div>

        <div class="relative w-full max-w-md">
            <!-- Main Login Card -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header Section with Gradient -->
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-12">
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <Leaf class="w-8 h-8 text-white" />
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-white text-center">Adiwiyata</h1>
                    <p class="text-green-100 text-center text-sm mt-2">Program Evaluasi Lingkungan Sekolah</p>
                </div>

                <!-- Form Section -->
                <div class="px-8 py-8">
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

                        <!-- Password Field -->
                        <div>
                            <InputLabel for="password" value="Password" />
                            <div class="relative mt-2">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <Lock class="w-5 h-5" />
                                </div>
                                <TextInput
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="pl-10 pr-10 w-full border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition"
                                    v-model="form.password"
                                    placeholder="Masukkan password Anda"
                                    required
                                    autocomplete="current-password"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
                                >
                                    <Eye v-if="!showPassword" class="w-5 h-5" />
                                    <EyeOff v-else class="w-5 h-5" />
                                </button>
                            </div>
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <!-- Remember Me Checkbox -->
                        <div class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <label class="ms-3 text-sm text-gray-600 cursor-pointer hover:text-gray-900 transition">
                                Ingat saya
                            </label>
                        </div>

                        <!-- Login Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-3 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 group"
                        >
                            <LoginIcon class="w-5 h-5 group-hover:translate-x-1 transition" />
                            <span v-if="!form.processing">Masuk</span>
                            <span v-else>Memproses...</span>
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

                        <!-- Footer Links -->
                        <div class="space-y-3 text-center">
                            <!-- Forgot Password -->
                            <div v-if="canResetPassword">
                                <Link
                                    :href="route('password.request')"
                                    class="text-sm text-green-600 hover:text-green-700 font-medium transition"
                                >
                                    Lupa password?
                                </Link>
                            </div>

                            <!-- Register Link -->
                            <div class="pt-2">
                                <p class="text-sm text-gray-600">
                                    Belum memiliki akun?
                                    <Link
                                        :href="route('register')"
                                        class="text-green-600 hover:text-green-700 font-semibold transition"
                                    >
                                        Daftar di sini
                                    </Link>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500 text-center">
                        © 2024 Program Adiwiyata. Semua hak dilindungi.
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
