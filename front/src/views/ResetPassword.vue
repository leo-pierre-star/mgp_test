<template>
  <div
    class="min-h-screen bg-gradient-to-br from-forest-500 to-moss-600 flex items-center justify-center p-4"
  >
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
      <h1 class="text-3xl font-bold text-forest-600 mb-2 text-center">
        Réinitialisation
      </h1>
      <p class="text-gray-600 mb-6 text-center">
        Définissez votre nouveau mot de passe
      </p>

      <form @submit.prevent="handleResetPassword" class="space-y-4">
        <div>
          <label
            for="token"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Code de réinitialisation
          </label>
          <input
            id="token"
            v-model="form.token"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-forest-500 focus:border-transparent outline-none transition"
            placeholder="Code reçu par email"
          />
        </div>

        <div>
          <label
            for="password"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Nouveau mot de passe
          </label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            @input="validatePassword"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-forest-500 focus:border-transparent outline-none transition"
            placeholder="••••••••"
          />

          <!-- Indicateur de force du mot de passe -->
          <div v-if="form.password" class="mt-2 space-y-1">
            <div class="flex items-center text-xs">
              <span
                :class="
                  passwordChecks.length ? 'text-green-600' : 'text-red-600'
                "
              >
                {{ passwordChecks.length ? "✓" : "✗" }}
              </span>
              <span class="ml-2">Au moins 8 caractères</span>
            </div>
            <div class="flex items-center text-xs">
              <span
                :class="
                  passwordChecks.uppercase ? 'text-green-600' : 'text-red-600'
                "
              >
                {{ passwordChecks.uppercase ? "✓" : "✗" }}
              </span>
              <span class="ml-2">Une majuscule</span>
            </div>
            <div class="flex items-center text-xs">
              <span
                :class="
                  passwordChecks.number ? 'text-green-600' : 'text-red-600'
                "
              >
                {{ passwordChecks.number ? "✓" : "✗" }}
              </span>
              <span class="ml-2">Un chiffre</span>
            </div>
            <div class="flex items-center text-xs">
              <span
                :class="
                  passwordChecks.special ? 'text-green-600' : 'text-red-600'
                "
              >
                {{ passwordChecks.special ? "✓" : "✗" }}
              </span>
              <span class="ml-2">Un caractère spécial</span>
            </div>
          </div>
        </div>

        <div>
          <label
            for="confirmPassword"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Confirmer le mot de passe
          </label>
          <input
            id="confirmPassword"
            v-model="form.confirmPassword"
            type="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-forest-500 focus:border-transparent outline-none transition"
            placeholder="••••••••"
          />
          <p
            v-if="
              form.confirmPassword && form.password !== form.confirmPassword
            "
            class="text-xs text-red-600 mt-1"
          >
            Les mots de passe ne correspondent pas
          </p>
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
          :disabled="
            loading ||
            !isPasswordValid ||
            form.password !== form.confirmPassword
          "
          class="w-full bg-forest-600 hover:bg-forest-700 text-white font-semibold py-3 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{
            loading ? "Réinitialisation..." : "Réinitialiser le mot de passe"
          }}
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
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import api from "../services/api";

const router = useRouter();

const form = ref({
  token: "",
  password: "",
  confirmPassword: "",
});

const passwordChecks = ref({
  length: false,
  uppercase: false,
  number: false,
  special: false,
});

const loading = ref(false);
const error = ref("");
const success = ref("");

const validatePassword = () => {
  const password = form.value.password;
  passwordChecks.value = {
    length: password.length >= 8,
    uppercase: /[A-Z]/.test(password),
    number: /[0-9]/.test(password),
    special: /[!@#$%^&*(),.?":{}|<>]/.test(password),
  };
};

const isPasswordValid = computed(() => {
  return Object.values(passwordChecks.value).every((check) => check);
});

const handleResetPassword = async () => {
  loading.value = true;
  error.value = "";
  success.value = "";

  try {
    await api.post("/password/reset", {
      token: form.value.token,
      password: form.value.password,
    });
    success.value = "Mot de passe réinitialisé avec succès ! Redirection...";
    setTimeout(() => {
      router.push("/login");
    }, 2000);
  } catch (err: any) {
    error.value =
      err.response?.data?.message ||
      "Une erreur est survenue. Veuillez réessayer.";
  } finally {
    loading.value = false;
  }
};
</script>
