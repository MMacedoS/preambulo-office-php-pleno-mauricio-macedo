<template>
  <component :is="currentDashboard" />
</template>

<script setup>
import { computed } from "vue";
import { useAuth } from "@/composables/useAuth";
import { ROLES } from "@/enums/roles";
import ClienteDashboard from "@/components/dashboards/ClienteDashboard.vue";
import AtendenteDashboard from "@/components/dashboards/AtendenteDashboard.vue";
import AdminDashboard from "@/components/dashboards/AdminDashboard.vue";

const { state } = useAuth();

const currentDashboard = computed(() => {
  switch (state.role) {
    case ROLES.CLIENTE:
      return ClienteDashboard;
    case ROLES.ATENDENTE:
      return AtendenteDashboard;
    case ROLES.ADMINISTRADOR:
      return AdminDashboard;
    default:
      return ClienteDashboard;
  }
});
</script>
