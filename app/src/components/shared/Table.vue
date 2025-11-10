<template>
  <div class="w-full overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-100 border-b">
            <th
              v-for="column in columns"
              :key="column.key"
              class="px-4 py-3 text-left text-sm font-semibold text-gray-700"
              :style="{ width: column.width || 'auto' }"
            >
              {{ column.label }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(row, index) in data"
            :key="index"
            class="border-b hover:bg-gray-50 transition-colors"
          >
            <td
              v-for="column in columns"
              :key="column.key"
              class="px-4 py-3 text-sm text-gray-700"
            >
              <slot
                :name="`cell-${column.key}`"
                :row="row"
                :value="row[column.key]"
              >
                <span
                  v-if="column.type === 'status'"
                  :class="getStatusClass(row[column.key])"
                >
                  {{ formatStatus(row[column.key]) }}
                </span>
                <span v-else-if="column.type === 'currency'">
                  R$ {{ parseFloat(row[column.key]).toFixed(2) }}
                </span>
                <span v-else-if="column.type === 'date'">
                  {{ formatDate(row[column.key]) }}
                </span>
                <div v-else-if="column.type === 'list'" class="space-y-1">
                  <div
                    v-for="item in row[column.key]"
                    :key="item.id"
                    class="text-xs bg-blue-50 px-2 py-1 rounded"
                  >
                    {{ item[column.itemField || "name"] }}
                  </div>
                </div>
                <span v-else>
                  {{ row[column.key] }}
                </span>
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="data.length === 0" class="text-center py-8 text-gray-500">
      Nenhum dado disponível
    </div>
  </div>
</template>

<script setup>
defineProps({
  columns: {
    type: Array,
    required: true,
  },
  data: {
    type: Array,
    default: () => [],
  },
});

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString("pt-BR");
};

const formatStatus = (status) => {
  const statusMap = {
    ativo: "Ativa",
    atrasado: "Atrasada",
    devolvido: "Devolvido",
    cancelado: "Cancelado",
  };
  return statusMap[status] || status;
};

const getStatusClass = (status) => {
  const classMap = {
    ativo:
      "bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold",
    atrasado:
      "bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold",
    devolvido:
      "bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold",
    cancelado:
      "bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold",
  };
  return classMap[status] || "";
};
</script>
