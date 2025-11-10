<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label
          for="client"
          class="block text-sm font-semibold text-gray-700 mb-2"
        >
          Cliente *
        </label>
        <select
          id="client"
          v-model="form.client_id"
          :class="getInputClass('client_id')"
          @change="loadClientInfo"
          :disabled="isEditMode"
          required
        >
          <option value="">Selecione um cliente</option>
          <option v-for="client in clients" :key="client.id" :value="client.id">
            {{ client.name }}
          </option>
        </select>
        <p v-if="errors.client_id" class="mt-1 text-sm text-red-600">
          {{ errors.client_id }}
        </p>
      </div>

      <div>
        <label
          for="status"
          class="block text-sm font-semibold text-gray-700 mb-2"
        >
          Status *
        </label>
        <select
          id="status"
          v-model="form.status"
          :class="getInputClass('status')"
          required
        >
          <option value="">Selecione um status</option>
          <option value="ativo">Ativo</option>
          <option value="devolvido">Devolvido</option>
          <option value="atrasado">Atrasado</option>
        </select>
        <p v-if="errors.status" class="mt-1 text-sm text-red-600">
          {{ errors.status }}
        </p>
      </div>

      <div>
        <label
          for="rental_date"
          class="block text-sm font-semibold text-gray-700 mb-2"
        >
          Data de Aluguel *
        </label>
        <input
          id="rental_date"
          v-model="form.rental_date"
          type="date"
          :class="getInputClass('rental_date')"
          required
        />
        <p v-if="errors.rental_date" class="mt-1 text-sm text-red-600">
          {{ errors.rental_date }}
        </p>
      </div>

      <div>
        <label
          for="return_date"
          class="block text-sm font-semibold text-gray-700 mb-2"
        >
          Data de Devolução *
        </label>
        <input
          id="return_date"
          v-model="form.return_date"
          type="date"
          :class="getInputClass('return_date')"
          required
        />
        <p v-if="errors.return_date" class="mt-1 text-sm text-red-600">
          {{ errors.return_date }}
        </p>
      </div>

      <div>
        <label
          for="total_value"
          class="block text-sm font-semibold text-gray-700 mb-2"
        >
          Valor Total *
        </label>
        <input
          id="total_value"
          v-model="form.total_value"
          type="number"
          step="0.01"
          placeholder="0.00"
          :class="getInputClass('total_value')"
          required
        />
        <p v-if="errors.total_value" class="mt-1 text-sm text-red-600">
          {{ errors.total_value }}
        </p>
      </div>

      <div>
        <label
          for="penalty"
          class="block text-sm font-semibold text-gray-700 mb-2"
        >
          Multa
        </label>
        <input
          id="penalty"
          v-model="form.penalty"
          type="number"
          step="0.01"
          placeholder="0.00"
          :class="getInputClass('penalty')"
        />
        <p v-if="errors.penalty" class="mt-1 text-sm text-red-600">
          {{ errors.penalty }}
        </p>
      </div>

      <div class="md:col-span-2">
        <label
          for="movies"
          class="block text-sm font-semibold text-gray-700 mb-2"
        >
          Filmes *
        </label>
        <div class="space-y-2">
          <div
            v-for="movie in selectedMovies"
            :key="movie.id"
            class="flex items-center justify-between bg-gray-50 p-3 rounded-lg"
          >
            <span class="text-sm text-gray-700">{{ movie.title }}</span>
            <button
              type="button"
              @click="removeMovie(movie.id)"
              class="text-red-600 hover:text-red-800"
            >
              ✕
            </button>
          </div>
          <select
            v-model="newMovieId"
            @change="addMovie"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Adicionar filme...</option>
            <option
              v-for="movie in availableMovies"
              :key="movie.id"
              :value="movie.id"
            >
              {{ movie.title }}
            </option>
          </select>
        </div>
        <p v-if="errors.movies" class="mt-1 text-sm text-red-600">
          {{ errors.movies }}
        </p>
      </div>
    </div>

    <div class="flex gap-3 justify-end border-t pt-6">
      <button
        type="button"
        @click="handleCancel"
        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium"
      >
        Cancelar
      </button>
      <button
        type="submit"
        :disabled="isLoading"
        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg font-medium flex items-center gap-2"
      >
        <span v-if="isLoading" class="animate-spin">⟳</span>
        {{ isEditMode ? "Atualizar" : "Criar" }} Aluguel
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, reactive, computed, watch } from "vue";
import apiClient from "@/services/apiClient";

const props = defineProps({
  rental: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(["cancel", "submit", "success", "error"]);

const form = reactive({
  client_id: "",
  status: "ativo",
  rental_date: "",
  return_date: "",
  total_value: "",
  penalty: "",
  movies: [],
});

const errors = reactive({
  client_id: null,
  status: null,
  rental_date: null,
  return_date: null,
  total_value: null,
  penalty: null,
  movies: null,
});

const isLoading = ref(false);
const clients = ref([]);
const availableMovies = ref([]);
const selectedMovies = ref([]);
const newMovieId = ref("");
const isEditMode = computed(() => !!props.rental?.id);

const clearErrors = () => {
  Object.keys(errors).forEach((key) => {
    errors[key] = null;
  });
};

const resetForm = () => {
  form.client_id = "";
  form.status = "ativo";
  form.rental_date = "";
  form.return_date = "";
  form.total_value = "";
  form.penalty = "";
  form.movies = [];
  selectedMovies.value = [];
  newMovieId.value = "";
  clearErrors();
};

watch(
  () => props.rental,
  (newRental) => {
    if (!newRental) {
      resetForm();
      return;
    }

    form.client_id = newRental.client?.id || "";
    form.status = newRental.status || "ativo";
    form.rental_date = newRental.rental_date || "";
    form.return_date = newRental.return_date || "";
    form.total_value = newRental.total_value || "";
    form.penalty = newRental.penalty || "";
    selectedMovies.value = newRental.movies || [];
    form.movies = (newRental.movies || []).map((m) => m.id);
  },
  { immediate: true }
);

const loadClients = async () => {
  try {
    const response = await apiClient.get("users", {
      params: { role: "cliente", per_page: 100 },
    });
    clients.value = response.data.data || [];
  } catch (error) {
    console.error("Erro ao carregar clientes:", error);
  }
};

const loadMovies = async () => {
  try {
    const response = await apiClient.get("movies", {
      params: { per_page: 100 },
    });
    availableMovies.value = response.data.data || [];
  } catch (error) {
    console.error("Erro ao carregar filmes:", error);
  }
};

const loadClientInfo = async () => {
  if (!form.client_id) return;

  try {
    await apiClient.get(`users/${form.client_id}`);
  } catch (error) {
    console.error("Erro ao carregar informações do cliente:", error);
  }
};

const addMovie = () => {
  if (!newMovieId.value) return;

  const movie = availableMovies.value.find((m) => m.id === newMovieId.value);
  if (movie && !selectedMovies.value.find((m) => m.id === movie.id)) {
    selectedMovies.value.push(movie);
    form.movies.push(movie.id);
    calculateTotalValue();
    newMovieId.value = "";
  }
};

const removeMovie = (movieId) => {
  selectedMovies.value = selectedMovies.value.filter((m) => m.id !== movieId);
  form.movies = form.movies.filter((id) => id !== movieId);
  calculateTotalValue();
};

const calculateTotalValue = () => {
  const total = selectedMovies.value.reduce((sum, movie) => {
    return sum + parseFloat(movie.rental_price || 0);
  }, 0);
  form.total_value = total.toFixed(2);
};

const getInputClass = (fieldName) => {
  const baseClass =
    "w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 transition";
  const errorClass = errors[fieldName]
    ? "border-red-500 focus:ring-red-500"
    : "border-gray-300 focus:ring-blue-500";
  return `${baseClass} ${errorClass}`;
};

const handleCancel = () => {
  resetForm();
  emit("cancel");
};

const handleSubmit = async () => {
  clearErrors();
  isLoading.value = true;

  try {
    const payload = {
      client_id: form.client_id,
      status: form.status,
      rental_date: form.rental_date,
      return_date: form.return_date,
      total_value: form.total_value,
      penalty: form.penalty || 0,
      movies: form.movies,
    };

    const apiMethod = isEditMode.value
      ? apiClient.put(`rentals/${props.rental.id}`, payload)
      : apiClient.post("rentals", payload);

    const response = await apiMethod;

    emit("submit", response.data.data || response.data);
    emit("success", {
      message: isEditMode.value
        ? "Aluguel atualizado com sucesso"
        : "Aluguel criado com sucesso",
    });
    resetForm();
  } catch (error) {
    if (error.response?.data?.errors) {
      Object.entries(error.response.data.errors).forEach(([key, value]) => {
        errors[key] = Array.isArray(value) ? value[0] : value;
      });
      return;
    }
    if (error.response?.data?.message) {
      emit("error", { message: error.response.data.message });
      return;
    }
    emit("error", {
      message: isEditMode.value
        ? "Erro ao atualizar aluguel"
        : "Erro ao criar aluguel",
    });
  } finally {
    isLoading.value = false;
  }
};

loadClients();
loadMovies();
</script>
