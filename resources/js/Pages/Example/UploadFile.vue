<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const file = ref(null)
const errors = ref(null)

function submit() {
  const formData = new FormData()
  formData.append('file', file.value)

  router.post('/upload-file', formData, {
    forceFormData: true,
    onError: (err) => {
      errors.value = err
    }
  })
}
</script>

<template>
  <div class="p-6 space-y-4">
    <h1 class="text-xl font-bold">Upload File</h1>

    <input 
      type="file" 
      @change="file = $event.target.files[0]"
      class="border p-2"
    />

    <button 
      @click="submit"
      class="px-4 py-2 bg-blue-600 text-white rounded"
    >
      Upload
    </button>

    <div v-if="errors" class="text-red-600 mt-2">
      <p>{{ errors.file }}</p>
    </div>
  </div>
</template>
