<template>
  <div
    class="min-h-screen bg-gradient-to-br from-green-600 to-green-800 flex items-center justify-center p-4"
  >
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-2 text-center">
        Registre-se
      </h1>
      <p class="text-gray-600 text-center mb-8">Crie sua conta</p>

      <form @submit.prevent="handleRegister" class="space-y-5">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-2"
            >Nome</label
          >
          <input
            id="name"
            v-model="form.name"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            placeholder="Seu nome"
          />
        </div>

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
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
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
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            placeholder="••••••••"
          />
        </div>

        <div>
          <label
            for="password_confirmation"
            class="block text-sm font-medium text-gray-700 mb-2"
            >Confirmar Senha</label
          >
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            placeholder="••••••••"
          />
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="w-full bg-green-600 text-white font-semibold py-2 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ isLoading ? "Registrando..." : "Registrar" }}
        </button>
      </form>

      <p
        v-if="error"
        class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded"
      >
        {{ error }}
      </p>

      <p class="text-center text-gray-600 mt-6">
        Já tem conta?
        <router-link
          to="/login"
          class="text-green-600 hover:underline font-semibold"
        >
          Faça login
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
const { register } = useAuth();
const isLoading = ref(false);
const error = ref("");

const form = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const handleRegister = async () => {
  error.value = "";

  if (form.password !== form.password_confirmation) {
    error.value = "As senhas não correspondem";
    return;
  }

  isLoading.value = true;

  try {
    await register(
      form.name,
      form.email,
      form.password,
      form.password_confirmation
    );
    router.push("/dashboard");
  } catch (err) {
    error.value = err.response?.data?.message || "Erro ao registrar";
  } finally {
    isLoading.value = false;
  }
};
</script>
