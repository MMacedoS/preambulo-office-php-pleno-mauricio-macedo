<template>
  <div
    class="min-h-screen bg-gradient-to-br from-purple-600 to-purple-800 flex items-center justify-center p-4"
  >
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl p-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-2 text-center">
        Dashboard
      </h1>
      <p class="text-gray-600 text-center mb-8">Bem-vindo ao LocaFilmes</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "@/composables/useAuth";
import { authService } from "@/services/authService";

const router = useRouter();
const { logout } = useAuth();

const maskToken = computed(() => {
  const token = authService.getToken();
  if (!token) return "Nenhum token";
  return token.substring(0, 10) + "..." + token.substring(token.length - 10);
});

const handleLogout = async () => {
  await logout();
  router.push("/login");
};
</script>
