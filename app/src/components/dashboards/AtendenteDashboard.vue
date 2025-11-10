<template>
  <div class="min-h-screen bg-gradient-to-br from-green-50 to-green-100 p-8">
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

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm">Total de Clientes</p>
          <p class="text-3xl font-bold text-blue-600">1.245</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm">Locações Ativas</p>
          <p class="text-3xl font-bold text-green-600">342</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
          <h2 class="text-2xl font-semibold text-gray-800 mb-6">Clientes</h2>
          <div class="space-y-3">
            <button
              class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg"
            >
              Listar Clientes
            </button>
            <button
              class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg"
            >
              Novo Cliente
            </button>
            <button
              class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg"
            >
              Editar Cliente
            </button>
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
import ProfileEditor from "@/components/shared/ProfileEditor.vue";

const showProfileEditor = ref(false);
const router = useRouter();
const { state, logout } = useAuth();
const user = state.user;

const handleLogout = async () => {
  await logout();
  router.push("/login");
};

const handleProfileEditorClose = () => {
  showProfileEditor.value = false;
};
</script>
