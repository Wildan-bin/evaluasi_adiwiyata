<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { User, Lock, Trash2 } from 'lucide-vue-next';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
</script>

<template>
    <Head title="Profil Saya" />

    <MainLayout>
        <Header
            title="Pengaturan Profil"
            description="Kelola informasi akun dan keamanan Anda"
            color="green"
        />

        <main class="container mx-auto px-4 pt-4 pb-8">
            <div class="max-w-4xl mx-auto space-y-6">
                
                <!-- User Info Card -->
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                            <User class="w-8 h-8 text-white" />
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">{{ user.name }}</h2>
                            <p class="text-green-100">{{ user.email }}</p>
                            <span class="inline-block mt-2 px-3 py-1 bg-white/20 rounded-full text-sm capitalize">
                                {{ user.role || 'User' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <User class="w-5 h-5 text-green-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Informasi Profil</h3>
                                <p class="text-sm text-gray-500">Perbarui nama dan email akun Anda</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </div>
                </div>

                <!-- Update Password -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <Lock class="w-5 h-5 text-blue-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Ubah Password</h3>
                                <p class="text-sm text-gray-500">Pastikan akun Anda menggunakan password yang aman</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden">
                    <div class="border-b border-red-200 bg-red-50 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-red-100 rounded-lg">
                                <Trash2 class="w-5 h-5 text-red-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-red-900">Hapus Akun</h3>
                                <p class="text-sm text-red-600">Tindakan ini tidak dapat dibatalkan</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>

            </div>
        </main>
    </MainLayout>
</template>
