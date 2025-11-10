<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 p-8">
    <div class="max-w-7xl mx-auto">
      <template v-if="!showDetails">
        <div class="flex justify-between items-center mb-8">
          <div>
            <h1 class="text-4xl font-bold text-gray-800">
              Olá, {{ user.name }}
            </h1>
            <p class="text-gray-600 mt-2">Bem-vindo ao seu painel de cliente</p>
          </div>
          <button
            @click="handleLogout"
            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg"
          >
            Sair
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Catálogo</h2>
            <p class="text-gray-600 mb-4">
              Visualize nossos filmes disponíveis
            </p>
            <button
              class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg"
            >
              Ver Catálogo
            </button>
          </div>

          <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
              Minhas Locações
            </h2>
            <p class="text-gray-600 mb-4">Acompanhe suas locações ativas</p>
            <button
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
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "@/composables/useAuth";
import apiClient from "@/services/apiClient";
import Accordion from "@/components/shared/Accordion.vue";

const router = useRouter();
const { state, logout } = useAuth();
const user = state.user;

const activeRentalsCount = ref(0);
const lateRentalsCount = ref(0);
const rentals = ref([]);
const showDetails = ref(false);

const accordionItems = computed(() =>
  rentals.value.map((rental) => ({
    title: `#${rental.code}`,
    ...rental,
  }))
);

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString("pt-BR");
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

const handleLogout = async () => {
  await logout();
  router.push("/login");
};

onMounted(() => {
  loadRentals();
});
</script>
