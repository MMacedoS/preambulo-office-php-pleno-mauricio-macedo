<template>
  <div class="min-h-screen bg-linear-to-br from-green-50 to-green-100 p-8">
    <div class="max-w-7xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-4xl font-bold text-gray-800">Painel do Atendente</h1>
          <p class="text-gray-600 mt-2">{{ user.name }}</p>
        </div>
        <div class="flex gap-3">
          <button
            @click="showProfileEditor = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"
          >
            Perfil
          </button>

          <button
            @click="handleLogout"
            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg"
          >
            Sair
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm">Total de Clientes</p>
          <p class="text-3xl font-bold text-blue-600">{{ totalClients }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm">Locações Ativas</p>
          <p class="text-3xl font-bold text-green-600">{{ activeRentals }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm">Locações Atrasadas</p>
          <p class="text-3xl font-bold text-red-600">{{ lateReturns }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Clientes</h2>
            <button
              @click="openNewClientModal"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
            >
              <span>+</span>
              Novo Cliente
            </button>
          </div>

          <div class="mb-6">
            <input
              v-model="searchQuery"
              @input="debounceSearch"
              type="text"
              placeholder="Buscar por nome..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-300">
                  <th class="text-left py-3 px-4 font-semibold text-gray-700">
                    Nome
                  </th>
                  <th class="text-left py-3 px-4 font-semibold text-gray-700">
                    Email
                  </th>
                  <th class="text-left py-3 px-4 font-semibold text-gray-700">
                    Papel
                  </th>
                  <th class="text-center py-3 px-4 font-semibold text-gray-700">
                    Ações
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="clients.length === 0" class="text-center">
                  <td colspan="4" class="py-6 text-gray-500">
                    Nenhum cliente encontrado
                  </td>
                </tr>
                <tr
                  v-for="client in clients"
                  :key="client.id"
                  class="border-b border-gray-200 hover:bg-gray-50"
                >
                  <td class="py-3 px-4">{{ client.name }}</td>
                  <td class="py-3 px-4">{{ client.email }}</td>
                  <td class="py-3 px-4">
                    <span
                      class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                      :class="{
                        'bg-purple-100 text-purple-800':
                          client.role === 'administrador',
                        'bg-blue-100 text-blue-800':
                          client.role === 'atendente',
                        'bg-green-100 text-green-800':
                          client.role === 'cliente',
                      }"
                    >
                      {{ client.role }}
                    </span>
                  </td>
                  <td class="py-3 px-4 text-center">
                    <div class="flex gap-2 justify-center">
                      <button
                        @click="editClient(client)"
                        title="Editar"
                        class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded-lg"
                      >
                        <svg
                          class="w-5 h-5"
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path
                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                          />
                        </svg>
                      </button>
                      <button
                        @click="deleteClient(client.id)"
                        title="Remover"
                        class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg"
                      >
                        <svg
                          class="w-5 h-5"
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-6 flex justify-between items-center">
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
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
          <h2 class="text-2xl font-semibold text-gray-800 mb-6">Aluguel</h2>
          <div class="space-y-3">
            <button
              class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg"
            >
              Listar Aluguéis
            </button>
            <button
              class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg"
            >
              Novo Aluguel
            </button>
            <button
              class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg"
            >
              Devolução
            </button>
          </div>
        </div>
      </div>
    </div>
    <ProfileEditor
      v-if="showProfileEditor"
      :userId="user.uuid"
      :userRole="user.role"
      :showPasswordField="true"
      :canEditRole="false"
      @close="handleProfileEditorClose"
      @save="handleProfileSaved"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "@/composables/useAuth";
import apiClient from "@/services/apiClient";
import ProfileEditor from "@/components/shared/ProfileEditor.vue";

const showProfileEditor = ref(false);
const totalClients = ref(0);
const activeRentals = ref(0);
const lateReturns = ref(0);
const router = useRouter();
const { state, logout } = useAuth();
const user = state.user;

const clients = ref([]);
const searchQuery = ref("");
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = ref(15);
let searchTimeout = null;

const handleLogout = async () => {
  await logout();
  router.push("/login");
};

const handleProfileEditorClose = () => {
  showProfileEditor.value = false;
};

const loadTotalClients = async () => {
  try {
    const response = await apiClient.get("total-clients");
    totalClients.value = response.data.data?.total_clients || 0;
  } catch (error) {
    console.error("Erro ao carregar total de clientes:", error);
  }
};

const loadActiveRentals = async () => {
  try {
    const response = await apiClient.get("totals-rentals-active");
    activeRentals.value = response.data.total_active_rentals || 0;
  } catch (error) {
    console.error("Erro ao carregar locações ativas:", error);
  }
};

const loadLateReturns = async () => {
  try {
    const response = await apiClient.get("totals-late-returns");
    lateReturns.value = response.data.total_late_returns || 0;
  } catch (error) {
    console.error("Erro ao carregar locações atrasadas:", error);
  }
};

const loadClients = async (page = 1, search = "") => {
  try {
    const params = {
      page: page,
      per_page: perPage.value,
      role: "cliente",
    };

    if (search) {
      params.name = search;
    }

    const response = await apiClient.get("users", { params });

    clients.value = response.data.data || [];
    currentPage.value = response.data.meta?.current_page || 1;
    totalPages.value = response.data.meta?.last_page || 1;
    perPage.value = response.data.meta?.per_page || 15;
  } catch (error) {
    console.error("Erro ao carregar clientes:", error);
    clients.value = [];
  }
};

const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    loadClients(1, searchQuery.value);
  }, 500);
};

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    loadClients(currentPage.value, searchQuery.value);
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
    loadClients(currentPage.value, searchQuery.value);
  }
};

const openNewClientModal = () => {
  // TODO: Implementar modal de novo cliente
  alert("Funcionalidade de novo cliente em desenvolvimento");
};

const editClient = (client) => {
  // TODO: Implementar edição de cliente
  console.log("Editar cliente:", client);
  alert(`Editar cliente: ${client.name}`);
};

const deleteClient = async (clientId) => {
  if (confirm("Tem certeza que deseja remover este cliente?")) {
    try {
      await apiClient.delete(`users/${clientId}`);
      alert("Cliente removido com sucesso");
      loadClients(currentPage.value, searchQuery.value);
    } catch (error) {
      console.error("Erro ao remover cliente:", error);
      alert("Erro ao remover cliente");
    }
  }
};

onMounted(() => {
  loadTotalClients();
  loadActiveRentals();
  loadLateReturns();
  loadClients();
});
</script>
