<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 flex items-center justify-center p-4 z-50"
  >
    <div class="absolute inset-0 bg-black bg-opacity-30" @click="close"></div>
    <div
      class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden relative z-10"
    >
      <div
        class="flex justify-between items-center p-6 border-b bg-linear-to-r from-gray-50 to-gray-100"
      >
        <h2 class="text-2xl font-bold text-gray-800">{{ title }}</h2>
        <button
          @click="close"
          class="text-gray-500 hover:text-gray-700 text-3xl leading-none w-8 h-8 flex items-center justify-center"
        >
          ×
        </button>
      </div>
      <div class="overflow-y-auto max-h-[calc(90vh-120px)] p-6">
        <slot></slot>
      </div>
      <div
        v-if="$slots.footer"
        class="border-t bg-gray-50 p-6 flex justify-end gap-3"
      >
        <slot name="footer"></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  title: {
    type: String,
    default: "Modal",
  },
});

const emit = defineEmits(["close"]);

const close = () => {
  emit("close");
};
</script>
