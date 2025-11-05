<script setup>
import { ref } from 'vue';
import Card from "@/Components/Card.vue";
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import { Home, BarChart3, Users, Settings, ChevronRight, Save, AlertCircle } from "lucide-vue-next";

// State untuk tabs
const activeTab = ref('penetapan');

// State untuk form responses - simpan jawaban setiap tab
const formResponses = ref({
  penetapan: {
    q1: null,
    q2: null,
    q3: null,
    q4: null,
    q5: null,
  },
  pelaksanaan: {
    q1: null,
    q2: null,
    q3: null,
    q4: null,
    q5: null,
  },
  evaluasi: {
    q1: null,
    q2: null,
    q3: null,
    q4: null,
    q5: null,
  },
  pengendalian: {
    q1: null,
    q2: null,
    q3: null,
    q4: null,
    q5: null,
  },
  peningkatan: {
    q1: null,
    q2: null,
    q3: null,
    q4: null,
    q5: null,
  }
});

// State untuk tracking form completion
const isSubmitting = ref(false);
const submitMessage = ref('');

// Definisi tabs
const tabs = [
  { id: 'penetapan', label: 'Penetapan' },
  { id: 'pelaksanaan', label: 'Pelaksanaan' },
  { id: 'evaluasi', label: 'Evaluasi' },
  { id: 'pengendalian', label: 'Pengendalian' },
  { id: 'peningkatan', label: 'Peningkatan' }
];

// Definisi pertanyaan untuk setiap tab sesuai PPEPP Adiwiyata
const questions = {
  penetapan: [
    {
      id: 'q1',
      text: 'Apakah sekolah telah menetapkan kebijakan lingkungan yang jelas dan tertulis?',
      hint: 'Terkait penetapan visi, misi, dan tujuan program lingkungan'
    },
    {
      id: 'q2',
      text: 'Apakah ada tim khusus yang bertanggung jawab untuk program Adiwiyata?',
      hint: 'Tim lingkungan, komite, atau koordinator program'
    },
    {
      id: 'q3',
      text: 'Apakah target dan indikator keberhasilan program lingkungan sudah ditetapkan?',
      hint: 'Target terukur untuk pelestarian lingkungan'
    },
    {
      id: 'q4',
      text: 'Apakah kurikulum sekolah mengintegrasikan pendidikan lingkungan hidup?',
      hint: 'Integrasi pada semua mata pelajaran atau program khusus'
    },
    {
      id: 'q5',
      text: 'Apakah ada alokasi anggaran untuk mendukung program Adiwiyata?',
      hint: 'Dana untuk kegiatan, sarana, dan prasarana lingkungan'
    },
    {
      id: 'q6',
      text: 'Apakah ada alokasi anggaran untuk mendukung program Adiwiyata?',
      hint: 'Dana untuk kegiatan, sarana, dan prasarana lingkungan'
    }
  ],
  pelaksanaan: [
    {
      id: 'q1',
      text: 'Apakah sekolah secara konsisten melaksanakan kegiatan pembelajaran tentang lingkungan?',
      hint: 'Pembelajaran aktif, proyek, dan kegiatan praktik lapangan'
    },
    {
      id: 'q2',
      text: 'Apakah sekolah memiliki fasilitas pengelolaan sampah yang baik (TPS, komposting, daur ulang)?',
      hint: 'Tersedianya tempat sampah terpisah dan pengelolaan limbah'
    },
    {
      id: 'q3',
      text: 'Apakah program hemat air dan listrik sudah diterapkan di sekolah?',
      hint: 'Penghematan energi dan konservasi sumber daya air'
    },
    {
      id: 'q4',
      text: 'Apakah sekolah melakukan program penghijauan dan perawatan taman sekolah?',
      hint: 'Taman edukasi, tanaman obat, atau area hijau lainnya'
    },
    {
      id: 'q5',
      text: 'Apakah partisipasi siswa dan guru dalam kegiatan lingkungan sudah berjalan aktif?',
      hint: 'Keterlibatan dalam program penghijauan, kebersihan, daur ulang'
    }
  ],
  evaluasi: [
    {
      id: 'q1',
      text: 'Apakah sekolah melakukan evaluasi berkala terhadap pencapaian program Adiwiyata?',
      hint: 'Evaluasi mingguan, bulanan, atau tahunan'
    },
    {
      id: 'q2',
      text: 'Apakah terdapat data pengukuran perubahan perilaku siswa terhadap lingkungan?',
      hint: 'Perubahan kesadaran, sikap, dan tindakan peduli lingkungan'
    },
    {
      id: 'q3',
      text: 'Apakah sekolah mendokumentasikan hasil dan dampak program lingkungan?',
      hint: 'Dokumentasi foto, laporan, atau database program'
    },
    {
      id: 'q4',
      text: 'Apakah efektivitas pengelolaan sampah dan energi sudah diukur?',
      hint: 'Pengukuran volume sampah, penggunaan listrik, air'
    },
    {
      id: 'q5',
      text: 'Apakah ada mekanisme umpan balik dari stakeholder tentang program?',
      hint: 'Survei kepuasan siswa, guru, orang tua, atau komite sekolah'
    }
  ],
  pengendalian: [
    {
      id: 'q1',
      text: 'Apakah sekolah memiliki sistem monitoring rutin untuk pelaksanaan program?',
      hint: 'Pemeriksaan harian atau mingguan oleh tim lingkungan'
    },
    {
      id: 'q2',
      text: 'Apakah ada mekanisme kontrol kualitas untuk limbah dan sampah sekolah?',
      hint: 'Standar pengelolaan sampah dan bahan berbahaya'
    },
    {
      id: 'q3',
      text: 'Apakah terdapat SOP (Standar Operasional Prosedur) yang jelas dan dipatuhi?',
      hint: 'Panduan tertulis untuk setiap kegiatan lingkungan'
    },
    {
      id: 'q4',
      text: 'Apakah ada sistem pelaporan dan pencatatan kegiatan lingkungan secara teratur?',
      hint: 'Laporan mingguan atau bulanan tentang pelaksanaan program'
    },
    {
      id: 'q5',
      text: 'Apakah sekolah melakukan koreksi atas temuan atau penyimpangan program?',
      hint: 'Tindakan perbaikan ketika ada masalah atau kurang capaian'
    }
  ],
  peningkatan: [
    {
      id: 'q1',
      text: 'Apakah sekolah melakukan upaya berkelanjutan untuk meningkatkan program Adiwiyata?',
      hint: 'Inovasi, perbaikan proses, atau pengembangan kegiatan baru'
    },
    {
      id: 'q2',
      text: 'Apakah ada pelatihan dan pengembangan kapasitas tim lingkungan sekolah?',
      hint: 'Workshop, seminar, atau pembelajaran dari institusi lain'
    },
    {
      id: 'q3',
      text: 'Apakah sekolah membangun kemitraan dengan komunitas atau lembaga lingkungan?',
      hint: 'Kolaborasi dengan pemerintah, NGO, atau sekolah lain'
    },
    {
      id: 'q4',
      text: 'Apakah ada program sosialisasi dan advokasi lingkungan ke masyarakat sekitar?',
      hint: 'Kampanye, seminar, atau kegiatan publik tentang lingkungan'
    },
    {
      id: 'q5',
      text: 'Apakah sekolah terus mengembangkan infrastruktur hijau dan ramah lingkungan?',
      hint: 'Penambahan fasilitas green building, solar panel, atau sistem air hujan'
    }
  ]
};

// Skala penilaian
const ratingScale = [
  { value: 1, label: 'Very Bad', color: 'bg-red-500', textColor: 'text-red-600', bgColor: 'bg-red-50' },
  { value: 2, label: 'Bad', color: 'bg-orange-500', textColor: 'text-orange-600', bgColor: 'bg-orange-50' },
  { value: 3, label: 'Neutral', color: 'bg-gray-400', textColor: 'text-gray-600', bgColor: 'bg-gray-50' },
  { value: 4, label: 'Good', color: 'bg-yellow-500', textColor: 'text-yellow-600', bgColor: 'bg-yellow-50' },
  { value: 5, label: 'Excellent', color: 'bg-green-500', textColor: 'text-green-600', bgColor: 'bg-green-50' }
];

// Function untuk hitung skor total tab
const calculateTabScore = (tabId) => {
  const responses = formResponses.value[tabId];
  const scores = Object.values(responses).filter(v => v !== null);
  if (scores.length === 0) return 0;
  return (scores.reduce((a, b) => a + b, 0) / scores.length).toFixed(2);
};

// Function untuk hitung progress tab
const calculateTabProgress = (tabId) => {
  const responses = formResponses.value[tabId];
  const totalQuestions = Object.keys(responses).length;
  const answered = Object.values(responses).filter(v => v !== null).length;
  return Math.round((answered / totalQuestions) * 100);
};

// Function untuk handle save
const handleSave = async () => {
  isSubmitting.value = true;
  submitMessage.value = '';

  try {
    // Simulasi API call
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // Hitung skor total semua tab
    const totalScore = tabs.map(tab => parseFloat(calculateTabScore(tab.id)) || 0)
      .reduce((a, b) => a + b, 0) / tabs.length;
    
    submitMessage.value = `✅ Kuisioner berhasil disimpan! Skor rata-rata: ${totalScore.toFixed(2)}/5`;
    
    setTimeout(() => {
      submitMessage.value = '';
    }, 4000);
  } catch (error) {
    submitMessage.value = '❌ Gagal menyimpan kuisioner. Coba lagi!';
  } finally {
    isSubmitting.value = false;
  }
};

// Function untuk reset form
const handleReset = () => {
  if (confirm('Apakah Anda yakin ingin mereset semua jawaban?')) {
    Object.keys(formResponses.value).forEach(key => {
      Object.keys(formResponses.value[key]).forEach(q => {
        formResponses.value[key][q] = null;
      });
    });
    submitMessage.value = '';
  }
};
</script>

<template>
  <MainLayout>
    <!-- Header -->
    <div class="px-4 sm:px-6 lg:px-2 py-3">
      <Header
        title="Form Evaluasi Adiwiyata"
        description="Kelola dan monitor program lingkungan sekolah Anda melalui penilaian PPEPP"
        color="green"
        size="lg"
      />
    </div>

    <div class="px-4 sm:px-6 lg:px-8 py-6">
      <!-- Tab Navigation - Dark Style seperti gambar -->
      <div class="bg-gray-100 rounded-lg overflow-hidden mb-8">
        <div class="flex flex-col sm:flex-row overflow-x-auto">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'px-6 py-4 font-semibold text-sm sm:text-base transition-all duration-300 whitespace-nowrap border-b-2 sm:border-b-0 sm:border-r',
              activeTab === tab.id
                ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-lg'
                : 'bg-gray-100 text-gray-400 hover:text-gray-300 hover:bg-gray-600'
            ]"
          >
            <div class="flex items-center gap-2">
              <span>{{ tab.icon }}</span>
              <span>{{ tab.label }}</span>
            </div>
          </button>
        </div>

        <!-- Progress Bar di bawah tabs -->
        <div class="bg-gray-100 px-6 py-3 border-t border-gray-700">
          <div class="flex items-center justify-between gap-4">
            <div class="text-sm text-gray-400">
              <span class="text-green-400 font-semibold">{{ calculateTabProgress(activeTab) }}%</span> 
              selesai
            </div>
            <div class="flex-1 bg-gray-700 rounded-full h-2 overflow-hidden">
              <div 
                class="bg-gradient-to-r from-green-500 to-emerald-500 h-full transition-all duration-300"
                :style="{ width: calculateTabProgress(activeTab) + '%' }"
              ></div>
            </div>
            <div class="text-sm text-gray-400">
              Skor: <span class="text-yellow-400 font-semibold">{{ calculateTabScore(activeTab) }}/5</span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Tab Content -->
      <div class="space-y-6">
        <!-- Questions Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-8">
          <!-- Header Tab Content -->
          <div class="mb-8 pb-6 border-b border-green-100">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">
              {{ tabs.find(t => t.id === activeTab)?.label }}
            </h2>
            <p class="text-gray-600">
              Silakan jawab pertanyaan berikut dengan memilih skalanya.
            </p>
          </div>

          <!-- Questions List -->
          <div class="space-y-8">
            <template v-for="(question, index) in questions[activeTab]" :key="question.id">
              <div class="border-b border-gray-100 pb-8 last:border-b-0 last:pb-0">
                <!-- Question Text -->
                <div class="mb-4">
                  <div class="flex items-start gap-3 mb-2">
                    <span class="flex-shrink-0 w-7 h-7 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-sm">
                      {{ index + 1 }}
                    </span>
                    <h3 class="font-semibold text-gray-900 text-lg">{{ question.text }}</h3>
                  </div>
                  <p class="text-sm text-gray-500 ml-10">{{ question.hint }}</p>
                </div>

                <!-- Rating Scale -->
                <div class="ml-10 mb-6">
                  <div class="grid grid-cols-5 gap-2 sm:gap-3 md:gap-4">
                    <button
                      v-for="rating in ratingScale"
                      :key="rating.value"
                      @click="formResponses[activeTab][question.id] = rating.value"
                      :class="[
                        'p-3 sm:p-4 rounded-lg border-2 transition-all duration-200 transform hover:scale-105 active:scale-95',
                        formResponses[activeTab][question.id] === rating.value
                          ? `${rating.color} text-white border-${rating.color} shadow-md`
                          : `${rating.bgColor} text-gray-600 border-gray-200 hover:border-green-300`
                      ]"
                    >
                      <div class="flex flex-col items-center">
                        <span class="font-bold text-lg">{{ rating.value }}</span>
                        <span class="text-xs mt-1 font-semibold hidden sm:inline">{{ rating.label }}</span>
                      </div>
                    </button>
                  </div>
                </div>

                <!-- Selected Answer Display -->
                <div v-if="formResponses[activeTab][question.id]" class="ml-10">
                  <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-green-50 border border-green-200">
                    <span class="text-sm font-medium text-green-700">
                      ✓ Dipilih: 
                      <span class="font-bold">
                        {{ ratingScale.find(r => r.value === formResponses[activeTab][question.id])?.label }}
                      </span>
                    </span>
                  </div>
                </div>
              </div>
            </template>
          </div>

          <!-- Summary Section -->
          <div class="mt-8 pt-6 border-t-2 border-green-100 bg-green-50 rounded-lg p-6">
            <div class="flex items-center gap-3 mb-4">
              <BarChart3 class="w-6 h-6 text-green-600" />
              <h3 class="font-bold text-gray-900">Ringkasan Tab {{ tabs.find(t => t.id === activeTab)?.label }}</h3>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="bg-white rounded-lg p-4 border border-green-200">
                <p class="text-sm text-gray-600 mb-1">Total Pertanyaan</p>
                <p class="text-2xl font-bold text-green-600">{{ questions[activeTab].length }}</p>
              </div>
              <div class="bg-white rounded-lg p-4 border border-green-200">
                <p class="text-sm text-gray-600 mb-1">Sudah Dijawab</p>
                <p class="text-2xl font-bold text-green-600">
                  {{ Object.values(formResponses[activeTab]).filter(v => v !== null).length }}
                </p>
              </div>
              <div class="bg-white rounded-lg p-4 border border-green-200">
                <p class="text-sm text-gray-600 mb-1">Rata-rata Skor</p>
                <p class="text-2xl font-bold text-green-600">{{ calculateTabScore(activeTab) }}</p>
              </div>
              <div class="bg-white rounded-lg p-4 border border-green-200">
                <p class="text-sm text-gray-600 mb-1">Progress</p>
                <p class="text-2xl font-bold text-green-600">{{ calculateTabProgress(activeTab) }}%</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Success Message -->
        <Transition
          enter-active-class="transition-all duration-300"
          enter-from-class="opacity-0 transform translate-y-4"
          enter-to-class="opacity-100 transform translate-y-0"
          leave-active-class="transition-all duration-300"
          leave-from-class="opacity-100 transform translate-y-0"
          leave-to-class="opacity-0 transform translate-y-4"
        >
          <div 
            v-if="submitMessage"
            :class=" [
              'flex items-start gap-3 p-4 rounded-lg border',
              submitMessage.includes('✅') 
                ? 'bg-green-50 border-green-200 text-green-800'
                : 'bg-red-50 border-red-200 text-red-800'
            ]"
          >
            <AlertCircle class="w-5 h-5 flex-shrink-0 mt-0.5" />
            <p class="font-medium">{{ submitMessage }}</p>
          </div>
        </Transition>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
          <button
            @click="handleReset"
            class="w-full sm:w-auto px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors duration-200 active:scale-95"
          >
            ↻ Reset Semua
          </button>

          <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <!-- Score Display -->
            <div class="flex items-center gap-2 px-4 py-3 bg-green-50 border-2 border-green-200 rounded-lg">
              <BarChart3 class="w-5 h-5 text-green-600" />
              <span class="font-bold text-green-700">
                Total Skor: 
                <span class="text-lg">
                  {{ (tabs.map(t => parseFloat(calculateTabScore(t.id)) || 0).reduce((a, b) => a + b, 0) / tabs.length).toFixed(2) }}/1.00  
                </span>
              </span>
            </div>

            <!-- Save Button -->
            <button
              @click="handleSave"
              :disabled="isSubmitting"
              :class=" [
                'w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 font-semibold rounded-lg transition-all duration-200 active:scale-95',
                isSubmitting
                  ? 'bg-gray-400 text-white cursor-not-allowed'
                  : 'bg-green-600 text-white hover:bg-green-700 shadow-md hover:shadow-lg'
              ]"
            >
              <Save class="w-5 h-5" />
              {{ isSubmitting ? 'Menyimpan...' : 'Simpan Kuisioner' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<style scoped>
/* Smooth transitions untuk semua elemen */
button {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom scrollbar untuk tab navigation */
div::-webkit-scrollbar {
  height: 4px;
}

div::-webkit-scrollbar-track {
  background: transparent;
}

div::-webkit-scrollbar-thumb {
  background: linear-gradient(to right, #059669, #10b981);
  border-radius: 10px;
}

div::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to right, #047857, #059669);
}
</style>