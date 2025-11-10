<template>
  <div class="min-h-screen bg-linear-to-br from-blue-50 to-blue-100 p-8">
    <div class="max-w-7xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-4xl font-bold text-gray-800">Catálogo de Filmes</h1>
          <p class="text-gray-600 mt-2">Navegue e alugue nossos filmes</p>
        </div>
        <button
          @click="handleBack"
          class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg"
        >
          Voltar
        </button>
      </div>

      <div v-if="loading" class="flex justify-center items-center h-64">
        <div class="animate-spin">
          <svg
            class="w-12 h-12 text-blue-600"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
        </div>
      </div>

      <div
        v-else
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
      >
        <div
          v-for="movie in movies"
          :key="movie.id"
          class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 transform hover:scale-105 cursor-pointer"
        >
          <div
            class="bg-linear-to-r from-blue-500 to-blue-600 h-40 flex items-center justify-center"
          >
            <div class="text-center">
              <p class="text-white text-xl font-semibold">{{ movie.title }}</p>
            </div>
          </div>

          <div class="p-5">
            <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2">
              Lançamento: {{ movie.release_year }}
            </h3>

            <div class="mb-3">
              <span
                class="inline-block bg-purple-100 text-purple-800 text-xs font-semibold px-3 py-1 rounded-full"
              >
                {{ movie.genre }}
              </span>
            </div>

            <p class="text-sm text-gray-600 mb-4 line-clamp-3">
              {{ movie.description }}
            </p>

            <div class="mb-4 flex items-center gap-2">
              <span
                class="inline-block bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full"
              >
                {{ movie.quantity }}
                <span v-if="movie.quantity === 1">cópia</span>
                <span v-else>cópias disponíveis</span>
              </span>
              <span
                v-if="movie.quantity === 0"
                class="inline-block bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full"
              >
                Indisponível
              </span>
              <span
                v-else-if="movie.quantity < 5"
                class="inline-block bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full"
              >
                Poucas
              </span>
            </div>

            <div
              class="flex items-baseline justify-between pt-4 border-t border-gray-200"
            >
              <span class="text-gray-600 text-sm">Aluguel</span>
              <span class="text-2xl font-bold text-green-600">
                R$ {{ parseFloat(movie.rental_price).toFixed(2) }}
              </span>
            </div>

            <button
              v-if="!isClient && movie.quantity > 0"
              @click="handleRent(movie)"
              class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition-colors duration-200"
            >
              Alugar
            </button>
            <button
              v-else-if="!isClient && movie.quantity === 0"
              disabled
              class="w-full mt-4 bg-gray-400 text-white font-semibold py-2 rounded-lg cursor-not-allowed"
            >
              Indisponível
            </button>
          </div>
        </div>
      </div>

      <div v-if="meta && !loading" class="mt-8 flex justify-center gap-2">
        <button
          v-for="page in meta.last_page"
          :key="page"
          @click="loadCatalog(page)"
          :class="
            page === currentPage
              ? 'bg-blue-600 text-white'
              : 'bg-white text-gray-800'
          "
          class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-blue-100 transition-colors duration-200"
        >
          {{ page }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import apiClient from "@/services/apiClient";
import { useAuth } from "@/composables/useAuth";

defineProps({
  onBack: Function,
});

const emit = defineEmits(["back"]);
const { state } = useAuth();

const movies = ref([]);
const loading = ref(true);
const meta = ref(null);
const currentPage = ref(1);

const isClient = computed(() => {
  return state.user?.role === "cliente";
});

const loadCatalog = async (page = 1) => {
  try {
    loading.value = true;
    const response = await apiClient.get("movies/catalog", {
      params: { page },
    });
    movies.value = response.data.data || [];
    meta.value = response.data.meta || null;
    currentPage.value = page;
    window.scrollTo({ top: 0, behavior: "smooth" });
  } catch (error) {
    console.error("Erro ao carregar catálogo:", error);
  } finally {
    loading.value = false;
  }
};

const handleRent = (movie) => {
  console.log("Alugando filme:", movie);
  // Implementar lógica de aluguel aqui
};

const handleBack = () => {
  emit("back");
};

onMounted(() => {
  loadCatalog();
});
</script>
