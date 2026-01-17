<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import {
    Eye,
    MessageCirclePlus,
    ChevronDown,
    ChevronUp,
    FileText,
    CheckCircle2,
} from "lucide-vue-next";

const props = defineProps({
    user: Object,
    assignedSchool: Object,
    formData: Object,
});

const commentForm = useForm({
    file_id: null,
    file_type: null,
    comment: "",
});

const activeComment = ref(null);
const collapsedSections = ref({
    a5: false,
    a6: true,
    a8: true,
});

// Computed properties untuk statistik
const totalFiles = computed(() => {
    let count = 0;
    if (props.formData?.a5?.length) count += props.formData.a5.length;
    if (props.formData?.a6?.length) count += props.formData.a6.length;
    if (props.formData?.a8) count += 1;
    return count;
});

const filesWithComments = computed(() => {
    let count = 0;
    if (props.formData?.a5?.length) {
        count += props.formData.a5.filter((f) => f.comments?.length > 0).length;
    }
    if (props.formData?.a6?.length) {
        count += props.formData.a6.filter((f) => f.comments?.length > 0).length;
    }
    if (props.formData?.a8?.comments?.length > 0) {
        count += 1;
    }
    return count;
});

const toggleSection = (section) => {
    collapsedSections.value[section] = !collapsedSections.value[section];
};

const submitComment = (fileId, fileType) => {
    commentForm.file_id = fileId;
    commentForm.file_type = fileType;

    commentForm.post(route("mentor.comment.store"), {
        preserveScroll: true,
        onSuccess: () => {
            commentForm.reset("comment");
            activeComment.value = null;
        },
    });
};

const getFileIcon = (filename) => {
    const ext = filename?.split(".").pop()?.toLowerCase();
    if (["pdf"].includes(ext)) return "📄";
    if (["doc", "docx"].includes(ext)) return "📝";
    if (["jpg", "jpeg", "png"].includes(ext)) return "🖼️";
    return "📎";
};

const formatFileSize = (bytes) => {
    if (!bytes) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i];
};

const previewFile = (type, id) => {
    window.open(route("file-evidence.preview", { type, id }), "_blank");
};

const toggleComment = (fileId) => {
    activeComment.value = activeComment.value === fileId ? null : fileId;
};
</script>

<template>

    <Head title="Form Mentor" />
    <MainLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900">
                                Form Submission - Review
                            </h1>
                            <p class="text-gray-600 mt-2">
                                Review dan berikan komentar pada dokumen yang
                                diupload
                            </p>
                        </div>
                        <FileText class="w-12 h-12 text-blue-600 opacity-20" />
                    </div>

                    <!-- School Info Card -->
                    <div v-if="assignedSchool"
                        class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 p-6 rounded-lg shadow-sm">
                        <p class="text-sm text-blue-700 font-semibold uppercase tracking-wide">
                            Sekolah yang Ditugaskan
                        </p>
                        <h2 class="text-2xl font-bold text-blue-900 mt-2">
                            {{
                                assignedSchool.school_name ||
                                assignedSchool.school_email
                            }}
                        </h2>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl flex flex-row">
                    <!-- Statistics Cards -->
                    <div class="flex flex-col gap-8 p-6 bg-gray-50 border-b">
                        <!-- Total Files Card -->
                        <div
                            class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-600 text-sm font-medium">
                                        Total File Keseluruhan
                                    </p>
                                    <p class="text-4xl font-bold text-blue-600 mt-2">
                                        {{ totalFiles }}
                                    </p>
                                    <p class="text-gray-500 text-xs mt-2">
                                        File dari A5, A6, dan A8
                                    </p>
                                </div>
                                <div class="bg-blue-100 p-4 rounded-full">
                                    <FileText class="w-8 h-8 text-blue-600" />
                                </div>
                            </div>
                        </div>

                        <!-- Files Reviewed Card -->
                        <div
                            class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-600 text-sm font-medium">
                                        File Direview
                                    </p>
                                    <p class="text-4xl font-bold text-green-600 mt-2">
                                        {{ filesWithComments }}
                                    </p>
                                    <p class="text-gray-500 text-xs mt-2">
                                        {{
                                            Math.round(
                                                (filesWithComments /
                                                    totalFiles) *
                                                100,
                                            )
                                        }}% dari total file
                                    </p>
                                </div>
                                <div class="bg-green-100 p-4 rounded-full">
                                    <CheckCircle2 class="w-8 h-8 text-green-600" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Data State -->
                    <div v-if="
                        !formData ||
                        (!formData.a5?.length &&
                            !formData.a6?.length &&
                            !formData.a8)
                    " class="p-12 text-center">
                        <div class="flex justify-center mb-4">
                            <svg class="h-20 w-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium text-lg">
                            Belum ada data form
                        </p>
                        <p class="text-gray-400 mt-2">
                            Sekolah belum mengisi form evaluasi
                        </p>
                    </div>

                    <!-- Form Data -->
                    <div v-else class="w-full">
                        <!-- Sections -->
                        <div class="p-6 space-y-6">
                            <!-- A5 - Rencana -->
                            <div v-if="formData.a5?.length"
                                class="border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition w-full">
                                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-green-200 flex items-center justify-between cursor-pointer hover:from-green-100 hover:to-emerald-100 transition"
                                    @click="toggleSection('a5')">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-green-600 rounded-full p-2">
                                            <FileText class="w-5 h-5 text-white" />
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                A5 - Rencana & Evaluasi
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                {{ formData.a5.length }} file
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronUp v-if="!collapsedSections.a5" class="w-6 h-6 text-gray-600" />
                                    <ChevronDown v-else class="w-6 h-6 text-gray-600" />
                                </div>
                                <div v-show="!collapsedSections.a5" class="p-6 space-y-4 bg-white">
                                    <div v-for="file in formData.a5" :key="file.id"
                                        class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition">
                                        <div class="p-4 bg-gray-50 hover:bg-gray-100 transition">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-4">
                                                    <div class="text-4xl bg-white rounded-lg p-2">
                                                        {{
                                                            getFileIcon(
                                                                file.path_file,
                                                            )
                                                        }}
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold text-gray-900">
                                                            {{ file.indikator }}
                                                        </p>
                                                        <p class="text-sm text-gray-600">
                                                            {{
                                                                formatFileSize(
                                                                    file.file_size,
                                                                )
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <button @click="
                                                        previewFile(
                                                            'a5',
                                                            file.id,
                                                        )
                                                        "
                                                        class="p-2 text-blue-600 rounded-lg hover:bg-blue-100 transition"
                                                        title="Lihat File">
                                                        <Eye class="w-6 h-6" />
                                                    </button>
                                                    <button @click="
                                                        toggleComment(
                                                            'a5-' + file.id,
                                                        )
                                                        "
                                                        class="p-2 text-green-600 rounded-lg hover:bg-green-100 transition"
                                                        title="Tambah Komentar">
                                                        <MessageCirclePlus class="w-6 h-6" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Comment Section -->
                                        <div v-if="
                                            activeComment ===
                                            'a5-' + file.id
                                        " class="p-4 bg-blue-50 border-t border-blue-200">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tambah
                                                Komentar</label>
                                            <textarea v-model="commentForm.comment" rows="3"
                                                class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                                placeholder="Tulis komentar Anda..."></textarea>
                                            <div class="mt-3 flex justify-end space-x-2">
                                                <button @click="
                                                    activeComment = null
                                                    "
                                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                                                    Batal
                                                </button>
                                                <button @click="
                                                    submitComment(
                                                        file.id,
                                                        'a5',
                                                    )
                                                    " :disabled="commentForm.processing
                                                        "
                                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition font-medium">
                                                    {{
                                                        commentForm.processing
                                                            ? "Mengirim..."
                                                            : "Kirim"
                                                    }}
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Existing Comments -->
                                        <div v-if="file.comments?.length"
                                            class="p-4 bg-gray-50 border-t border-gray-200">
                                            <p class="text-sm font-semibold text-gray-700 mb-3">
                                                💬 Komentar ({{
                                                    file.comments.length
                                                }})
                                            </p>
                                            <div class="space-y-2">
                                                <div v-for="comment in file.comments" :key="comment.id"
                                                    class="p-3 bg-white rounded-lg border border-gray-200 hover:shadow-sm transition">
                                                    <p class="text-sm text-gray-800">
                                                        {{ comment.comment }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1 font-medium">
                                                        👤
                                                        {{
                                                            comment.mentor_name
                                                        }}
                                                        •
                                                        {{
                                                            new Date(
                                                                comment.created_at,
                                                            ).toLocaleDateString(
                                                                "id-ID",
                                                                {
                                                                    year: "numeric",
                                                                    month: "short",
                                                                    day: "numeric",
                                                                    hour: "2-digit",
                                                                    minute: "2-digit",
                                                                },
                                                            )
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- A6 - Self Assessment -->
                            <div v-if="formData.a6?.length"
                                class="border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-6 py-4 border-b border-blue-200 flex items-center justify-between cursor-pointer hover:from-blue-100 hover:to-cyan-100 transition"
                                    @click="toggleSection('a6')">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-blue-600 rounded-full p-2">
                                            <FileText class="w-5 h-5 text-white" />
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                A6 - Self Assessment
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                {{ formData.a6.length }} file
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronUp v-if="!collapsedSections.a6" class="w-6 h-6 text-gray-600" />
                                    <ChevronDown v-else class="w-6 h-6 text-gray-600" />
                                </div>
                                <div v-show="!collapsedSections.a6" class="p-6 space-y-4 bg-white">
                                    <div v-for="file in formData.a6" :key="file.id"
                                        class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition">
                                        <div class="p-4 bg-gray-50 hover:bg-gray-100 transition">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-4">
                                                    <div class="text-4xl bg-white rounded-lg p-2">
                                                        {{
                                                            getFileIcon(
                                                                file.path_file,
                                                            )
                                                        }}
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold text-gray-900">
                                                            {{ file.indikator }}
                                                        </p>
                                                        <p class="text-sm text-gray-600">
                                                            {{
                                                                formatFileSize(
                                                                    file.file_size,
                                                                )
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <button @click="
                                                        previewFile(
                                                            'a6',
                                                            file.id,
                                                        )
                                                        "
                                                        class="p-2 text-blue-600 rounded-lg hover:bg-blue-100 transition"
                                                        title="Lihat File">
                                                        <Eye class="w-6 h-6" />
                                                    </button>
                                                    <button @click="
                                                        toggleComment(
                                                            'a6-' + file.id,
                                                        )
                                                        "
                                                        class="p-2 text-green-600 rounded-lg hover:bg-green-100 transition"
                                                        title="Tambah Komentar">
                                                        <MessageCirclePlus class="w-6 h-6" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Comment Section -->
                                        <div v-if="
                                            activeComment ===
                                            'a6-' + file.id
                                        " class="p-4 bg-blue-50 border-t border-blue-200">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tambah
                                                Komentar</label>
                                            <textarea v-model="commentForm.comment" rows="3"
                                                class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                                placeholder="Tulis komentar Anda..."></textarea>
                                            <div class="mt-3 flex justify-end space-x-2">
                                                <button @click="
                                                    activeComment = null
                                                    "
                                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                                                    Batal
                                                </button>
                                                <button @click="
                                                    submitComment(
                                                        file.id,
                                                        'a6',
                                                    )
                                                    " :disabled="commentForm.processing
                                                        "
                                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition font-medium">
                                                    {{
                                                        commentForm.processing
                                                            ? "Mengirim..."
                                                            : "Kirim"
                                                    }}
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Existing Comments -->
                                        <div v-if="file.comments?.length"
                                            class="p-4 bg-gray-50 border-t border-gray-200">
                                            <p class="text-sm font-semibold text-gray-700 mb-3">
                                                💬 Komentar ({{
                                                    file.comments.length
                                                }})
                                            </p>
                                            <div class="space-y-2">
                                                <div v-for="comment in file.comments" :key="comment.id"
                                                    class="p-3 bg-white rounded-lg border border-gray-200 hover:shadow-sm transition">
                                                    <p class="text-sm text-gray-800">
                                                        {{ comment.comment }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1 font-medium">
                                                        👤
                                                        {{
                                                            comment.mentor_name
                                                        }}
                                                        •
                                                        {{
                                                            new Date(
                                                                comment.created_at,
                                                            ).toLocaleDateString(
                                                                "id-ID",
                                                                {
                                                                    year: "numeric",
                                                                    month: "short",
                                                                    day: "numeric",
                                                                    hour: "2-digit",
                                                                    minute: "2-digit",
                                                                },
                                                            )
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- A8 - Pernyataan -->
                            <div v-if="formData.a8"
                                class="border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                                <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-purple-200 flex items-center justify-between cursor-pointer hover:from-purple-100 hover:to-pink-100 transition"
                                    @click="toggleSection('a8')">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-purple-600 rounded-full p-2">
                                            <FileText class="w-5 h-5 text-white" />
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                A8 - Pernyataan & Persetujuan
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                1 file
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronUp v-if="!collapsedSections.a8" class="w-6 h-6 text-gray-600" />
                                    <ChevronDown v-else class="w-6 h-6 text-gray-600" />
                                </div>
                                <div v-show="!collapsedSections.a8" class="p-6 bg-white">
                                    <div
                                        class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition">
                                        <div class="p-4 bg-gray-50 hover:bg-gray-100 transition">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-4">
                                                    <div class="text-4xl bg-white rounded-lg p-2">
                                                        {{
                                                            getFileIcon(
                                                                formData.a8
                                                                    .bukti_persetujuan,
                                                            )
                                                        }}
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold text-gray-900">
                                                            Bukti Persetujuan
                                                        </p>
                                                        <p class="text-sm text-gray-600">
                                                            {{
                                                                formatFileSize(
                                                                    formData.a8
                                                                        .file_size,
                                                                )
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <button @click="
                                                        previewFile(
                                                            'a8',
                                                            formData.a8.id,
                                                        )
                                                        "
                                                        class="p-2 text-blue-600 rounded-lg hover:bg-blue-100 transition"
                                                        title="Lihat File">
                                                        <Eye class="w-6 h-6" />
                                                    </button>
                                                    <button @click="
                                                        toggleComment(
                                                            'a8-' +
                                                            formData.a8
                                                                .id,
                                                        )
                                                        "
                                                        class="p-2 text-green-600 rounded-lg hover:bg-green-100 transition"
                                                        title="Tambah Komentar">
                                                        <MessageCirclePlus class="w-6 h-6" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Comment Section -->
                                        <div v-if="
                                            activeComment ===
                                            'a8-' + formData.a8.id
                                        " class="p-4 bg-purple-50 border-t border-purple-200">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tambah
                                                Komentar</label>
                                            <textarea v-model="commentForm.comment" rows="3"
                                                class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                                placeholder="Tulis komentar Anda..."></textarea>
                                            <div class="mt-3 flex justify-end space-x-2">
                                                <button @click="
                                                    activeComment = null
                                                    "
                                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                                                    Batal
                                                </button>
                                                <button @click="
                                                    submitComment(
                                                        formData.a8.id,
                                                        'a8',
                                                    )
                                                    " :disabled="commentForm.processing
                                                        "
                                                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition font-medium">
                                                    {{
                                                        commentForm.processing
                                                            ? "Mengirim..."
                                                            : "Kirim"
                                                    }}
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Existing Comments -->
                                        <div v-if="formData.a8.comments?.length"
                                            class="p-4 bg-gray-50 border-t border-gray-200">
                                            <p class="text-sm font-semibold text-gray-700 mb-3">
                                                💬 Komentar ({{
                                                    formData.a8.comments.length
                                                }})
                                            </p>
                                            <div class="space-y-2">
                                                <div v-for="comment in formData
                                                    .a8.comments" :key="comment.id"
                                                    class="p-3 bg-white rounded-lg border border-gray-200 hover:shadow-sm transition">
                                                    <p class="text-sm text-gray-800">
                                                        {{ comment.comment }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1 font-medium">
                                                        👤
                                                        {{
                                                            comment.mentor_name
                                                        }}
                                                        •
                                                        {{
                                                            new Date(
                                                                comment.created_at,
                                                            ).toLocaleDateString(
                                                                "id-ID",
                                                                {
                                                                    year: "numeric",
                                                                    month: "short",
                                                                    day: "numeric",
                                                                    hour: "2-digit",
                                                                    minute: "2-digit",
                                                                },
                                                            )
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Info -->
                <div v-if="
                    formData &&
                    (formData.a5?.length ||
                        formData.a6?.length ||
                        formData.a8)
                " class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold">💡 Tips:</span> Klik pada
                        judul section untuk membuka/menutup detail file. Gunakan
                        tombol 👁️ untuk melihat file dan 💬 untuk memberikan
                        komentar.
                    </p>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
