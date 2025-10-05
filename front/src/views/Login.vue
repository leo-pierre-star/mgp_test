<template>
  <div
    class="min-h-screen bg-gradient-to-br from-forest-500 to-moss-600 flex items-center justify-center p-4"
  >
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
      <h1 class="text-3xl font-bold text-forest-600 mb-2 text-center">
        Connexion
      </h1>
      <p class="text-gray-600 mb-6 text-center">
        Accédez à votre espace client
      </p>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label
            for="email"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Email
          </label>
          <input
            id="email"
            v-model="form.username"
            type="email"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-forest-500 focus:border-transparent outline-none transition"
            placeholder="votre@email.com"
          />
        </div>

        <div>
          <label
            for="password"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Mot de passe
          </label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-forest-500 focus:border-transparent outline-none transition"
            placeholder="••••••••"
          />
        </div>

        <div
          v-if="error"
          class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm"
        >
          {{ error }}
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-forest-600 hover:bg-forest-700 text-white font-semibold py-3 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? "Connexion..." : "Se connecter" }}
        </button>
      </form>

      <div class="mt-6 text-center space-y-2">
        <router-link
          to="/request-password-reset"
          class="block text-sm text-forest-600 hover:text-forest-700 hover:underline"
        >
          Mot de passe oublié ?
        </router-link>
        <p class="text-sm text-gray-600">
          Pas encore de compte ?
          <router-link
            to="/register"
            class="text-forest-600 hover:text-forest-700 font-semibold hover:underline"
          >
            S'inscrire
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import api from "../services/api";

const router = useRouter();

const form = ref({
  username: "",
  password: "",
});

const loading = ref(false);
const error = ref("");

const handleLogin = async () => {
  loading.value = true;
  error.value = "";

  try {
    const response = await api.post("/login_check", form.value);
    localStorage.setItem("token", response.data.token);
    router.push("/dashboard");
  } catch (err: any) {
    error.value =
      err.response?.data?.message ||
      "Une erreur est survenue. Veuillez réessayer.";
  } finally {
    loading.value = false;
  }
};
</script>
