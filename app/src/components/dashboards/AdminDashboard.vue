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

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Usuários</h2>
          <button
            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded"
          >
            Gerenciar Usuários
          </button>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Filmes</h2>
          <button
            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded"
          >
            Gerenciar Filmes
          </button>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Clientes</h2>
          <button
            class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded"
          >
            Gerenciar Clientes
          </button>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Aluguel</h2>
          <button
            class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-3 rounded"
          >
            Gerenciar Aluguéis
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "@/composables/useAuth";
import apiClient from "@/services/apiClient";

const router = useRouter();
const { state, logout } = useAuth();
const user = state.user;

const totalRevenue = ref(0);
const lateReturns = ref(0);
const pendingPenalties = ref(0);

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

onMounted(() => {
  loadTotalRevenue();
  loadLateReturns();
  loadPendingPenalties();
});
</script>
