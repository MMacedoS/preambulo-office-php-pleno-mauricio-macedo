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
          <button
            @click="activeTab = 'clientes'"
            :class="[
              'flex-1 py-4 px-6 font-semibold text-center transition-all',
              activeTab === 'clientes'
                ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50'
                : 'text-gray-600 hover:text-gray-800',
            ]"
          >
            <div class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path
                  d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v4h8v-4zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                />
              </svg>
              Clientes
            </div>
          </button>
          <button
            @click="activeTab = 'filmes'"
            :class="[
              'flex-1 py-4 px-6 font-semibold text-center transition-all',
              activeTab === 'filmes'
                ? 'text-green-600 border-b-2 border-green-600 bg-green-50'
                : 'text-gray-600 hover:text-gray-800',
            ]"
          >
            <div class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path
                  d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm4 2v4h8V8H6z"
                />
              </svg>
              Filmes
            </div>
          </button>
          <button
            @click="activeTab = 'locacoes'"
            :class="[
              'flex-1 py-4 px-6 font-semibold text-center transition-all',
              activeTab === 'locacoes'
                ? 'text-orange-600 border-b-2 border-orange-600 bg-orange-50'
                : 'text-gray-600 hover:text-gray-800',
            ]"
          >
            <div class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path
                  d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                />
              </svg>
              Locações
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

        <div
          v-if="activeTab === 'clientes'"
          class="bg-white rounded-lg shadow-lg p-6"
        >
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
              v-model="searchClientsQuery"
              @input="debounceSearchClients"
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
                      class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800"
                    >
                      cliente
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
              Página {{ currentClientsPage }} de {{ totalClientsPages }}
            </p>
            <div class="flex gap-2">
              <button
                @click="previousClientsPage"
                :disabled="currentClientsPage === 1"
                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Anterior
              </button>
              <button
                @click="nextClientsPage"
                :disabled="currentClientsPage === totalClientsPages"
                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Próximo
              </button>
            </div>
          </div>
        </div>

        <div
          v-if="activeTab === 'filmes'"
          class="bg-white rounded-lg shadow-lg p-6"
        >
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Filmes</h2>
            <button
              @click="openNewMovieModal"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
            >
              <span>+</span>
              Novo Filme
            </button>
          </div>

          <div class="mb-6">
            <input
              v-model="searchMoviesQuery"
              @input="debounceSearchMovies"
              type="text"
              placeholder="Buscar por título..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            />
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-300">
                  <th class="text-left py-3 px-4 font-semibold text-gray-700">
                    Título
                  </th>
                  <th class="text-left py-3 px-4 font-semibold text-gray-700">
                    Preço
                  </th>
                  <th class="text-left py-3 px-4 font-semibold text-gray-700">
                    Estoque
                  </th>
                  <th class="text-center py-3 px-4 font-semibold text-gray-700">
                    Ações
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="movies.length === 0" class="text-center">
                  <td colspan="4" class="py-6 text-gray-500">
                    Nenhum filme encontrado
                  </td>
                </tr>
                <tr
                  v-for="movie in movies"
                  :key="movie.id"
                  class="border-b border-gray-200 hover:bg-gray-50"
                >
                  <td class="py-3 px-4">{{ movie.title }}</td>
                  <td class="py-3 px-4">
                    R$ {{ parseFloat(movie.rental_price).toFixed(2) }}
                  </td>
                  <td class="py-3 px-4">
                    <span
                      class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                      :class="
                        movie.quantity > 0
                          ? 'bg-green-100 text-green-800'
                          : 'bg-red-100 text-red-800'
                      "
                    >
                      {{ movie.quantity }}
                    </span>
                  </td>
                  <td class="py-3 px-4 text-center">
                    <div class="flex gap-2 justify-center">
                      <button
                        @click="editMovie(movie)"
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
                        @click="deleteMovie(movie.id)"
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
              Página {{ currentMoviesPage }} de {{ totalMoviesPages }}
            </p>
            <div class="flex gap-2">
              <button
                @click="previousMoviesPage"
                :disabled="currentMoviesPage === 1"
                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Anterior
              </button>
              <button
                @click="nextMoviesPage"
                :disabled="currentMoviesPage === totalMoviesPages"
                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Próximo
              </button>
            </div>
          </div>
        </div>

        <div v-if="activeTab === 'locacoes'" class="bg-white rounded-lg shadow-lg p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Locações</h2>
            <button
              @click="openNewRentalModal"
              class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
            >
              <span>+</span>
              Nova Locação
            </button>
          </div>

          <div class="mb-6">
            <input
              v-model="searchRentalsQuery"
              @input="debounceSearchRentals"
              type="text"
              placeholder="Buscar por cliente..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
            />
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-300">
                  <th class="text-left py-3 px-4 font-semibold text-gray-700">Cliente</th>
                  <th class="text-left py-3 px-4 font-semibold text-gray-700">Filme</th>
                  <th class="text-left py-3 px-4 font-semibold text-gray-700">Data Início</th>
                  <th class="text-left py-3 px-4 font-semibold text-gray-700">Data Fim</th>
                  <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                  <th class="text-center py-3 px-4 font-semibold text-gray-700">Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="rentals.length === 0" class="text-center">
                  <td colspan="6" class="py-6 text-gray-500">Nenhuma locação encontrada</td>
                </tr>
                <tr v-for="rental in rentals" :key="rental.id" class="border-b border-gray-200 hover:bg-gray-50">
                  <td class="py-3 px-4">{{ rental.user?.name || 'N/A' }}</td>
                  <td class="py-3 px-4">{{ rental.movie?.title || 'N/A' }}</td>
                  <td class="py-3 px-4">{{ formatDate(rental.rental_date) }}</td>
                  <td class="py-3 px-4">{{ formatDate(rental.return_date) }}</td>
                  <td class="py-3 px-4">
                    <span
                      class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                      :class="{
                        'bg-green-100 text-green-800': rental.status === 'returned',
                        'bg-yellow-100 text-yellow-800': rental.status === 'active',
                        'bg-red-100 text-red-800': rental.status === 'late',
                      }"
                    >
                      {{ rental.status === 'returned' ? 'Devolvido' : rental.status === 'active' ? 'Ativo' : 'Atrasado' }}
                    </span>
                  </td>
                  <td class="py-3 px-4 text-center">
                    <div class="flex gap-2 justify-center">
                      <button
                        @click="editRental(rental)"
                        title="Editar"
                        class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded-lg"
                      >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                      </button>
                      <button
                        @click="deleteRental(rental.id)"
                        title="Remover"
                        class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg"
                      >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
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
              Página {{ currentRentalsPage }} de {{ totalRentalsPages }}
            </p>
            <div class="flex gap-2">
              <button
                @click="previousRentalsPage"
                :disabled="currentRentalsPage === 1"
                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Anterior
              </button>
              <button
                @click="nextRentalsPage"
                :disabled="currentRentalsPage === totalRentalsPages"
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
      :isOpen="showClientFormModal"
      :title="clientFormMode === 'create' ? 'Novo Cliente' : 'Editar Cliente'"
      @close="handleClientFormClose"
    >
      <ClientForm
        :client="selectedClient"
        :canEditRole="false"
        :includePasswordField="true"
        @cancel="handleClientFormClose"
        @submit="handleClientFormSubmit"
        @success="handleClientFormSuccess"
        @error="handleClientFormError"
      />
    </Modal>

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

    <Modal
      :isOpen="showMovieFormModal"
      :title="movieFormMode === 'create' ? 'Novo Filme' : 'Editar Filme'"
      @close="handleMovieFormClose"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2"
            >Título</label
          >
          <input
            v-model="selectedMovie.title"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            placeholder="Título do filme"
          />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2"
            >Preço</label
          >
          <input
            v-model.number="selectedMovie.rental_price"
            type="number"
            step="0.01"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            placeholder="0.00"
          />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2"
            >Estoque</label
          >
          <input
            v-model.number="selectedMovie.quantity"
            type="number"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            placeholder="0"
          />
        </div>
        <div class="flex gap-2 justify-end">
          <button
            @click="handleMovieFormClose"
            class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg"
          >
            Cancelar
          </button>
          <button
            @click="handleMovieFormSubmit(selectedMovie)"
            class="px-4 py-2 text-white bg-green-600 hover:bg-green-700 rounded-lg"
          >
            Salvar
          </button>
        </div>
      </div>
    </Modal>

    <Modal
      :isOpen="showRentalFormModal"
      :title="rentalFormMode === 'create' ? 'Nova Locação' : 'Editar Locação'"
      @close="handleRentalFormClose"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Cliente</label>
          <select
            v-model.number="selectedRental.user_id"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
          >
            <option value="">Selecione um cliente</option>
            <option v-for="client in clients" :key="client.id" :value="client.id">
              {{ client.name }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Filme</label>
          <select
            v-model.number="selectedRental.movie_id"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
          >
            <option value="">Selecione um filme</option>
            <option v-for="movie in movies" :key="movie.id" :value="movie.id">
              {{ movie.title }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Data de Início</label>
          <input
            v-model="selectedRental.rental_date"
            type="date"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
          />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Data de Retorno</label>
          <input
            v-model="selectedRental.return_date"
            type="date"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
          />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
          <select
            v-model="selectedRental.status"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
          >
            <option value="active">Ativo</option>
            <option value="returned">Devolvido</option>
            <option value="late">Atrasado</option>
          </select>
        </div>
        <div class="flex gap-2 justify-end">
          <button
            @click="handleRentalFormClose"
            class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg"
          >
            Cancelar
          </button>
          <button
            @click="handleRentalFormSubmit(selectedRental)"
            class="px-4 py-2 text-white bg-orange-600 hover:bg-orange-700 rounded-lg"
          >
            Salvar
          </button>
        </div>
      </div>
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

const clients = ref([]);
const searchClientsQuery = ref("");
const currentClientsPage = ref(1);
const totalClientsPages = ref(1);
const perPageClients = ref(10);
const showClientFormModal = ref(false);
const clientFormMode = ref("create");
const selectedClient = ref(null);

const movies = ref([]);
const searchMoviesQuery = ref("");
const currentMoviesPage = ref(1);
const totalMoviesPages = ref(1);
const perPageMovies = ref(10);
const showMovieFormModal = ref(false);
const movieFormMode = ref("create");
const selectedMovie = ref(null);

const rentals = ref([]);
const searchRentalsQuery = ref("");
const currentRentalsPage = ref(1);
const totalRentalsPages = ref(1);
const perPageRentals = ref(10);
const showRentalFormModal = ref(false);
const rentalFormMode = ref("create");
const selectedRental = ref(null);

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

const formatDate = (date) => {
  if (!date) return "N/A";
  return new Date(date).toLocaleDateString("pt-BR");
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

const loadClients = async (page = 1, search = "") => {
  try {
    const params = {
      page: page,
      per_page: perPageClients.value,
      role: "cliente",
    };

    if (search) {
      params.name = search;
    }

    const response = await apiClient.get("users", { params });

    clients.value = response.data.data || [];
    currentClientsPage.value = response.data.meta?.current_page || 1;
    totalClientsPages.value = response.data.meta?.last_page || 1;
    perPageClients.value = response.data.meta?.per_page || 10;
  } catch (error) {
    console.error("Erro ao carregar clientes:", error);
    clients.value = [];
  }
};

let searchClientsTimeout = null;

const debounceSearchClients = () => {
  clearTimeout(searchClientsTimeout);
  searchClientsTimeout = setTimeout(() => {
    currentClientsPage.value = 1;
    loadClients(1, searchClientsQuery.value);
  }, 500);
};

const previousClientsPage = () => {
  if (currentClientsPage.value > 1) {
    currentClientsPage.value--;
    loadClients(currentClientsPage.value, searchClientsQuery.value);
  }
};

const nextClientsPage = () => {
  if (currentClientsPage.value < totalClientsPages.value) {
    currentClientsPage.value++;
    loadClients(currentClientsPage.value, searchClientsQuery.value);
  }
};

const openNewClientModal = () => {
  clientFormMode.value = "create";
  selectedClient.value = null;
  showClientFormModal.value = true;
};

const editClient = (client) => {
  clientFormMode.value = "edit";
  selectedClient.value = client;
  showClientFormModal.value = true;
};

const deleteClient = async (clientId) => {
  if (confirm("Tem certeza que deseja remover este cliente?")) {
    try {
      await apiClient.delete(`users/${clientId}`);
      alert("Cliente removido com sucesso");
      loadClients(currentClientsPage.value, searchClientsQuery.value);
    } catch (error) {
      console.error("Erro ao remover cliente:", error);
      alert("Erro ao remover cliente");
    }
  }
};

const handleClientFormClose = () => {
  showClientFormModal.value = false;
  selectedClient.value = null;
  clientFormMode.value = "create";
};

const handleClientFormSubmit = (data) => {
  console.log("Cliente salvo:", data);
};

const handleClientFormSuccess = ({ message }) => {
  loadClients(currentClientsPage.value, searchClientsQuery.value);
  handleClientFormClose();
};

const handleClientFormError = ({ message }) => {
  console.error("Erro no formulário:", message);
};

const loadMovies = async (page = 1, search = "") => {
  try {
    const params = {
      page: page,
      per_page: perPageMovies.value,
    };

    if (search) {
      params.title = search;
    }

    const response = await apiClient.get("movies", { params });

    movies.value = response.data.data || [];
    currentMoviesPage.value = response.data.meta?.current_page || 1;
    totalMoviesPages.value = response.data.meta?.last_page || 1;
    perPageMovies.value = response.data.meta?.per_page || 10;
  } catch (error) {
    console.error("Erro ao carregar filmes:", error);
    movies.value = [];
  }
};

let searchMoviesTimeout = null;

const debounceSearchMovies = () => {
  clearTimeout(searchMoviesTimeout);
  searchMoviesTimeout = setTimeout(() => {
    currentMoviesPage.value = 1;
    loadMovies(1, searchMoviesQuery.value);
  }, 500);
};

const previousMoviesPage = () => {
  if (currentMoviesPage.value > 1) {
    currentMoviesPage.value--;
    loadMovies(currentMoviesPage.value, searchMoviesQuery.value);
  }
};

const nextMoviesPage = () => {
  if (currentMoviesPage.value < totalMoviesPages.value) {
    currentMoviesPage.value++;
    loadMovies(currentMoviesPage.value, searchMoviesQuery.value);
  }
};

const openNewMovieModal = () => {
  movieFormMode.value = "create";
  selectedMovie.value = null;
  showMovieFormModal.value = true;
};

const editMovie = (movie) => {
  movieFormMode.value = "edit";
  selectedMovie.value = movie;
  showMovieFormModal.value = true;
};

const deleteMovie = async (movieId) => {
  if (confirm("Tem certeza que deseja remover este filme?")) {
    try {
      await apiClient.delete(`movies/${movieId}`);
      alert("Filme removido com sucesso");
      loadMovies(currentMoviesPage.value, searchMoviesQuery.value);
    } catch (error) {
      console.error("Erro ao remover filme:", error);
      alert("Erro ao remover filme");
    }
  }
};

const handleMovieFormClose = () => {
  showMovieFormModal.value = false;
  selectedMovie.value = null;
  movieFormMode.value = "create";
};

const handleMovieFormSubmit = async (data) => {
  try {
    if (movieFormMode.value === "create") {
      await apiClient.post("movies", data);
      alert("Filme criado com sucesso");
    } else {
      await apiClient.put(`movies/${data.id}`, data);
      alert("Filme atualizado com sucesso");
    }
    loadMovies(currentMoviesPage.value, searchMoviesQuery.value);
    handleMovieFormClose();
  } catch (error) {
    console.error("Erro ao salvar filme:", error);
    alert("Erro ao salvar filme");
  }
};

const handleMovieFormSuccess = ({ message }) => {
  loadMovies(currentMoviesPage.value, searchMoviesQuery.value);
  handleMovieFormClose();
};

const handleMovieFormError = ({ message }) => {
  console.error("Erro no formulário:", message);
};

const loadRentals = async (page = 1, search = "") => {
  try {
    const params = {
      page: page,
      per_page: perPageRentals.value,
    };

    if (search) {
      params.user_name = search;
    }

    const response = await apiClient.get("rentals", { params });

    rentals.value = response.data.data || [];
    currentRentalsPage.value = response.data.meta?.current_page || 1;
    totalRentalsPages.value = response.data.meta?.last_page || 1;
    perPageRentals.value = response.data.meta?.per_page || 10;
  } catch (error) {
    console.error("Erro ao carregar locações:", error);
    rentals.value = [];
  }
};

let searchRentalsTimeout = null;

const debounceSearchRentals = () => {
  clearTimeout(searchRentalsTimeout);
  searchRentalsTimeout = setTimeout(() => {
    currentRentalsPage.value = 1;
    loadRentals(1, searchRentalsQuery.value);
  }, 500);
};

const previousRentalsPage = () => {
  if (currentRentalsPage.value > 1) {
    currentRentalsPage.value--;
    loadRentals(currentRentalsPage.value, searchRentalsQuery.value);
  }
};

const nextRentalsPage = () => {
  if (currentRentalsPage.value < totalRentalsPages.value) {
    currentRentalsPage.value++;
    loadRentals(currentRentalsPage.value, searchRentalsQuery.value);
  }
};

const openNewRentalModal = () => {
  rentalFormMode.value = "create";
  selectedRental.value = null;
  showRentalFormModal.value = true;
};

const editRental = (rental) => {
  rentalFormMode.value = "edit";
  selectedRental.value = rental;
  showRentalFormModal.value = true;
};

const deleteRental = async (rentalId) => {
  if (confirm("Tem certeza que deseja remover esta locação?")) {
    try {
      await apiClient.delete(`rentals/${rentalId}`);
      alert("Locação removida com sucesso");
      loadRentals(currentRentalsPage.value, searchRentalsQuery.value);
    } catch (error) {
      console.error("Erro ao remover locação:", error);
      alert("Erro ao remover locação");
    }
  }
};

const handleRentalFormClose = () => {
  showRentalFormModal.value = false;
  selectedRental.value = null;
  rentalFormMode.value = "create";
};

const handleRentalFormSubmit = async (data) => {
  try {
    if (rentalFormMode.value === "create") {
      await apiClient.post("rentals", data);
      alert("Locação criada com sucesso");
    } else {
      await apiClient.put(`rentals/${data.id}`, data);
      alert("Locação atualizada com sucesso");
    }
    loadRentals(currentRentalsPage.value, searchRentalsQuery.value);
    handleRentalFormClose();
  } catch (error) {
    console.error("Erro ao salvar locação:", error);
    alert("Erro ao salvar locação");
  }
};

const handleRentalFormSuccess = ({ message }) => {
  loadRentals(currentRentalsPage.value, searchRentalsQuery.value);
  handleRentalFormClose();
};

const handleRentalFormError = ({ message }) => {
  console.error("Erro no formulário:", message);
};

onMounted(() => {
  loadTotalRevenue();
  loadLateReturns();
  loadPendingPenalties();
  loadUsers();
  loadClients();
  loadMovies();
  loadRentals();
});
</script>
