<template>
  <div class="min-h-screen bg-linear-to-br from-blue-50 to-blue-100 p-8">
    <div class="max-w-7xl mx-auto">
      <template v-if="showCatalog">
        <CatalogoDashboard @back="showCatalog = false" />
      </template>
      <template v-else-if="!showDetails">
        <div class="flex justify-between items-center mb-8">
          <div>
            <h1 class="text-4xl font-bold text-gray-800">
              Olá, {{ user.name }}
            </h1>
            <p class="text-gray-600 mt-2">Bem-vindo ao seu painel de cliente</p>
          </div>
          <div class="flex gap-3">
            <button
              @click="showProfileEditor = true"
              class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"
            >
              Editar Perfil
            </button>
            <button
              @click="handleLogout"
              class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg"
            >
              Sair
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Catálogo</h2>
            <p class="text-gray-600 mb-4">
              Visualize nossos filmes disponíveis
            </p>
            <button
              @click="showCatalog = true"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg"
            >
              Ver Catálogo
            </button>
          </div>

          <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
              Minhas Locações
            </h2>
            <p class="text-gray-600 mb-4">Acompanhe todas as suas locações</p>
            <button
              @click="handleShowHistory"
              class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg"
            >
              Ver Locações
            </button>
          </div>

          <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Status</h2>
            <div class="space-y-3">
              <div class="bg-blue-50 p-3 rounded">
                <p class="text-gray-600 text-sm">Locações Ativas</p>
                <p class="text-2xl font-bold text-blue-600">
                  {{ activeRentalsCount }}
                </p>
              </div>
              <div class="bg-red-50 p-3 rounded">
                <p class="text-gray-600 text-sm">Locações Atrasadas</p>
                <p class="text-2xl font-bold text-red-600">
                  {{ lateRentalsCount }}
                </p>
              </div>
            </div>
            <button
              @click="showDetails = true"
              class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg mt-4"
            >
              Ver Detalhes
            </button>
          </div>
        </div>
      </template>

      <template v-else>
        <div class="flex justify-between items-center mb-8">
          <div>
            <h1 class="text-4xl font-bold text-gray-800">Locações Atuais</h1>
            <p class="text-gray-600 mt-2">
              Acompanhe todas as suas locações ativas e atrasadas
            </p>
          </div>
          <button
            @click="showDetails = false"
            class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg"
          >
            Voltar
          </button>
        </div>

        <Accordion :items="accordionItems">
          <template #header="{ item }">
            <div class="flex items-center justify-between w-full">
              <span class="font-semibold text-gray-800">#{{ item.code }} </span>
              <span class="text-lg"
                >{{ item.movies.length }} filmes alugados</span
              >
              <span
                :class="
                  item.status === 'ativo'
                    ? 'bg-green-100 text-green-800 animate-pulse'
                    : 'bg-red-100 text-red-800 animate-pulse'
                "
                class="px-3 py-1 rounded-full text-xs font-semibold"
              >
                {{ item.status === "ativo" ? "Ativa" : "Atrasada" }}
              </span>
            </div>
          </template>
          <template #content="{ item }">
            <div class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-gray-600">Data da Locação</p>
                  <p class="font-semibold text-gray-800">
                    {{ formatDate(item.rental_date) }}
                  </p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Data de Devolução</p>
                  <p class="font-semibold text-gray-800">
                    {{ formatDate(item.return_date) }}
                  </p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Valor Total</p>
                  <p class="font-semibold text-gray-800">
                    R$ {{ parseFloat(item.total_value).toFixed(2) }}
                  </p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Multa</p>
                  <p class="font-semibold text-red-600">
                    R$ {{ parseFloat(item.penalty).toFixed(2) }}
                  </p>
                </div>
              </div>
              <div>
                <p class="text-sm text-gray-600 mb-2">Filmes Alugados</p>
                <div class="space-y-2">
                  <div
                    v-for="movie in item.movies"
                    :key="movie.id"
                    class="bg-blue-50 p-3 rounded border border-blue-100"
                  >
                    <p class="font-semibold text-gray-800">{{ movie.title }}</p>
                    <p class="text-sm text-gray-600">
                      Gênero: {{ movie.genre }}
                    </p>
                    <p class="text-sm text-gray-600">
                      Aluguel: R$
                      {{ parseFloat(movie.rental_price).toFixed(2) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </Accordion>
      </template>

      <template v-if="showRentalsHistory">
        <div
          class="fixed inset-0 bg-linear-to-br from-blue-50 to-blue-100 p-8 z-40 overflow-y-auto"
        >
          <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-8">
              <div>
                <h1 class="text-4xl font-bold text-gray-800">
                  Histórico de Locações
                </h1>
                <p class="text-gray-600 mt-2">
                  Todas as suas locações (ativas, atrasadas e devolvidas)
                </p>
              </div>
              <button
                @click="showRentalsHistory = false"
                class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg"
              >
                Voltar
              </button>
            </div>

            <RentalsFilter
              @apply="handleApplyFilters"
              @reset="handleResetFilters"
            />

            <Accordion :items="historyItems">
              <template #header="{ item }">
                <div class="flex items-center justify-between w-full gap-4">
                  <span class="font-semibold text-gray-800 min-w-fit"
                    >#{{ item.code }}</span
                  >
                  <span class="text-lg min-w-fit"
                    >{{ item.movies.length }} filmes</span
                  >
                  <span
                    :class="getStatusClass(item.status)"
                    class="px-3 py-1 rounded-full text-xs font-semibold min-w-fit"
                  >
                    {{ getStatusLabel(item.status) }}
                  </span>
                  <span class="text-sm text-gray-600 ml-auto">{{
                    formatDate(item.return_date)
                  }}</span>
                </div>
              </template>
              <template #content="{ item }">
                <div class="space-y-4">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <p class="text-sm text-gray-600">Data da Locação</p>
                      <p class="font-semibold text-gray-800">
                        {{ formatDate(item.rental_date) }}
                      </p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-600">Data de Devolução</p>
                      <p class="font-semibold text-gray-800">
                        {{ formatDate(item.return_date) }}
                      </p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-600">Valor Total</p>
                      <p class="font-semibold text-gray-800">
                        R$ {{ parseFloat(item.total_value).toFixed(2) }}
                      </p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-600">Multa</p>
                      <p class="font-semibold text-red-600">
                        R$ {{ parseFloat(item.penalty).toFixed(2) }}
                      </p>
                    </div>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600 mb-2">Filmes Alugados</p>
                    <div class="space-y-2">
                      <div
                        v-for="movie in item.movies"
                        :key="movie.id"
                        class="bg-blue-50 p-3 rounded border border-blue-100"
                      >
                        <p class="font-semibold text-gray-800">
                          {{ movie.title }}
                        </p>
                        <p class="text-sm text-gray-600">
                          Gênero: {{ movie.genre }}
                        </p>
                        <p class="text-sm text-gray-600">
                          Aluguel: R$
                          {{ parseFloat(movie.rental_price).toFixed(2) }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </template>
            </Accordion>

            <div v-if="historyMeta" class="mt-8 flex justify-center gap-2">
              <button
                v-for="page in historyMeta.last_page"
                :key="page"
                @click="loadRentalsHistory(page)"
                :class="
                  page === currentHistoryPage
                    ? 'bg-blue-600 text-white'
                    : 'bg-white text-gray-800'
                "
                class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-blue-100"
              >
                {{ page }}
              </button>
            </div>
          </div>
        </div>
      </template>
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
import Accordion from "@/components/shared/Accordion.vue";
import RentalsFilter from "@/components/shared/RentalsFilter.vue";
import CatalogoDashboard from "@/components/dashboards/CatalogoDashboard.vue";
import ProfileEditor from "@/components/shared/ProfileEditor.vue";

const router = useRouter();
const { state, logout } = useAuth();
const user = state.user;

const activeRentalsCount = ref(0);
const lateRentalsCount = ref(0);
const rentals = ref([]);
const showDetails = ref(false);
const showRentalsHistory = ref(false);
const showCatalog = ref(false);
const showProfileEditor = ref(false);
const rentalsHistory = ref([]);
const historyMeta = ref(null);
const currentHistoryPage = ref(1);
const currentFilters = ref({});

const accordionItems = computed(() =>
  rentals.value.map((rental) => ({
    title: `#${rental.code}`,
    ...rental,
  }))
);

const historyItems = computed(() =>
  rentalsHistory.value.map((rental) => ({
    title: `#${rental.code}`,
    ...rental,
  }))
);

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString("pt-BR");
};

const getStatusLabel = (status) => {
  const statusMap = {
    ativo: "Ativa",
    atrasado: "Atrasada",
    devolvido: "Devolvido",
  };
  return statusMap[status] || status;
};

const getStatusClass = (status) => {
  const classMap = {
    ativo: "bg-green-100 text-green-800 animate-pulse",
    atrasado: "bg-red-100 text-red-800 animate-pulse",
    devolvido: "bg-blue-100 text-blue-800",
  };
  return classMap[status] || "";
};

const loadRentals = async () => {
  try {
    const response = await apiClient.get("rentals/info-rentals");
    rentals.value = response.data.data || [];

    activeRentalsCount.value = rentals.value.filter(
      (rental) => rental.status === "ativo"
    ).length;
    lateRentalsCount.value = rentals.value.filter(
      (rental) => rental.status === "atrasado"
    ).length;
  } catch (error) {
    console.error("Erro ao carregar locações:", error);
  }
};

const loadRentalsHistory = async (page = 1) => {
  try {
    const params = {
      page,
      ...currentFilters.value,
    };
    const response = await apiClient.get("rentals/history", { params });
    rentalsHistory.value = response.data.data || [];
    historyMeta.value = response.data.meta || null;
    currentHistoryPage.value = page;
  } catch (error) {
    console.error("Erro ao carregar histórico de locações:", error);
  }
};

const handleLogout = async () => {
  await logout();
  router.push("/login");
};

const handleShowHistory = () => {
  showRentalsHistory.value = true;
  loadRentalsHistory(1);
};

const handleApplyFilters = (filters) => {
  currentFilters.value = Object.fromEntries(
    Object.entries(filters).filter(([, value]) => value)
  );
  loadRentalsHistory(1);
};

const handleResetFilters = () => {
  currentFilters.value = {};
  loadRentalsHistory(1);
};

const handleProfileEditorClose = () => {
  showProfileEditor.value = false;
};

const handleProfileSaved = (updatedUser) => {
  user.name = updatedUser.name;
  user.email = updatedUser.email;
};

onMounted(() => {
  loadRentals();
});
</script>
