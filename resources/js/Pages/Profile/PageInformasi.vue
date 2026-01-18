<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const showAdditionalPrograms = ref(false);

function toggleProgram() {
    showAdditionalPrograms.value = !showAdditionalPrograms.value;
}
</script>

<template>
    <Head title="Informasi" />
    <div>
        

      <body class="bg-gray-100">

    <!-- Main Content -->
<main class="bg-gray-100">

    <!-- REGISTRATION CTA (enhanced background with leaves + animations) -->
    <section class="relative bg-gradient-to-r from-green-500 to-green-600 text-white py-20 overflow-hidden">

        <!-- floating leaf decorations (more, varied) -->
        <svg class="absolute left-6 top-6 w-20 h-20 leaf leaf-slow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="animation-delay:0s">
            <path d="M2 12c4-6 12-8 18-6-3 4-6 10-10 12-3-2-6-6-8-6z" fill="rgba(255,255,255,0.14)" />
        </svg>

        <svg class="absolute left-24 top-20 w-12 h-12 leaf" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="animation-delay:0.9s; transform: rotate(-12deg);">
            <path d="M2 12c4-6 12-8 18-6-3 4-6 10-10 12-3-2-6-6-8-6z" fill="rgba(255,255,255,0.12)" />
        </svg>

        <svg class="absolute right-8 bottom-8 w-28 h-28 leaf leaf-slower" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="animation-delay:1.6s">
            <path d="M22 12c-4-6-12-8-18-6 3 4 6 10 10 12 3-2 6-6 8-6z" fill="rgba(255,255,255,0.12)" />
        </svg>

        <svg class="absolute right-28 top-14 w-14 h-14 leaf leaf-slow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="animation-delay:0.5s; transform: rotate(18deg);">
            <path d="M2 12c4-6 12-8 18-6-3 4-6 10-10 12-3-2-6-6-8-6z" fill="rgba(255,255,255,0.11)" />
        </svg>

        <svg class="absolute left-10 bottom-24 w-16 h-16 leaf" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="animation-delay:2.1s; transform: rotate(8deg);">
            <path d="M2 12c4-6 12-8 18-6-3 4-6 10-10 12-3-2-6-6-8-6z" fill="rgba(255,255,255,0.10)" />
        </svg>

        <svg class="absolute left-1/2 top-8 w-10 h-10 leaf" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="animation-delay:1.1s; transform: translateX(-50%) rotate(-6deg);">
            <path d="M2 12c4-6 12-8 18-6-3 4-6 10-10 12-3-2-6-6-8-6z" fill="rgba(255,255,255,0.13)" />
        </svg>

        <svg class="absolute right-44 bottom-20 w-8 h-8 leaf leaf-slower" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="animation-delay:0.4s; transform: rotate(22deg);">
            <path d="M2 12c4-6 12-8 18-6-3 4-6 10-10 12-3-2-6-6-8-6z" fill="rgba(255,255,255,0.09)" />
        </svg>

        <!-- top wave (subtle, animated) -->
        <svg class="absolute top-0 left-0 w-full wave-anim" viewBox="0 0 1440 120" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0,32 C144,96 432,0 720,32 C1008,64 1296,96 1440,64 L1440 0 L0 0 Z" fill="#ffffff" opacity="0.06"></path>
        </svg>

        <div class="relative container mx-auto px-6 text-center">
            <div class="max-w-3xl mx-auto bg-white/10 backdrop-blur-md rounded-2xl p-8 md:p-12 pop-in">
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4">Bawa Greenedu ke Sekolah Anda</h2>
                <p class="max-w-3xl mx-auto mb-6 text-lg md:text-xl text-white/95">
                    Tertarik membuat sekolah Anda lebih bersih, hijau, dan berkelanjutan? Daftarkan
                    sekolah Anda sekarang — tim kami siap membantu dengan modul pelatihan, pendampingan,
                    dan penerapan program yang mudah diikuti. Dapatkan konsultasi awal gratis dan dukungan
                    berkelanjutan untuk menjadikan program ini sukses di lingkungan sekolah Anda.
                </p>
                <a href="mailto:info@contoh123.com" aria-label="Hubungi via email"
                   class="inline-block bg-white/95 text-green-700 font-bold py-3 px-8 rounded-full shadow-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                    Hubungi via Email
                </a>
                <p class="mt-4 text-sm opacity-90">Butuh bantuan? Hubungi kami untuk informasi lebih lanjut.</p>
            </div>
        </div>

        <!-- bottom wave (subtle, animated) -->
        <svg class="absolute bottom-0 left-0 w-full wave-anim" viewBox="0 0 1440 120" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0,64 C144,96 432,32 720,64 C1008,96 1296,96 1440,80 L1440 120 L0 120 Z" fill="#ffffff" opacity="0.04"></path>
        </svg>
    </section>

</main>

</body>
    </div>
</template>
<style scoped>
    @keyframes float {
        0% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-12px) rotate(4deg); }
        100% { transform: translateY(0) rotate(0deg); }
    }
    @keyframes slow-sway {
        0% { transform: translateX(0); }
        50% { transform: translateX(-12px); }
        100% { transform: translateX(0); }
    }
    @keyframes pop {
        0% { opacity: 0; transform: translateY(8px) scale(.995); }
        100% { opacity: 1; transform: none; }
    }

    .leaf { animation: float 6s ease-in-out infinite; will-change: transform; }
    .leaf-slow { animation-duration: 9s; }
    .leaf-slower { animation-duration: 12s; }
    .wave-anim { animation: slow-sway 12s linear infinite; will-change: transform; }
    .pop-in { animation: pop 560ms cubic-bezier(.2,.9,.2,1) both; }
</style>