<template>
  <div class="bg-white rounded-lg shadow-lg p-6 h-screen flex flex-col">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold text-gray-800">Aluguéis</h2>
      <button
        @click="openNewRentalModal"
        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
      >
        <span>+</span>
        Novo Aluguel
      </button>
    </div>

    <div class="mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input
          v-model="searchQuery"
          @input="debounceSearch"
          type="text"
          placeholder="Buscar por código..."
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <select
          v-model="filterStatus"
          @change="handleStatusFilter"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Todos os status</option>
          <option value="ativo">Ativo</option>
          <option value="devolvido">Devolvido</option>
          <option value="atrasado">Atrasado</option>
        </select>
      </div>
    </div>

    <div class="overflow-x-auto grow overflow-y-auto">
      <table class="w-full">
        <thead class="sticky top-0 bg-white">
          <tr class="border-b border-gray-300">
            <th class="text-left py-3 px-4 font-semibold text-gray-700">
              Código
            </th>
            <th class="text-left py-3 px-4 font-semibold text-gray-700">
              Cliente
            </th>
            <th class="text-left py-3 px-4 font-semibold text-gray-700">
              Valor
            </th>
            <th class="text-left py-3 px-4 font-semibold text-gray-700">
              Multa
            </th>
            <th class="text-left py-3 px-4 font-semibold text-gray-700">
              Status
            </th>
            <th class="text-center py-3 px-4 font-semibold text-gray-700">
              Ações
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="rentals.length === 0" class="text-center">
            <td colspan="8" class="py-6 text-gray-500">
              Nenhum aluguel encontrado
            </td>
          </tr>
          <tr
            v-for="rental in rentals"
            :key="rental.id"
            class="border-b border-gray-200 hover:bg-gray-50"
          >
            <td class="py-3 px-4 font-semibold text-gray-700">
              #{{ rental.code }}
            </td>
            <td class="py-3 px-4">{{ rental.client?.name }}</td>
            <td class="py-3 px-4">
              R$ {{ parseFloat(rental.total_value).toFixed(2) }}
            </td>
            <td class="py-3 px-4">
              R$ {{ parseFloat(rental.penalty).toFixed(2) }}
            </td>
            <td class="py-3 px-4">
              <span
                class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                :class="{
                  'bg-green-100 text-green-800': rental.status === 'ativo',
                  'bg-blue-100 text-blue-800': rental.status === 'devolvido',
                  'bg-red-100 text-red-800': rental.status === 'atrasado',
                }"
              >
                {{ formatStatus(rental.status) }}
              </span>
            </td>
            <td class="py-3 px-4 text-center">
              <div class="relative group">
                <button
                  class="text-gray-600 hover:text-gray-800 hover:bg-gray-100 p-2 rounded-lg"
                  title="Ações"
                >
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                      d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                    />
                  </svg>
                </button>
                <div
                  class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-10"
                >
                  <button
                    @click="viewRental(rental)"
                    class="block w-full text-left px-4 py-2 text-green-600 hover:bg-green-50"
                  >
                    Visualizar
                  </button>
                  <button
                    v-if="rental.status !== 'devolvido'"
                    @click="editRental(rental)"
                    class="block w-full text-left px-4 py-2 text-blue-600 hover:bg-blue-50"
                  >
                    Editar
                  </button>
                  <button
                    v-if="rental.status === 'ativo'"
                    @click="deleteRental(rental.id)"
                    class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 border-t"
                  >
                    Remover
                  </button>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-3 flex justify-between items-center shrink-0">
      <p class="text-sm text-gray-600">
        Página {{ currentPage }} de {{ totalPages }}
      </p>
      <div class="flex gap-2">
        <button
          @click="previousPage"
          :disabled="currentPage === 1"
          class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Anterior
        </button>
        <button
          @click="nextPage"
          :disabled="currentPage === totalPages"
          class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Próximo
        </button>
      </div>
    </div>

    <!-- Modal para formulário de aluguel -->
    <Modal
      :isOpen="showRentalFormModal"
      :title="
        rentalFormMode === 'create'
          ? 'Novo Aluguel'
          : rentalFormMode === 'view'
          ? 'Detalhes do Aluguel'
          : 'Editar Aluguel'
      "
      @close="handleRentalFormClose"
    >
      <RentalForm
        v-if="rentalFormMode !== 'view'"
        :rental="selectedRental"
        @cancel="handleRentalFormClose"
        @submit="handleRentalFormSubmit"
        @success="handleRentalFormSuccess"
        @error="handleRentalFormError"
      />
      <div v-else class="space-y-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">
            Cliente
          </label>
          <p class="text-gray-600">{{ selectedRental?.client?.name }}</p>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">
            Valor Total
          </label>
          <p class="text-gray-600">
            R$ {{ parseFloat(selectedRental?.total_value).toFixed(2) }}
          </p>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">
            Multa
          </label>
          <p class="text-gray-600">
            R$ {{ parseFloat(selectedRental?.penalty).toFixed(2) }}
          </p>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">
            Filmes
          </label>
          <div class="space-y-2">
            <div
              v-for="movie in selectedRental?.movies"
              :key="movie.id"
              class="text-gray-600"
            >
              - {{ movie.title }} (R$
              {{ parseFloat(movie.rental_price).toFixed(2) }})
            </div>
          </div>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">
            Status
          </label>
          <p class="text-gray-600">
            {{ formatStatus(selectedRental?.status) }}
          </p>
        </div>
        <div class="flex gap-3 justify-end border-t pt-6">
          <button
            @click="handleRentalFormClose"
            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium"
          >
            Fechar
          </button>
          <button
            v-if="selectedRental?.status !== 'devolvido'"
            @click="editRental(selectedRental)"
            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium"
          >
            Editar
          </button>
          <button
            v-if="
              selectedRental?.status === 'ativo' ||
              selectedRental?.status === 'atrasado'
            "
            @click="processReturn(selectedRental)"
            :disabled="processingReturn"
            class="px-6 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg font-medium"
          >
            {{ processingReturn ? "Processando..." : "Finalizar Devolução" }}
          </button>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import apiClient from "@/services/apiClient";
import Modal from "@/components/shared/Modal.vue";
import RentalForm from "@/components/shared/RentalForm.vue";

const rentals = ref([]);
const searchQuery = ref("");
const filterStatus = ref("");
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = ref(10);
let searchTimeout = null;

const showRentalFormModal = ref(false);
const rentalFormMode = ref("create");
const selectedRental = ref(null);
const processingReturn = ref(false);

const formatDate = (date) => {
  if (!date) return "-";
  const d = new Date(date);
  return d.toLocaleDateString("pt-BR");
};

const formatStatus = (status) => {
  const statusMap = {
    ativo: "Ativo",
    devolvido: "Devolvido",
    atrasado: "Atrasado",
  };
  return statusMap[status] || status;
};

const loadRentals = async (page = 1, search = "") => {
  try {
    const params = {
      page: page,
      per_page: perPage.value,
    };

    if (search) {
      params.code = search;
    }

    if (filterStatus.value) {
      params.status = filterStatus.value;
    }

    const response = await apiClient.get("rentals", { params });

    rentals.value = response.data.data || [];
    currentPage.value = response.data.meta?.current_page || 1;
    totalPages.value = response.data.meta?.last_page || 1;
    perPage.value = response.data.meta?.per_page || 10;
  } catch (error) {
    console.error("Erro ao carregar aluguéis:", error);
    if (error.response) {
      console.error("Status:", error.response.status);
      console.error("Dados:", error.response.data);
    }
    rentals.value = [];
  }
};

const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    loadRentals(1, searchQuery.value);
  }, 500);
};

const handleStatusFilter = () => {
  currentPage.value = 1;
  loadRentals(1, searchQuery.value);
};

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    loadRentals(currentPage.value, searchQuery.value);
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
    loadRentals(currentPage.value, searchQuery.value);
  }
};

const openNewRentalModal = () => {
  rentalFormMode.value = "create";
  selectedRental.value = null;
  showRentalFormModal.value = true;
};

const viewRental = (rental) => {
  rentalFormMode.value = "view";
  selectedRental.value = rental;
  showRentalFormModal.value = true;
};

const editRental = (rental) => {
  rentalFormMode.value = "edit";
  selectedRental.value = rental;
  showRentalFormModal.value = true;
};

const deleteRental = async (rentalId) => {
  if (confirm("Tem certeza que deseja remover este aluguel?")) {
    try {
      await apiClient.delete(`rentals/${rentalId}`);
      alert("Aluguel removido com sucesso");
      loadRentals(currentPage.value, searchQuery.value);
    } catch (error) {
      console.error("Erro ao remover aluguel:", error);
      alert("Erro ao remover aluguel");
    }
  }
};

const handleRentalFormClose = () => {
  showRentalFormModal.value = false;
  selectedRental.value = null;
  rentalFormMode.value = "create";
};

const handleRentalFormSubmit = (data) => {
  console.log("Aluguel salvo:", data);
};

const handleRentalFormSuccess = ({ message }) => {
  loadRentals(currentPage.value, searchQuery.value);
  handleRentalFormClose();
};

const handleRentalFormError = ({ message }) => {
  console.error("Erro no formulário:", message);
};

const processReturn = async (rental) => {
  if (confirm("Deseja finalizar a devolução deste aluguel?")) {
    processingReturn.value = true;
    try {
      await apiClient.post(`rentals/${rental.id}/process-return`);
      alert("Devolução processada com sucesso");
      loadRentals(currentPage.value, searchQuery.value);
      handleRentalFormClose();
    } catch (error) {
      console.error("Erro ao processar devolução:", error);
      alert("Erro ao processar devolução");
    } finally {
      processingReturn.value = false;
    }
  }
};

onMounted(() => {
  loadRentals();
});
</script>
