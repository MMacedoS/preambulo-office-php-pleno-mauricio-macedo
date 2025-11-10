<template>
  <div class="min-h-screen bg-linear-to-br from-purple-50 to-purple-100 p-8">
    <div class="max-w-7xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-4xl font-bold text-gray-800">
            Painel Administrativo
          </h1>
          <p class="text-gray-600 mt-2">{{ user.name }} • Administrador</p>
        </div>
        <button
          @click="handleLogout"
          class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg"
        >
          Sair
        </button>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm">Faturamento Total</p>
          <p class="text-3xl font-bold text-blue-600">
            R$ {{ parseFloat(totalRevenue).toFixed(2) }}
          </p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm">Aluguéis Atrasados</p>
          <p class="text-3xl font-bold text-red-600">
            {{ lateReturns }}
          </p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm">Multas Pendentes</p>
          <p class="text-3xl font-bold text-orange-600">
            R$ {{ parseFloat(pendingPenalties).toFixed(2) }}
          </p>
        </div>
      </div>

      <div class="mb-6 bg-white rounded-lg shadow">
        <div class="flex border-b border-gray-200">
          <button
            @click="activeTab = 'usuarios'"
            :class="[
              'flex-1 py-4 px-6 font-semibold text-center transition-all',
              activeTab === 'usuarios'
                ? 'text-purple-600 border-b-2 border-purple-600 bg-purple-50'
                : 'text-gray-600 hover:text-gray-800',
            ]"
          >
            <div class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
              </svg>
              Usuários
            </div>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-6">
        <div
          v-if="activeTab === 'usuarios'"
          class="bg-white rounded-lg shadow-lg p-6"
        >
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Usuários</h2>
            <button
              @click="openNewUserModal"
              class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
            >
              <span>+</span>
              Novo Usuário
            </button>
          </div>

          <div class="mb-6">
            <input
              v-model="searchUsersQuery"
              @input="debounceSearchUsers"
              type="text"
              placeholder="Buscar por nome..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
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
                <tr v-if="users.length === 0" class="text-center">
                  <td colspan="4" class="py-6 text-gray-500">
                    Nenhum usuário encontrado
                  </td>
                </tr>
                <tr
                  v-for="user_item in users"
                  :key="user_item.id"
                  class="border-b border-gray-200 hover:bg-gray-50"
                >
                  <td class="py-3 px-4">{{ user_item.name }}</td>
                  <td class="py-3 px-4">{{ user_item.email }}</td>
                  <td class="py-3 px-4">
                    <span
                      class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                      :class="{
                        'bg-purple-100 text-purple-800':
                          user_item.role === 'administrador',
                        'bg-blue-100 text-blue-800':
                          user_item.role === 'atendente',
                      }"
                    >
                      {{ user_item.role }}
                    </span>
                  </td>
                  <td class="py-3 px-4 text-center">
                    <div class="flex gap-2 justify-center">
                      <button
                        @click="editUser(user_item)"
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
                        @click="deleteUser(user_item.id)"
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
              Página {{ currentUsersPage }} de {{ totalUsersPages }}
            </p>
            <div class="flex gap-2">
              <button
                @click="previousUsersPage"
                :disabled="currentUsersPage === 1"
                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Anterior
              </button>
              <button
                @click="nextUsersPage"
                :disabled="currentUsersPage === totalUsersPages"
                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Próximo
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal
      :isOpen="showUserFormModal"
      :title="userFormMode === 'create' ? 'Novo Usuário' : 'Editar Usuário'"
      @close="handleUserFormClose"
    >
      <ClientForm
        :client="selectedUser"
        :canEditRole="true"
        :includePasswordField="true"
        @cancel="handleUserFormClose"
        @submit="handleUserFormSubmit"
        @success="handleUserFormSuccess"
        @error="handleUserFormError"
      />
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "@/composables/useAuth";
import apiClient from "@/services/apiClient";
import Modal from "@/components/shared/Modal.vue";
import ClientForm from "@/components/shared/ClientForm.vue";

const router = useRouter();
const { state, logout } = useAuth();
const user = state.user;

const activeTab = ref("usuarios");

const totalRevenue = ref(0);
const lateReturns = ref(0);
const pendingPenalties = ref(0);

const users = ref([]);
const searchUsersQuery = ref("");
const currentUsersPage = ref(1);
const totalUsersPages = ref(1);
const perPageUsers = ref(10);
let searchUsersTimeout = null;

const showUserFormModal = ref(false);
const userFormMode = ref("create");
const selectedUser = ref(null);

const loadTotalRevenue = async () => {
  try {
    const response = await apiClient.get("totals-revenue");
    totalRevenue.value = response.data.total_revenue || 0;
  } catch (error) {
    console.error("Erro ao carregar faturamento total:", error);
  }
};

const loadLateReturns = async () => {
  try {
    const response = await apiClient.get("totals-late-returns");
    lateReturns.value = response.data.total_late_returns || 0;
  } catch (error) {
    console.error("Erro ao carregar aluguéis atrasados:", error);
  }
};

const loadPendingPenalties = async () => {
  try {
    const response = await apiClient.get("totals-pending");
    pendingPenalties.value = response.data.total_pending || 0;
  } catch (error) {
    console.error("Erro ao carregar multas pendentes:", error);
  }
};

const handleLogout = async () => {
  await logout();
  router.push("/login");
};

const loadUsers = async (page = 1, search = "") => {
  try {
    const params = {
      page: page,
      per_page: perPageUsers.value,
    };

    const roleFilter = ["atendente", "administrador"];

    if (search) {
      params.name = search;
    }

    const response = await apiClient.get("users", { params });

    users.value = (response.data.data || []).filter((user) =>
      roleFilter.includes(user.role)
    );
    currentUsersPage.value = response.data.meta?.current_page || 1;
    totalUsersPages.value = response.data.meta?.last_page || 1;
    perPageUsers.value = response.data.meta?.per_page || 10;
  } catch (error) {
    console.error("Erro ao carregar usuários:", error);
    users.value = [];
  }
};

const debounceSearchUsers = () => {
  clearTimeout(searchUsersTimeout);
  searchUsersTimeout = setTimeout(() => {
    currentUsersPage.value = 1;
    loadUsers(1, searchUsersQuery.value);
  }, 500);
};

const previousUsersPage = () => {
  if (currentUsersPage.value > 1) {
    currentUsersPage.value--;
    loadUsers(currentUsersPage.value, searchUsersQuery.value);
  }
};

const nextUsersPage = () => {
  if (currentUsersPage.value < totalUsersPages.value) {
    currentUsersPage.value++;
    loadUsers(currentUsersPage.value, searchUsersQuery.value);
  }
};

const openNewUserModal = () => {
  userFormMode.value = "create";
  selectedUser.value = null;
  showUserFormModal.value = true;
};

const editUser = (user_item) => {
  userFormMode.value = "edit";
  selectedUser.value = user_item;
  showUserFormModal.value = true;
};

const deleteUser = async (userId) => {
  if (confirm("Tem certeza que deseja remover este usuário?")) {
    try {
      await apiClient.delete(`users/${userId}`);
      alert("Usuário removido com sucesso");
      loadUsers(currentUsersPage.value, searchUsersQuery.value);
    } catch (error) {
      console.error("Erro ao remover usuário:", error);
      alert("Erro ao remover usuário");
    }
  }
};

const handleUserFormClose = () => {
  showUserFormModal.value = false;
  selectedUser.value = null;
  userFormMode.value = "create";
};

const handleUserFormSubmit = (data) => {
  console.log("Usuário salvo:", data);
};

const handleUserFormSuccess = ({ message }) => {
  loadUsers(currentUsersPage.value, searchUsersQuery.value);
  handleUserFormClose();
};

const handleUserFormError = ({ message }) => {
  console.error("Erro no formulário:", message);
};

onMounted(() => {
  loadTotalRevenue();
  loadLateReturns();
  loadPendingPenalties();
  loadUsers();
});
</script>
