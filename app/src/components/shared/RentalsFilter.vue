<template>
  <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2"
          >Status</label
        >
        <select
          v-model="filters.status"
          @change="emitFilters"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Todos os status</option>
          <option value="ativo">Ativa</option>
          <option value="atrasado">Atrasada</option>
          <option value="devolvido">Devolvido</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2"
          >Data de Locação</label
        >
        <input
          v-model="filters.rental_date"
          @change="emitFilters"
          type="date"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2"
          >Data de Devolução</label
        >
        <input
          v-model="filters.return_date"
          @change="emitFilters"
          type="date"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2"
          >Código</label
        >
        <input
          v-model="filters.code"
          @change="emitFilters"
          type="text"
          placeholder="Ex: 123"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
    </div>

    <div class="flex mt-4 w-full justify-end">
      <div class="flex gap-3 w-50">
        <button
          @click="applyFilters"
          class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition-colors"
        >
          Filtrar
        </button>
        <button
          @click="resetFilters"
          class="flex-1 bg-gray-400 hover:bg-gray-500 text-white py-2 rounded-lg font-semibold transition-colors"
        >
          Limpar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const filters = ref({
  status: "",
  rental_date: "",
  return_date: "",
  code: "",
});

const emit = defineEmits(["apply", "reset"]);

const emitFilters = () => {
  emit("apply", { ...filters.value });
};

const applyFilters = () => {
  emit("apply", { ...filters.value });
};

const resetFilters = () => {
  filters.value = {
    status: "",
    rental_date: "",
    return_date: "",
    code: "",
  };
  emit("reset");
};
</script>
