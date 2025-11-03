<script setup>
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Leaf, Mail, Lock, CheckCircle, Eye, EyeOff } from 'lucide-vue-next';

const showPassword = ref(false);
const showPasswordConfirm = ref(false);

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset Password - Adiwiyata" />

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
                    <h1 class="text-3xl font-bold text-white text-center">Adiwiyata</h1>
                    <p class="text-green-100 text-center text-sm mt-2">Buat Password Baru Anda</p>
                </div>

                <!-- Form Section -->
                <div class="px-8 py-8">
                    <!-- Info Message -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-700">
                            Masukkan email Anda dan password baru untuk mereset akun Anda.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        <!-- Email Field (Read-only) -->
                        <div>
                            <InputLabel for="email" value="Email" />
                            <div class="relative mt-2">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <Mail class="w-5 h-5" />
                                </div>
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="pl-10 w-full border-2 border-gray-200 rounded-lg bg-gray-50 cursor-not-allowed"
                                    v-model="form.email"
                                    disabled
                                    autocomplete="username"
                                />
                            </div>
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <!-- New Password Field -->
                        <div>
                            <InputLabel for="password" value="Password Baru" />
                            <div class="relative mt-2">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <Lock class="w-5 h-5" />
                                </div>
                                <TextInput
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="pl-10 pr-10 w-full border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition"
                                    v-model="form.password"
                                    placeholder="Masukkan password baru"
                                    required
                                    autocomplete="new-password"
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

                        <!-- Confirm Password Field -->
                        <div>
                            <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                            <div class="relative mt-2">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <Lock class="w-5 h-5" />
                                </div>
                                <TextInput
                                    id="password_confirmation"
                                    :type="showPasswordConfirm ? 'text' : 'password'"
                                    class="pl-10 pr-10 w-full border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition"
                                    v-model="form.password_confirmation"
                                    placeholder="Konfirmasi password baru"
                                    required
                                    autocomplete="new-password"
                                />
                                <button
                                    type="button"
                                    @click="showPasswordConfirm = !showPasswordConfirm"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
                                >
                                    <Eye v-if="!showPasswordConfirm" class="w-5 h-5" />
                                    <EyeOff v-else class="w-5 h-5" />
                                </button>
                            </div>
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <!-- Reset Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-3 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 group mt-6"
                        >
                            <CheckCircle class="w-5 h-5 group-hover:scale-110 transition" />
                            <span v-if="!form.processing">Reset Password</span>
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
