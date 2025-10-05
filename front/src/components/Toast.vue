<template>
  <transition name="toast">
    <div
      v-if="show"
      :class="[
        'fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg max-w-sm z-50',
        typeClasses,
      ]"
    >
      <div class="flex items-start">
        <div class="flex-shrink-0">
          <svg
            v-if="type === 'success'"
            class="h-6 w-6"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
              clip-rule="evenodd"
            />
          </svg>
          <svg
            v-else-if="type === 'error'"
            class="h-6 w-6"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
              clip-rule="evenodd"
            />
          </svg>
          <svg v-else class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
            <path
              fill-rule="evenodd"
              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
        <div class="ml-3 flex-1">
          <p class="text-sm font-medium">{{ message }}</p>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";

const props = defineProps<{
  message: string;
  type?: "success" | "error" | "info";
  duration?: number;
}>();

const show = ref(false);

const typeClasses = computed(() => {
  switch (props.type) {
    case "success":
      return "bg-green-50 text-green-800 border border-green-200";
    case "error":
      return "bg-red-50 text-red-800 border border-red-200";
    default:
      return "bg-blue-50 text-blue-800 border border-blue-200";
  }
});

watch(
  () => props.message,
  (newMessage) => {
    if (newMessage) {
      show.value = true;
      setTimeout(() => {
        show.value = false;
      }, props.duration || 3000);
    }
  }
);
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}
</style>
