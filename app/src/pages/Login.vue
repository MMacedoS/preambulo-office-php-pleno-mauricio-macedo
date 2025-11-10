<template>
  <div
    class="min-h-screen bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center p-4"
  >
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-2 text-center">Login</h1>
      <p class="text-gray-600 text-center mb-8">Bem-vindo de volta</p>

      <form @submit.prevent="handleLogin" class="space-y-5">
        <div>
          <label
            for="email"
            class="block text-sm font-medium text-gray-700 mb-2"
            >Email</label
          >
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="seu@email.com"
          />
        </div>

        <div>
          <label
            for="password"
            class="block text-sm font-medium text-gray-700 mb-2"
            >Senha</label
          >
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="••••••••"
          />
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ isLoading ? "Entrando..." : "Entrar" }}
        </button>
      </form>

      <p
        v-if="error"
        class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded"
      >
        {{ error }}
      </p>

      <p class="text-center text-gray-600 mt-6">
        Não tem conta?
        <router-link
          to="/register"
          class="text-blue-600 hover:underline font-semibold"
        >
          Registre-se
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "@/composables/useAuth";

const router = useRouter();
const { login } = useAuth();
const isLoading = ref(false);
const error = ref("");

const form = reactive({
  email: "",
  password: "",
});

const handleLogin = async () => {
  error.value = "";
  isLoading.value = true;

  try {
    await login(form.email, form.password);
    router.push("/dashboard");
  } catch (err) {
    error.value = err.response?.data?.message || "Erro ao fazer login";
  } finally {
    isLoading.value = false;
  }
};
</script>
