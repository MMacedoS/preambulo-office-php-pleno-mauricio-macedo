<template>
  <div class="space-y-3">
    <div
      v-for="(item, index) in items"
      :key="index"
      class="border border-gray-200 rounded-lg overflow-hidden"
    >
      <button
        @click="toggleItem(index)"
        class="w-full px-6 py-4 flex justify-between items-center hover:bg-gray-50 transition-colors bg-gray-50"
      >
        <div class="flex-1 text-left">
          <slot name="header" :item="item" :index="index">
            <span class="font-semibold text-gray-800">{{ item.title }}</span>
          </slot>
        </div>
        <svg
          :class="{ 'rotate-180': openItems.includes(index) }"
          class="w-5 h-5 text-gray-600 transition-transform"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 14l-7 7m0 0l-7-7m7 7V3"
          />
        </svg>
      </button>
      <div
        v-if="openItems.includes(index)"
        class="px-6 py-4 border-t border-gray-200 bg-white"
      >
        <slot name="content" :item="item" :index="index">
          <p class="text-gray-700">{{ item.content }}</p>
        </slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

defineProps({
  items: {
    type: Array,
    required: true,
  },
});

const openItems = ref([]);

const toggleItem = (index) => {
  if (openItems.value.includes(index)) {
    openItems.value = openItems.value.filter((i) => i !== index);
  } else {
    openItems.value.push(index);
  }
};
</script>
