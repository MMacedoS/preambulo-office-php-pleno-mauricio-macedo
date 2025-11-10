<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
  >
    <div
      class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-screen overflow-y-auto"
    >
      <div
        class="bg-linear-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center sticky top-0"
      >
        <h2 class="text-xl font-bold text-white">Editar Perfil</h2>
        <button
          @click="handleClose"
          class="text-white hover:bg-blue-800 rounded-full p-1 transition"
        >
          <svg
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>

      <div class="p-6 space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Nome
          </label>
          <input
            v-model="formData.name"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
            placeholder="Seu nome completo"
            :disabled="loading"
          />
          <span v-if="errors.name" class="text-sm text-red-600 mt-1 block">
            {{ errors.name }}
          </span>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Email
          </label>
          <input
            v-model="formData.email"
            type="email"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
            placeholder="seu@email.com"
            :disabled="loading"
          />
          <span v-if="errors.email" class="text-sm text-red-600 mt-1 block">
            {{ errors.email }}
          </span>
        </div>

        <div v-if="showPasswordField">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Senha (deixe em branco para manter a atual)
          </label>
          <input
            v-model="formData.password"
            type="password"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
            placeholder="••••••••"
            :disabled="loading"
          />
          <span v-if="errors.password" class="text-sm text-red-600 mt-1 block">
            {{ errors.password }}
          </span>
        </div>

        <div v-if="canEditRole">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Perfil
          </label>
          <select
            v-model="formData.role"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
            :disabled="loading"
          >
            <option value="cliente">Cliente</option>
            <option value="atendente">Atendente</option>
            <option value="administrador">Administrador</option>
          </select>
          <span v-if="errors.role" class="text-sm text-red-600 mt-1 block">
            {{ errors.role }}
          </span>
        </div>

        <div
          v-if="successMessage"
          class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
              clip-rule="evenodd"
            />
          </svg>
          <span>{{ successMessage }}</span>
        </div>

        <div
          v-if="globalError"
          class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg"
        >
          {{ globalError }}
        </div>
      </div>

      <div class="bg-gray-50 px-6 py-4 flex gap-3 sticky bottom-0 border-t">
        <button
          @click="handleClose"
          class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition font-medium"
          :disabled="loading"
        >
          Cancelar
        </button>
        <button
          @click="handleSave"
          class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium flex items-center justify-center gap-2"
          :disabled="loading"
        >
          <svg
            v-if="!loading"
            class="w-5 h-5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M5 13l4 4L19 7"
            />
          </svg>
          <span v-if="loading" class="inline-block animate-spin">⌛</span>
          {{ loading ? "Salvando..." : "Salvar" }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from "vue";
import apiClient from "@/services/apiClient";

const props = defineProps({
  userId: {
    type: String,
    required: true,
  },
  userRole: {
    type: String,
    default: "cliente",
  },
  showPasswordField: {
    type: Boolean,
    default: true,
  },
  canEditRole: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["close", "save"]);

const loading = ref(false);
const successMessage = ref("");
const globalError = ref("");
const formData = reactive({
  name: "",
  email: "",
  password: "",
  role: "cliente",
});

const errors = reactive({
  name: "",
  email: "",
  password: "",
  role: "",
});

const loadUserData = async () => {
  try {
    loading.value = true;
    const response = await apiClient.get(`users/profile`);
    const user = response.data.data;

    formData.name = user.name || "";
    formData.email = user.email || "";
    formData.role = user.role || props.userRole;
    formData.password = "";
  } catch (error) {
    globalError.value = "Erro ao carregar dados do usuário";
    console.error("Erro:", error);
  } finally {
    loading.value = false;
  }
};

const clearErrors = () => {
  Object.keys(errors).forEach((key) => {
    errors[key] = "";
  });
};

const handleSave = async () => {
  clearErrors();
  globalError.value = "";
  successMessage.value = "";

  try {
    loading.value = true;

    const payload = {
      name: formData.name,
      email: formData.email,
    };

    if (props.showPasswordField && formData.password) {
      payload.password = formData.password;
    }

    if (props.canEditRole) {
      payload.role = formData.role;
    }

    const response = await apiClient.put(`users/profile`, payload);

    successMessage.value = "Perfil atualizado com sucesso!";
    emit("save", response.data.data);

    setTimeout(() => {
      handleClose();
    }, 1500);
  } catch (error) {
    if (error.response?.status === 422) {
      const errorData = error.response.data.errors || {};
      Object.keys(errorData).forEach((field) => {
        if (errors.hasOwnProperty(field)) {
          errors[field] = Array.isArray(errorData[field])
            ? errorData[field][0]
            : errorData[field];
        }
      });
    } else {
      globalError.value =
        error.response?.data?.message || "Erro ao atualizar perfil";
    }
  } finally {
    loading.value = false;
  }
};

const handleClose = () => {
  emit("close");
};

onMounted(() => {
  loadUserData();
});
</script>
