<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Header from '@/Components/Header.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const showAdditionalPrograms = ref(false);

function toggleProgram() {
    showAdditionalPrograms.value = !showAdditionalPrograms.value;
}
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
                    href="/informasi"
                    class="bg-gradient-to-r from-green-500 to-emerald-600 px-4 py-2 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 font-medium transition"
                >
                    Informasi
                </Link>
            </div>
        </div>

        <!-- HERO SECTION -->
        <section class="bg-cover bg-center flex items-center justify-center text-white relative py-20"
            style="background-image: url('https://images.unsplash.com/photo-1506784983877-45594efa4c85?q=80&w=2070&auto=format&fit=crop');">
            <div class="text-center px-6">
                <h1 class="text-6xl font-extrabold text-green-500">Greenedu UM</h1>
                <p class="max-w-2xl mx-auto mt-6 text-lg">
                    Platform edukasi lingkungan yang mendukung pengelolaan sampah berkelanjutan di sekolah.
                    Bersama kita wujudkan sekolah yang bersih, hijau, dan peduli masa depan.
                </p>
                <div class="mt-8">
                    <button class="bg-white text-gray-800 font-bold rounded-full py-4 px-8">
                        Pelajari Pengolahan Sampah
                    </button>
                    <button class="bg-transparent border-2 border-white font-bold rounded-full py-4 px-8 ml-4">
                        Lihat Program Sekolah
                    </button>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <main class="container mx-auto px-4 pt-12 pb-8">
            <!-- Welcome Card -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">
                    Selamat Datang di Evaluasi Greenedu
                </h2>
                <p class="text-gray-600 mb-8 text-lg">
                    Platform evaluasi program lingkungan berkelanjutan untuk sekolah
                </p>

                <div class="flex gap-4 justify-center">
                    <Link
                        v-if="canLogin"
                        href="/login"
                        class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 font-medium transition"
                    >
                        Masuk Sekarang
                    </Link>
                    <Link
                        v-if="canRegister"
                        href="/informasi"
                        class="px-6 py-3 border-2 border-green-600 text-green-600 rounded-lg hover:bg-green-50 font-semibold transition"
                    >
                        Daftar Baru
                    </Link>
                </div>
            </div>

            <!-- WHY GREENEDU -->
            <section class="py-20">
                <div class="container mx-auto px-6 text-center">
                    <h2 class="text-4xl font-bold mb-4 text-gray-800">
                        Mengapa Greenedu UM Penting?
                    </h2>
                    <p class="max-w-2xl mx-auto mb-12 text-gray-600">
                        Greenedu UM hadir sebagai solusi edukatif untuk membantu sekolah mengelola sampah
                        secara bijak melalui pembelajaran, praktik langsung, dan aksi nyata.
                    </p>

                    <div class="flex flex-wrap justify-center gap-8">
                        <div class="w-full md:w-1/3 p-6 bg-white rounded-lg shadow-lg">
                            <h3 class="text-2xl font-bold mb-2">Pemilahan Sampah</h3>
                            <p class="text-gray-600">
                                Mengajarkan siswa untuk memisahkan sampah organik, anorganik,
                                dan B3 sejak dari sumbernya.
                            </p>
                        </div>

                        <div class="w-full md:w-1/3 p-6 bg-white rounded-lg shadow-lg">
                            <h3 class="text-2xl font-bold mb-2">Daur Ulang & Kompos</h3>
                            <p class="text-gray-600">
                                Sampah diolah kembali menjadi produk bermanfaat seperti kompos,
                                ecobrick, dan kerajinan ramah lingkungan.
                            </p>
                        </div>

                        <div class="w-full md:w-1/3 p-6 bg-white rounded-lg shadow-lg">
                            <h3 class="text-2xl font-bold mb-2">Partisipasi Aktif</h3>
                            <p class="text-gray-600">
                                Melibatkan siswa, guru, dan seluruh warga sekolah dalam
                                membangun budaya peduli lingkungan.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- IMPACT -->
            <section class="bg-gray-800 text-white py-12 mb-12">
                <div class="container mx-auto px-6 flex flex-wrap justify-around text-center gap-8">
                    <div>
                        <p class="text-4xl font-bold">500+</p>
                        <p>Siswa Terlibat Aktif</p>
                    </div>
                    <div>
                        <p class="text-4xl font-bold">1 Ton+</p>
                        <p>Sampah Terolah</p>
                    </div>
                    <div>
                        <p class="text-4xl font-bold">10+</p>
                        <p>Program Lingkungan</p>
                    </div>
                </div>
            </section>

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

            <!-- JENIS SAMPAH -->
            <section class="py-20">
                <div class="container mx-auto px-6">
                    <h2 class="text-4xl font-bold text-center text-gray-800 mb-10">
                        Jenis Sampah di Lingkungan Sekolah
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1581578731548-c64695cc6952"
                                class="w-full h-64 object-cover">
                            <div class="p-6">
                                <h3 class="font-bold text-xl mb-2">Sampah Organik</h3>
                                <p class="text-gray-600 text-sm">
                                    Sisa makanan & daun, diolah menjadi kompos.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1604187351574-c75ca79f5807"
                                class="w-full h-64 object-cover">
                            <div class="p-6">
                                <h3 class="font-bold text-xl mb-2">Sampah Anorganik</h3>
                                <p class="text-gray-600 text-sm">
                                    Plastik, botol, dan kaleng untuk daur ulang.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1589923188651-268a9765e432"
                                class="w-full h-64 object-cover">
                            <div class="p-6">
                                <h3 class="font-bold text-xl mb-2">Sampah Kertas</h3>
                                <p class="text-gray-600 text-sm">
                                    Kertas bekas yang dapat digunakan kembali.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1582407947304-fd86f028f716"
                                class="w-full h-64 object-cover">
                            <div class="p-6">
                                <h3 class="font-bold text-xl mb-2">Sampah B3</h3>
                                <p class="text-gray-600 text-sm">
                                    Baterai & lampu, perlu penanganan khusus.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <!-- PROGRAM GREENEDU UM -->
            <section class="py-20 bg-gray-200">
                <div class="container mx-auto px-6">

                    <!-- HEADER -->
                    <div class="text-center mb-12">
                        <h2 class="text-4xl font-bold text-gray-800 mb-4">
                            Program Greenedu UM
                        </h2>
                        <p class="text-gray-600 max-w-2xl mx-auto">
                            Berbagai program pengelolaan sampah yang mudah diterapkan
                            untuk mendukung sekolah yang bersih dan berkelanjutan.
                        </p>
                    </div>

                    <!-- PROGRAM UTAMA -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                        <!-- Program 1 -->
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                            <img src="https://images.unsplash.com/photo-1617957743580-0c5a0f9c55c2"
                                class="w-full h-48 object-cover">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">Bank Sampah</h3>
                                <p class="text-gray-600 text-sm flex-grow">
                                    Menabung sampah anorganik yang memiliki nilai ekonomi.
                                </p>
                                <a href="#" download class="mt-6 inline-block bg-green-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-green-700 transition duration-300 w-full text-center">
                                    📥 Download
                                </a>
                            </div>
                        </div>

                        <!-- Program 2 -->
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                            <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c"
                                class="w-full h-48 object-cover">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">Komposting</h3>
                                <p class="text-gray-600 text-sm flex-grow">
                                    Mengolah sampah organik menjadi pupuk alami.
                                </p>
                                <a href="#" download class="mt-6 inline-block bg-green-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-green-700 transition duration-300 w-full text-center">
                                    📥 Download
                                </a>
                            </div>
                        </div>

                        <!-- Program 3 -->
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                            <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c"
                                class="w-full h-48 object-cover">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">Ecobrick</h3>
                                <p class="text-gray-600 text-sm flex-grow">
                                    Pemanfaatan plastik bekas menjadi bahan bangunan ramah lingkungan.
                                </p>
                                <a href="#" download class="mt-6 inline-block bg-green-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-green-700 transition duration-300 w-full text-center">
                                    📥 Download
                                </a>
                            </div>
                        </div>

                    </div>

                    <!-- TOMBOL LIHAT LAINNYA -->
                    <div class="text-center mt-12">
                        <button @click="toggleProgram"
                            class="bg-green-800 text-white font-semibold py-3 px-8 rounded-md hover:bg-green-900 transition">
                            {{ showAdditionalPrograms ? 'Sembunyikan' : 'Lihat Lainnya' }}
                        </button>
                    </div>

                    <!-- PROGRAM TAMBAHAN (JIKA DITAMPILKAN) -->
                    <div v-if="showAdditionalPrograms" class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                        <!-- Program 4 -->
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                            <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c"
                                class="w-full h-48 object-cover">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">Daur Ulang Kreatif</h3>
                                <p class="text-gray-600 text-sm flex-grow">
                                    Mengubah sampah menjadi karya seni atau produk berguna.
                                </p>
                                <a href="#" download class="mt-6 inline-block bg-green-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-green-700 transition duration-300 w-full text-center">
                                    📥 Download
                                </a>
                            </div>
                        </div>

                        <!-- Program 5 -->
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                            <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c"
                                class="w-full h-48 object-cover">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">Jelajah Alam</h3>
                                <p class="text-gray-600 text-sm flex-grow">
                                    Program edukasi di luar ruangan untuk mengenal lingkungan.
                                </p>
                                <a href="#" download class="mt-6 inline-block bg-green-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-green-700 transition duration-300 w-full text-center">
                                    📥 Download
                                </a>
                            </div>
                        </div>

                        <!-- Program 6 -->
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                            <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c"
                                class="w-full h-48 object-cover">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">Duta Lingkungan</h3>
                                <p class="text-gray-600 text-sm flex-grow">
                                    Membentuk siswa sebagai agen perubahan di sekolah.
                                </p>
                                <a href="#" download class="mt-6 inline-block bg-green-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-green-700 transition duration-300 w-full text-center">
                                    📥 Download
                                </a>
                            </div>
                        </div>

                                                <!-- 4 -->
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                            <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c" class="w-full h-48 object-cover">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">Hari Bebas Sampah</h3>
                                <p class="text-gray-600 text-sm flex-grow">Kampanye pengurangan sampah sekali pakai di sekolah.</p>
                                <a href="#" download class="mt-6 inline-block bg-green-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-green-700 transition duration-300 w-full text-center">📥 Download</a>
                            </div>
                        </div>

                        <!-- 5 -->
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                            <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c" class="w-full h-48 object-cover">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">Kantin Hijau</h3>
                                <p class="text-gray-600 text-sm flex-grow">Pengurangan plastik di area kantin sekolah.</p>
                                <a href="#" download class="mt-6 inline-block bg-green-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-green-700 transition duration-300 w-full text-center">📥 Download</a>
                            </div>
                        </div>

                        <!-- 6 -->
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                            <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c" class="w-full h-48 object-cover">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">Workshop Daur Ulang</h3>
                                <p class="text-gray-600 text-sm flex-grow">Pelatihan pengolahan sampah kreatif bagi siswa.</p>
                                <a href="#" download class="mt-6 inline-block bg-green-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-green-700 transition duration-300 w-full text-center">📥 Download</a>
                            </div>
                        </div>

                        <!-- 7 -->
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                            <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c" class="w-full h-48 object-cover">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">Monitoring Sampah</h3>
                                <p class="text-gray-600 text-sm flex-grow">Pencatatan jumlah dan jenis sampah sekolah.</p>
                                <a href="#" download class="mt-6 inline-block bg-green-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-green-700 transition duration-300 w-full text-center">📥 Download</a>
                            </div>
                        </div>

                        <!-- 8 -->
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                            <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c" class="w-full h-48 object-cover">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">Green Ambassador</h3>
                                <p class="text-gray-600 text-sm flex-grow">Duta lingkungan dari kalangan siswa.</p>
                                <a href="#" download class="mt-6 inline-block bg-green-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-green-700 transition duration-300 w-full text-center">📥 Download</a>
                            </div>
                        </div>

                        <!-- 9 -->
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                            <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c" class="w-full h-48 object-cover">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">Taman Kompos</h3>
                                <p class="text-gray-600 text-sm flex-grow">Pemanfaatan kompos untuk penghijauan sekolah.</p>
                                <a href="#" download class="mt-6 inline-block bg-green-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-green-700 transition duration-300 w-full text-center">📥 Download</a>
                            </div>
                        </div>

                        <!-- 10 -->
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                            <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c" class="w-full h-48 object-cover">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">Lomba Lingkungan</h3>
                                <p class="text-gray-600 text-sm flex-grow">Kompetisi kreatif bertema kebersihan dan lingkungan.</p>
                                <a href="#" download class="mt-6 inline-block bg-green-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-green-700 transition duration-300 w-full text-center">📥 Download</a>
                            </div>
                        </div>

                        <!-- 11 -->
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                            <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c" class="w-full h-48 object-cover">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">No Plastic in School</h3>
                                <p class="text-gray-600 text-sm flex-grow">Program kreatif bertema menghilangkan plastik di sekolah.</p>
                                <a href="#" download class="mt-6 inline-block bg-green-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-green-700 transition duration-300 w-full text-center">📥 Download</a>
                            </div>
                        </div>

                        <!-- 12 -->
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                            <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c" class="w-full h-48 object-cover">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">Lomba Lingkungan</h3>
                                <p class="text-gray-600 text-sm flex-grow">Kompetisi kreatif bertema kebersihan dan lingkungan.</p>
                                <a href="#" download class="mt-6 inline-block bg-green-600 text-white font-semibold py-2 px-6 rounded-md hover:bg-green-700 transition duration-300 w-full text-center">📥 Download</a>
                            </div>
                        </div>

                    </div>

                </div>
            </section>
        </main>
    </div>
</template>

<style>
.pop-in {
    animation: pop-in 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards;
}

@keyframes pop-in {
    0% {
        opacity: 0;
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}
</style>