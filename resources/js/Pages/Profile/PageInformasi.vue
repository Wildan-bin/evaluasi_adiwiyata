<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue'
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const programTambahanVisible = ref(false);

function toggleProgram() {
    programTambahanVisible.value = !programTambahanVisible.value;
}
</script>

<template>
    
    <MainLayout>
        <Head title="Dashboard" />
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <Header
            title="Dashboard Adiwiyata"
            description="Kelola dan monitor program lingkungan sekolah Anda"
            color="green"
        />

      <body class="bg-gray-100">

    <!-- Main Content -->
<main class="bg-gray-100">

    <!-- HERO SECTION -->
    <section class="h-screen bg-cover bg-center flex items-center justify-center text-white"
        style="background-image: url('hero-background.jpg');">
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
    <section class="bg-gray-800 text-white py-12">
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
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <img src="https://images.unsplash.com/photo-1617957743580-0c5a0f9c55c2"
                     class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="font-bold text-xl mb-2">Bank Sampah</h3>
                    <p class="text-gray-600">
                        Menabung sampah anorganik yang memiliki nilai ekonomi.
                    </p>
                     <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                        Download
                    </a>
                </div>
            </div>

            <!-- Program 2 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c"
                     class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="font-bold text-xl mb-2">Komposting</h3>
                    <p class="text-gray-600">
                        Mengolah sampah organik menjadi pupuk alami.
                    </p>
                     <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                        Download
                    </a>
                </div>
            </div>

            <!-- Program 2 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <img src="https://images.unsplash.com/photo-1598514983318-2f64f8f4796c"
                     class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="font-bold text-xl mb-2">Komposting</h3>
                    <p class="text-gray-600">
                        Mengolah sampah organik menjadi pupuk alami.
                    </p>
                     <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                        Download
                    </a>
                </div>
            </div>

            <!-- Program 3 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <img src="https://images.unsplash.com/photo-1567699333395-a03c1fe3a395"
                     class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="font-bold text-xl mb-2">3-Daur Ulang</h3>
                    <p class="text-gray-600">
                        Mengubah sampah menjadi produk baru yang bermanfaat.
                    </p>
                     <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                        Download
                    </a>
                </div>
            </div>

        </div>

        <!-- BUTTON -->
        <div class="text-center mt-12">
            <button @click="toggleProgram"
                class="bg-green-600 text-white font-bold rounded-full py-4 px-10 hover:bg-green-700 transition">
                Lihat Semua Program
            </button>
        </div>

        <!-- PROGRAM TAMBAHAN (HIDDEN) -->
        <div v-show="programTambahanVisible"
             class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- 1 -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-bold text-xl mb-2">Eco Brick</h3>
                <p class="text-gray-600">
                    Pemanfaatan plastik bekas menjadi bahan bangunan ramah lingkungan.
                </p>
                 <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                        Download
                </a>
            </div>

            <!-- 2 -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-bold text-xl mb-2">Pemilahan Sampah</h3>
                <p class="text-gray-600">
                    Edukasi pemilahan sampah sejak dari sumbernya.
                </p>
                 <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                        Download
                </a>
            </div>

            <!-- 3 -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-bold text-xl mb-2">Green Class</h3>
                <p class="text-gray-600">
                    Program kelas peduli lingkungan berbasis praktik langsung.
                </p>
                 <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                        Download
                </a>
            </div>

            <!-- 4 -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-bold text-xl mb-2">Hari Bebas Sampah</h3>
                <p class="text-gray-600">
                    Kampanye pengurangan sampah sekali pakai di sekolah.
                </p>
                 <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                        Download
                </a>
            </div>

            <!-- 5 -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-bold text-xl mb-2">Kantin Hijau</h3>
                <p class="text-gray-600">
                    Pengurangan plastik di area kantin sekolah.
                </p>
                 <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                        Download
                </a>
            </div>

            <!-- 6 -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-bold text-xl mb-2">Workshop Daur Ulang</h3>
                <p class="text-gray-600">
                    Pelatihan pengolahan sampah kreatif bagi siswa.
                </p>
                 <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                        Download
                </a>
            </div>

            <!-- 7 -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-bold text-xl mb-2">Monitoring Sampah</h3>
                <p class="text-gray-600">
                    Pencatatan jumlah dan jenis sampah sekolah.
                </p>
                <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                            Download
                </a>
            </div>

            <!-- 8 -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-bold text-xl mb-2">Green Ambassador</h3>
                <p class="text-gray-600">
                    Duta lingkungan dari kalangan siswa.
                </p>
                 <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                        Download
                </a>
            </div>

            <!-- 9 -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-bold text-xl mb-2">Taman Kompos</h3>
                <p class="text-gray-600">
                    Pemanfaatan kompos untuk penghijauan sekolah.
                </p>
                 <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                        Download
                </a>
            </div>

            <!-- 10 -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-bold text-xl mb-2">Lomba Lingkungan</h3>
                <p class="text-gray-600">
                    Kompetisi kreatif bertema kebersihan dan lingkungan.
                </p>
                 <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                        Download
                </a>
            </div>

            <!-- 11 -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-bold text-xl mb-2">No Plastic in School</h3>
                <p class="text-gray-600">
                    Program kreatif bertema menghilangkan plastik di sekolah.
                </p>
                 <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                        Download
                </a>
            </div>

            <!-- 12 -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-bold text-xl mb-2">Lomba Lingkungan</h3>
                <p class="text-gray-600">
                    Kompetisi kreatif bertema kebersihan dan lingkungan.
                </p>
                 <a href="#" download class="mt-4 inline-block bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                        Download
                </a>
            </div>

        </div>
    </div>
</section>

</main>

</body>
    </MainLayout>
</template>