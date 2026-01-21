<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    School, 
    User, 
    Mail, 
    Phone, 
    MapPin, 
    Send, 
    MessageCircle,
    Leaf,
    ArrowLeft,
    CheckCircle,
    AlertCircle,
    Building,
    Hash,
    Briefcase
} from 'lucide-vue-next';

// Form data
const form = ref({
    nama_sekolah: '',
    npsn: '',
    jenjang: '',
    alamat: '',
    kabupaten_kota: '',
    nama_pj: '',
    jabatan: '',
    email: '',
    no_whatsapp: ''
});

// Jenjang options
const jenjangOptions = [
    { value: 'SD', label: 'SD - Sekolah Dasar' },
    { value: 'SMP', label: 'SMP - Sekolah Menengah Pertama' },
    { value: 'SMA', label: 'SMA - Sekolah Menengah Atas' },
    { value: 'SMK', label: 'SMK - Sekolah Menengah Kejuruan' }
];

// Validation
const errors = ref({});

const validateForm = () => {
    errors.value = {};
    
    if (!form.value.nama_sekolah.trim()) errors.value.nama_sekolah = 'Nama sekolah wajib diisi';
    if (!form.value.npsn.trim()) errors.value.npsn = 'NPSN wajib diisi';
    if (!form.value.jenjang) errors.value.jenjang = 'Jenjang wajib dipilih';
    if (!form.value.alamat.trim()) errors.value.alamat = 'Alamat wajib diisi';
    if (!form.value.kabupaten_kota.trim()) errors.value.kabupaten_kota = 'Kabupaten/Kota wajib diisi';
    if (!form.value.nama_pj.trim()) errors.value.nama_pj = 'Nama penanggung jawab wajib diisi';
    if (!form.value.jabatan.trim()) errors.value.jabatan = 'Jabatan wajib diisi';
    if (!form.value.email.trim()) {
        errors.value.email = 'Email wajib diisi';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
        errors.value.email = 'Format email tidak valid';
    }
    if (!form.value.no_whatsapp.trim()) {
        errors.value.no_whatsapp = 'No. WhatsApp wajib diisi';
    } else if (!/^(\+62|62|0)8[1-9][0-9]{6,10}$/.test(form.value.no_whatsapp.replace(/\s|-/g, ''))) {
        errors.value.no_whatsapp = 'Format nomor WhatsApp tidak valid';
    }
    
    return Object.keys(errors.value).length === 0;
};

// Generate Request ID
const generateRequestId = () => {
    const timestamp = Date.now();
    return `REQ-${timestamp}`;
};

// Get current date
const getCurrentDate = () => {
    const now = new Date();
    return now.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

// Admin email
const adminEmail = 'admin@greenedu.id'; // Ganti dengan email admin sebenarnya

// Admin WhatsApp
const adminWhatsApp = '6281234567890'; // Ganti dengan nomor WA admin sebenarnya

// Generate Email Template
const generateEmailBody = () => {
    const requestId = generateRequestId();
    const currentDate = getCurrentDate();
    
    return `Kepada Yth.
Admin Greenedu

Dengan hormat,

Kami dari:

📌 INFORMASI SEKOLAH
━━━━━━━━━━━━━━━━━━━━━━━━
Nama Sekolah    : ${form.value.nama_sekolah}
NPSN            : ${form.value.npsn}
Jenjang         : ${form.value.jenjang}
Alamat          : ${form.value.alamat}
Kabupaten/Kota  : ${form.value.kabupaten_kota}

👤 PENANGGUNG JAWAB
━━━━━━━━━━━━━━━━━━━━━━━━
Nama            : ${form.value.nama_pj}
Jabatan         : ${form.value.jabatan}
Email           : ${form.value.email}
No. WhatsApp    : ${form.value.no_whatsapp}

Dengan ini mengajukan permohonan pembuatan akun pada sistem Greenedu untuk keperluan evaluasi program Adiwiyata di sekolah kami.

Kami berkomitmen untuk menggunakan akun ini sesuai dengan ketentuan yang berlaku.

Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.

Hormat kami,
${form.value.nama_pj}
${form.value.nama_sekolah}

━━━━━━━━━━━━━━━━━━━━━━━━
📅 Tanggal Request: ${currentDate}
🔖 Request ID: ${requestId}`;
};

// Generate WhatsApp Template
const generateWhatsAppBody = () => {
    const requestId = generateRequestId();
    
    return `*[REQUEST AKUN GREENEDU]*

Halo Admin Greenedu,

Kami ingin mengajukan permohonan akun:

*📌 DATA SEKOLAH*
• Nama Sekolah: ${form.value.nama_sekolah}
• NPSN: ${form.value.npsn}
• Jenjang: ${form.value.jenjang}
• Alamat: ${form.value.alamat}
• Kab/Kota: ${form.value.kabupaten_kota}

*👤 PENANGGUNG JAWAB*
• Nama: ${form.value.nama_pj}
• Jabatan: ${form.value.jabatan}
• Email: ${form.value.email}
• No. HP: ${form.value.no_whatsapp}

Mohon diproses untuk pembuatan akun.

Terima kasih 🙏

_Request ID: ${requestId}_`;
};

// Send via Email
const sendViaEmail = () => {
    if (!validateForm()) return;
    
    const subject = encodeURIComponent(`[REQUEST AKUN] Permohonan Akun Greenedu - ${form.value.nama_sekolah}`);
    const body = encodeURIComponent(generateEmailBody());
    
    window.open(`mailto:${adminEmail}?subject=${subject}&body=${body}`, '_blank');
    
    showSuccessMessage.value = true;
    successMethod.value = 'email';
};

// Send via WhatsApp
const sendViaWhatsApp = () => {
    if (!validateForm()) return;
    
    const text = encodeURIComponent(generateWhatsAppBody());
    
    window.open(`https://wa.me/${adminWhatsApp}?text=${text}`, '_blank');
    
    showSuccessMessage.value = true;
    successMethod.value = 'whatsapp';
};

// Success state
const showSuccessMessage = ref(false);
const successMethod = ref('');

// Check if form is filled
const isFormFilled = computed(() => {
    return form.value.nama_sekolah && 
           form.value.npsn && 
           form.value.jenjang && 
           form.value.alamat && 
           form.value.kabupaten_kota && 
           form.value.nama_pj && 
           form.value.jabatan && 
           form.value.email && 
           form.value.no_whatsapp;
});
</script>

<template>
    <Head title="Request Akun - Greenedu" />

    <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-emerald-50">
        <!-- Background decorative elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-green-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
        </div>

        <!-- Header -->
        <header class="relative bg-gradient-to-r from-green-600 to-emerald-600 text-white">
            <div class="container mx-auto px-4 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/20 rounded-lg">
                            <Leaf class="w-8 h-8" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Greenedu</h1>
                            <p class="text-green-100 text-sm">Evaluasi Program Adiwiyata</p>
                        </div>
                    </div>
                    <Link 
                        href="/"
                        class="flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Kembali
                    </Link>
                </div>
            </div>
        </header>

        <main class="relative container mx-auto px-4 py-8">
            <div class="max-w-3xl mx-auto">
                
                <!-- Title Section -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <School class="w-8 h-8 text-green-600" />
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Request Akun Sekolah</h2>
                    <p class="text-gray-600">Isi data berikut untuk mengajukan pembuatan akun Greenedu</p>
                </div>

                <!-- Success Message -->
                <transition
                    enter-active-class="transition-all duration-300"
                    enter-from-class="opacity-0 -translate-y-4"
                    enter-to-class="opacity-100 translate-y-0"
                >
                    <div v-if="showSuccessMessage" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                        <div class="flex items-start gap-3">
                            <CheckCircle class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" />
                            <div>
                                <h4 class="font-semibold text-green-800">Request Berhasil Dikirim!</h4>
                                <p class="text-green-700 text-sm mt-1">
                                    <span v-if="successMethod === 'email'">
                                        Aplikasi email Anda telah dibuka dengan template request. 
                                        Silakan kirim email tersebut ke admin.
                                    </span>
                                    <span v-else>
                                        WhatsApp telah dibuka dengan template request. 
                                        Silakan kirim pesan tersebut ke admin.
                                    </span>
                                </p>
                                <p class="text-green-600 text-sm mt-2">
                                    ⏳ Admin akan memproses request dalam 1-3 hari kerja.
                                </p>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- Form Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    
                    <!-- Section: Informasi Sekolah -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <Building class="w-5 h-5 text-green-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Informasi Sekolah</h3>
                                <p class="text-sm text-gray-500">Data identitas sekolah Anda</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nama Sekolah -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Sekolah <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <School class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input
                                        v-model="form.nama_sekolah"
                                        type="text"
                                        class="w-full pl-10 pr-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                        :class="errors.nama_sekolah ? 'border-red-500' : 'border-gray-300'"
                                        placeholder="Contoh: SDN 01 Kota Malang"
                                    />
                                </div>
                                <p v-if="errors.nama_sekolah" class="text-red-500 text-xs mt-1">{{ errors.nama_sekolah }}</p>
                            </div>

                            <!-- NPSN -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    NPSN <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <Hash class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input
                                        v-model="form.npsn"
                                        type="text"
                                        class="w-full pl-10 pr-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                        :class="errors.npsn ? 'border-red-500' : 'border-gray-300'"
                                        placeholder="Contoh: 20512345"
                                    />
                                </div>
                                <p v-if="errors.npsn" class="text-red-500 text-xs mt-1">{{ errors.npsn }}</p>
                            </div>

                            <!-- Jenjang -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Jenjang <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.jenjang"
                                    class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition appearance-none bg-white"
                                    :class="errors.jenjang ? 'border-red-500' : 'border-gray-300'"
                                >
                                    <option value="">-- Pilih Jenjang --</option>
                                    <option v-for="opt in jenjangOptions" :key="opt.value" :value="opt.value">
                                        {{ opt.label }}
                                    </option>
                                </select>
                                <p v-if="errors.jenjang" class="text-red-500 text-xs mt-1">{{ errors.jenjang }}</p>
                            </div>

                            <!-- Alamat -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Alamat Sekolah <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <MapPin class="absolute left-3 top-3 w-5 h-5 text-gray-400" />
                                    <textarea
                                        v-model="form.alamat"
                                        rows="2"
                                        class="w-full pl-10 pr-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition resize-none"
                                        :class="errors.alamat ? 'border-red-500' : 'border-gray-300'"
                                        placeholder="Alamat lengkap sekolah"
                                    ></textarea>
                                </div>
                                <p v-if="errors.alamat" class="text-red-500 text-xs mt-1">{{ errors.alamat }}</p>
                            </div>

                            <!-- Kabupaten/Kota -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kabupaten/Kota <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.kabupaten_kota"
                                    type="text"
                                    class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                    :class="errors.kabupaten_kota ? 'border-red-500' : 'border-gray-300'"
                                    placeholder="Contoh: Kota Malang"
                                />
                                <p v-if="errors.kabupaten_kota" class="text-red-500 text-xs mt-1">{{ errors.kabupaten_kota }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Penanggung Jawab -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <User class="w-5 h-5 text-blue-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Penanggung Jawab</h3>
                                <p class="text-sm text-gray-500">Data kontak yang dapat dihubungi</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nama PJ -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <User class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input
                                        v-model="form.nama_pj"
                                        type="text"
                                        class="w-full pl-10 pr-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                        :class="errors.nama_pj ? 'border-red-500' : 'border-gray-300'"
                                        placeholder="Nama penanggung jawab"
                                    />
                                </div>
                                <p v-if="errors.nama_pj" class="text-red-500 text-xs mt-1">{{ errors.nama_pj }}</p>
                            </div>

                            <!-- Jabatan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Jabatan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <Briefcase class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input
                                        v-model="form.jabatan"
                                        type="text"
                                        class="w-full pl-10 pr-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                        :class="errors.jabatan ? 'border-red-500' : 'border-gray-300'"
                                        placeholder="Contoh: Kepala Sekolah"
                                    />
                                </div>
                                <p v-if="errors.jabatan" class="text-red-500 text-xs mt-1">{{ errors.jabatan }}</p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email Resmi <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <Mail class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="w-full pl-10 pr-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                        :class="errors.email ? 'border-red-500' : 'border-gray-300'"
                                        placeholder="email@sekolah.sch.id"
                                    />
                                </div>
                                <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
                            </div>

                            <!-- No WhatsApp -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    No. WhatsApp <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <Phone class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input
                                        v-model="form.no_whatsapp"
                                        type="tel"
                                        class="w-full pl-10 pr-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                        :class="errors.no_whatsapp ? 'border-red-500' : 'border-gray-300'"
                                        placeholder="08123456789"
                                    />
                                </div>
                                <p v-if="errors.no_whatsapp" class="text-red-500 text-xs mt-1">{{ errors.no_whatsapp }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Kirim Request -->
                    <div class="p-6 bg-gray-50">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <Send class="w-5 h-5 text-purple-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Kirim Request</h3>
                                <p class="text-sm text-gray-500">Pilih metode pengiriman request akun</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Email Button -->
                            <button
                                @click="sendViaEmail"
                                class="flex items-center justify-center gap-3 p-6 bg-white border-2 border-gray-200 rounded-xl hover:border-green-500 hover:bg-green-50 transition group"
                                :class="{ 'opacity-50 cursor-not-allowed': !isFormFilled }"
                            >
                                <div class="p-3 bg-green-100 rounded-full group-hover:bg-green-200 transition">
                                    <Mail class="w-6 h-6 text-green-600" />
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold text-gray-900">Kirim via Email</p>
                                    <p class="text-sm text-gray-500">Buka aplikasi email</p>
                                </div>
                            </button>

                            <!-- WhatsApp Button -->
                            <button
                                @click="sendViaWhatsApp"
                                class="flex items-center justify-center gap-3 p-6 bg-white border-2 border-gray-200 rounded-xl hover:border-green-500 hover:bg-green-50 transition group"
                                :class="{ 'opacity-50 cursor-not-allowed': !isFormFilled }"
                            >
                                <div class="p-3 bg-green-100 rounded-full group-hover:bg-green-200 transition">
                                    <MessageCircle class="w-6 h-6 text-green-600" />
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold text-gray-900">Kirim via WhatsApp</p>
                                    <p class="text-sm text-gray-500">Buka WhatsApp Web/App</p>
                                </div>
                            </button>
                        </div>

                        <!-- Info Box -->
                        <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                            <div class="flex items-start gap-3">
                                <AlertCircle class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
                                <div class="text-sm text-amber-800">
                                    <p class="font-medium">Informasi Penting:</p>
                                    <ul class="mt-1 list-disc list-inside space-y-1 text-amber-700">
                                        <li>Request akan diproses dalam 1-3 hari kerja</li>
                                        <li>Kredensial akun akan dikirim ke email yang didaftarkan</li>
                                        <li>Pastikan data yang diisi sudah benar sebelum mengirim</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        Sudah punya akun? 
                        <Link href="/login" class="text-green-600 hover:text-green-700 font-semibold">
                            Login di sini
                        </Link>
                    </p>
                </div>

            </div>
        </main>
    </div>
</template>

<style scoped>
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}
</style>
