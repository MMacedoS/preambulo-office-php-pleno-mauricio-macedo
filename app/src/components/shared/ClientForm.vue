<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label
          for="name"
          class="block text-sm font-semibold text-gray-700 mb-2"
        >
          Nome *
        </label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          placeholder="Nome completo"
          :class="getInputClass('name')"
          required
        />
        <p v-if="errors.name" class="mt-1 text-sm text-red-600">
          {{ errors.name }}
        </p>
      </div>

      <div>
        <label
          for="email"
          class="block text-sm font-semibold text-gray-700 mb-2"
        >
          Email *
        </label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          placeholder="email@exemplo.com"
          :class="getInputClass('email')"
          required
        />
        <p v-if="errors.email" class="mt-1 text-sm text-red-600">
          {{ errors.email }}
        </p>
      </div>

      <div v-if="includePasswordField" class="md:col-span-2">
        <label
          for="password"
          class="block text-sm font-semibold text-gray-700 mb-2"
        >
          Senha {{ isEditMode ? "(deixe em branco para manter)" : "*" }}
        </label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          placeholder="Mínimo 8 caracteres"
          :class="getInputClass('password')"
          :required="!isEditMode"
        />
        <p v-if="errors.password" class="mt-1 text-sm text-red-600">
          {{ errors.password }}
        </p>
      </div>

      <div v-if="canEditRole" class="md:col-span-2">
        <label
          for="role"
          class="block text-sm font-semibold text-gray-700 mb-2"
        >
          Papel *
        </label>
        <select
          id="role"
          v-model="form.role"
          :class="getInputClass('role')"
          required
        >
          <option value="">Selecione um papel</option>
          <option value="cliente">Cliente</option>
          <option value="atendente">Atendente</option>
          <option value="administrador">Administrador</option>
        </select>
        <p v-if="errors.role" class="mt-1 text-sm text-red-600">
          {{ errors.role }}
        </p>
      </div>

      <div v-if="!canEditRole" class="md:col-span-2 hidden">
        <input v-model="form.role" type="hidden" />
      </div>
    </div>

    <div class="flex gap-3 justify-end border-t pt-6">
      <button
        type="button"
        @click="handleCancel"
        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium"
      >
        Cancelar
      </button>
      <button
        type="submit"
        :disabled="isLoading"
        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg font-medium flex items-center gap-2"
      >
        <span v-if="isLoading" class="animate-spin">⟳</span>
        {{ isEditMode ? "Atualizar" : "Criar" }} Cliente
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, reactive, computed, watch } from "vue";
import apiClient from "@/services/apiClient";

const props = defineProps({
  client: {
    type: Object,
    default: null,
  },
  canEditRole: {
    type: Boolean,
    default: false,
  },
  includePasswordField: {
    type: Boolean,
    default: true,
  },
});

const emit = defineEmits(["cancel", "submit", "success", "error"]);

const form = reactive({
  name: "",
  email: "",
  password: "",
  role: "cliente",
});

const errors = reactive({
  name: null,
  email: null,
  password: null,
  role: null,
});

const isLoading = ref(false);
const isEditMode = computed(() => !!props.client?.id);

const clearErrors = () => {
  Object.keys(errors).forEach((key) => {
    errors[key] = null;
  });
};

const resetForm = () => {
  form.name = "";
  form.email = "";
  form.password = "";
  form.role = "cliente";
  clearErrors();
};

watch(
  () => props.client,
  (newClient) => {
    if (!newClient) {
      resetForm();
      return;
    }

    form.name = newClient.name || "";
    form.email = newClient.email || "";
    form.role = newClient.role || "cliente";
    form.password = "";
  },
  { immediate: true }
);

const getInputClass = (fieldName) => {
  const baseClass =
    "w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 transition";
  const errorClass = errors[fieldName]
    ? "border-red-500 focus:ring-red-500"
    : "border-gray-300 focus:ring-blue-500";
  return `${baseClass} ${errorClass}`;
};

const handleCancel = () => {
  resetForm();
  emit("cancel");
};

const handleSubmit = async () => {
  clearErrors();
  isLoading.value = true;

  try {
    const payload = {
      name: form.name,
      email: form.email,
      role: form.role,
    };

    if (form.password) {
      payload.password = form.password;
    }

    const apiMethod = isEditMode.value
      ? apiClient.put(`users/${props.client.id}`, payload)
      : apiClient.post("users", payload);

    const response = await apiMethod;

    emit("submit", response.data.data || response.data);
    emit("success", {
      message: isEditMode.value
        ? "Cliente atualizado com sucesso"
        : "Cliente criado com sucesso",
    });
    resetForm();
  } catch (error) {
    if (error.response?.data?.errors) {
      Object.entries(error.response.data.errors).forEach(([key, value]) => {
        errors[key] = Array.isArray(value) ? value[0] : value;
      });
      return;
    }

    if (error.response?.data?.message) {
      emit("error", { message: error.response.data.message });
      return;
    }

    emit("error", {
      message: isEditMode.value
        ? "Erro ao atualizar cliente"
        : "Erro ao criar cliente",
    });
  } finally {
    isLoading.value = false;
  }
};
</script>
