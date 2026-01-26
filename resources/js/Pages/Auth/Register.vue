<script setup>
// filepath: /home/wildanrobin/Projects/evaluasi_adiwiyata/resources/js/Pages/Auth/Register.vue
import { ref, computed } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { refreshCsrfToken } from '@/Composables/useCsrf';
import { Leaf, Mail, Lock, Eye, EyeOff, UserPlus, User as UserIcon, ArrowLeft } from 'lucide-vue-next';

const showPassword = ref(false);
const showPasswordConfirm = ref(false);

// Get props from backend
const page = usePage();
const roleFromUrl = computed(() => page.props.role || 'user');
const isAdminCreating = computed(() => page.props.isAdminCreating || false);

// Role display name
const roleDisplayName = computed(() => {
    const roleMap = {
        'admin': 'Admin',
        'user': 'User (Sekolah)',
        'mentor': 'Mentor'
    };
    return roleMap[roleFromUrl.value] || 'User';
});

// Role color
const roleColor = computed(() => {
    const colorMap = {
        'admin': 'from-red-500 to-rose-600',
        'user': 'from-green-500 to-emerald-600',
        'mentor': 'from-blue-500 to-indigo-600'
    };
    return colorMap[roleFromUrl.value] || 'from-green-500 to-emerald-600';
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = async () => {
    await refreshCsrfToken();

    // ✅ Gunakan route berbeda berdasarkan siapa yang membuat
    if (isAdminCreating.value) {
        // Admin membuat user baru
        form.post(route('register.store-by-admin', { role: roleFromUrl.value }), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    } else {
        // Guest self-registration
        form.post(route('register'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    }
};

// ✅ Back button destination
const backRoute = computed(() => {
    if (isAdminCreating.value) {
        return route('dashboard');
    }
    return route('login');
});

const backText = computed(() => {
    if (isAdminCreating.value) {
        return 'Kembali ke Dashboard';
    }
    return 'Sudah punya akun? Login';
});
</script>

<template>
    <Head :title="`Daftar ${roleDisplayName} - Greenedu`" />

    <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-emerald-50 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <!-- Background decorative elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-green-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse delay-2000"></div>
        </div>

        <div class="relative w-full max-w-md">
            <!-- Back Button -->
            <div class="mb-4">
                <Link
                    :href="backRoute"
                    class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition"
                >
                    <ArrowLeft class="w-5 h-5" />
                    <span>{{ backText }}</span>
                </Link>
            </div>

            <!-- Main Register Card -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header Section with Gradient -->
                <div :class="`bg-gradient-to-r ${roleColor} px-8 py-12`">
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <Leaf class="w-8 h-8 text-white" />
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-white text-center">Greenedu</h1>
                    <p class="text-white/80 text-center text-sm mt-2">
                        {{ isAdminCreating ? 'Daftarkan Akun Baru' : 'Buat Akun Anda' }}
                    </p>
                    <!-- Role Badge (only show when admin creating) -->
                    <div v-if="isAdminCreating" class="flex justify-center mt-4">
                        <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white font-semibold text-sm">
                            Role: {{ roleDisplayName }}
                        </span>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="px-8 py-8">
                    <form @submit.prevent="submit" class="space-y-5">
                        <!-- Name Field -->
                        <div>
                            <InputLabel for="name" value="Nama Lengkap" />
                            <div class="relative mt-2">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <UserIcon class="w-5 h-5" />
                                </div>
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="pl-10 w-full border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition"
                                    v-model="form.name"
                                    placeholder="Masukkan nama lengkap"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />
                            </div>
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

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
                                    placeholder="Masukkan email"
                                    required
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
                                    placeholder="Buat password yang kuat"
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
                                    placeholder="Konfirmasi password"
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

                        <!-- Register Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            :class="`w-full bg-gradient-to-r ${roleColor} hover:opacity-90 text-white font-semibold py-3 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 group mt-6`"
                        >
                            <UserPlus class="w-5 h-5 group-hover:translate-x-1 transition" />
                            <span v-if="!form.processing">
                                {{ isAdminCreating ? `Daftar ${roleDisplayName}` : 'Daftar Sekarang' }}
                            </span>
                            <span v-else>Memproses...</span>
                        </button>
                    </form>

                    <!-- Link to login (only for guest) -->
                    <div v-if="!isAdminCreating" class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Sudah punya akun?
                            <Link :href="route('login')" class="text-green-600 hover:text-green-700 font-semibold">
                                Login disini
                            </Link>
                        </p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500 text-center">
                        © 2024 Program Greenedu. Semua hak dilindungi.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
