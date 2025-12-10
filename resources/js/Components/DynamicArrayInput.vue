<script setup>
import { computed } from 'vue';
import { Trash2, Plus } from 'lucide-vue-next';

const props = defineProps({
  label: {
    type: String,
    required: true
  },
  modelValue: {
    type: Array,
    default: () => []
  },
  fields: {
    type: Array,
    required: true,
    validator: (value) => {
      return value.every(field => 
        field.name && field.label && field.type
      );
    }
  },
  required: Boolean
});

const emit = defineEmits(['update:modelValue']);

/**
 * Add new row
 */
const addRow = () => {
  const newRow = {};
  props.fields.forEach(field => {
    newRow[field.name] = '';
  });
  emit('update:modelValue', [...props.modelValue, newRow]);
};

/**
 * Update specific field in specific row
 * IMPORTANT: Buat object baru untuk trigger reactivity
 */
const updateField = (rowIndex, fieldName, value) => {
  const updated = props.modelValue.map((row, idx) => {
    if (idx === rowIndex) {
      // Return new object, bukan mutasi
      return {
        ...row,
        [fieldName]: value
      };
    }
    return row;
  });
  emit('update:modelValue', updated);
};

/**
 * Remove row
 */
const removeRow = (rowIndex) => {
  emit('update:modelValue', 
    props.modelValue.filter((_, idx) => idx !== rowIndex)
  );
};
</script>

<template>
  <div class="space-y-3">
    <!-- Label -->
    <label class="block text-sm font-semibold text-gray-900">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <!-- Rows -->
    <div class="space-y-3">
      <template v-for="(row, rowIndex) in modelValue" :key="rowIndex">
        <div class="flex gap-2 items-end p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
          <!-- Fields -->
          <div class="flex-1 grid gap-2" :style="{ gridTemplateColumns: `repeat(${fields.length}, 1fr)` }">
            <template v-for="field in fields" :key="field.name">
              <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-600 mb-1">
                  {{ field.label }}
                </label>
                <input
                  :type="field.type"
                  :value="row[field.name] || ''"
                  @input="(e) => updateField(rowIndex, field.name, e.target.value)"
                  :placeholder="`Masukkan ${field.label.toLowerCase()}`"
                  class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
            </template>
          </div>

          <!-- Delete Button -->
          <button
            @click="removeRow(rowIndex)"
            class="flex-shrink-0 p-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
            title="Hapus baris"
          >
            <Trash2 class="w-5 h-5" />
          </button>
        </div>
      </template>
    </div>

    <!-- Add Button -->
    <button
      @click="addRow"
      class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors font-semibold text-sm"
    >
      <Plus class="w-4 h-4" />
      Tambah Baris
    </button>
  </div>
</template>

<style scoped>
input {
  transition: all 0.2s ease;
}

input:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>