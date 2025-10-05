<template>
  <div
    class="min-h-screen bg-gradient-to-br from-forest-500 to-moss-600 flex items-center justify-center p-4"
  >
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
      <h1 class="text-3xl font-bold text-forest-600 mb-2 text-center">
        Mot de passe oublié
      </h1>
      <p class="text-gray-600 mb-6 text-center">
        Entrez votre email pour recevoir un lien de réinitialisation
      </p>

      <form @submit.prevent="handleRequestReset" class="space-y-4">
        <div>
          <label
            for="email"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Email
          </label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-forest-500 focus:border-transparent outline-none transition"
            placeholder="votre@email.com"
          />
        </div>

        <div
          v-if="error"
          class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm"
        >
          {{ error }}
        </div>

        <div
          v-if="success"
          class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm"
        >
          {{ success }}
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-forest-600 hover:bg-forest-700 text-white font-semibold py-3 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? "Envoi..." : "Envoyer le lien" }}
        </button>
      </form>

      <div class="mt-6 text-center">
        <router-link
          to="/login"
          class="text-sm text-forest-600 hover:text-forest-700 hover:underline"
        >
          ← Retour à la connexion
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import api from "../services/api";

const form = ref({
  email: "",
});

const loading = ref(false);
const error = ref("");
const success = ref("");

const handleRequestReset = async () => {
  loading.value = true;
  error.value = "";
  success.value = "";

  try {
    await api.post("/password/forgot", form.value);
    success.value =
      "Un email de réinitialisation a été envoyé à votre adresse.";
    form.value.email = "";
  } catch (err: any) {
    error.value =
      err.response?.data?.message ||
      "Une erreur est survenue. Veuillez réessayer.";
  } finally {
    loading.value = false;
  }
};
</script>
