<template>
  <div
    class="min-h-screen bg-gradient-to-br from-forest-500 to-moss-600 flex items-center justify-center p-4 animate-gradient"
  >
    <div
      class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md card-entrance"
    >
      <div class="header-fade-in">
        <h1 class="text-3xl font-bold text-forest-600 mb-2 text-center">
          Inscription
        </h1>
        <p class="text-gray-600 mb-6 text-center">Créez votre compte client</p>
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4 form-fade-in">
        <div class="input-group">
          <label
            for="email"
            class="block text-sm font-medium text-gray-700 mb-1 transition-all duration-200"
          >
            Email
          </label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-forest-500 focus:border-transparent outline-none transition-all duration-300 hover:border-forest-300 focus:scale-[1.02]"
            placeholder="votre@email.com"
          />
        </div>

        <div class="input-group">
          <label
            for="password"
            class="block text-sm font-medium text-gray-700 mb-1 transition-all duration-200"
          >
            Mot de passe
          </label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            @input="validatePassword"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-forest-500 focus:border-transparent outline-none transition-all duration-300 hover:border-forest-300 focus:scale-[1.02]"
            placeholder="••••••••"
          />

          <!-- Indicateur de force du mot de passe -->
          <div v-if="form.password" class="mt-2 space-y-1 password-checks">
            <div
              class="flex items-center text-xs check-item"
              :class="{ 'check-valid': passwordChecks.length }"
            >
              <span
                class="transition-all duration-300 transform"
                :class="
                  passwordChecks.length
                    ? 'text-green-600 scale-110'
                    : 'text-red-600'
                "
              >
                {{ passwordChecks.length ? "✓" : "✗" }}
              </span>
              <span class="ml-2 transition-colors duration-200"
                >Au moins 8 caractères</span
              >
            </div>
            <div
              class="flex items-center text-xs check-item"
              :class="{ 'check-valid': passwordChecks.uppercase }"
            >
              <span
                class="transition-all duration-300 transform"
                :class="
                  passwordChecks.uppercase
                    ? 'text-green-600 scale-110'
                    : 'text-red-600'
                "
              >
                {{ passwordChecks.uppercase ? "✓" : "✗" }}
              </span>
              <span class="ml-2 transition-colors duration-200"
                >Une majuscule</span
              >
            </div>
            <div
              class="flex items-center text-xs check-item"
              :class="{ 'check-valid': passwordChecks.number }"
            >
              <span
                class="transition-all duration-300 transform"
                :class="
                  passwordChecks.number
                    ? 'text-green-600 scale-110'
                    : 'text-red-600'
                "
              >
                {{ passwordChecks.number ? "✓" : "✗" }}
              </span>
              <span class="ml-2 transition-colors duration-200"
                >Un chiffre</span
              >
            </div>
            <div
              class="flex items-center text-xs check-item"
              :class="{ 'check-valid': passwordChecks.special }"
            >
              <span
                class="transition-all duration-300 transform"
                :class="
                  passwordChecks.special
                    ? 'text-green-600 scale-110'
                    : 'text-red-600'
                "
              >
                {{ passwordChecks.special ? "✓" : "✗" }}
              </span>
              <span class="ml-2 transition-colors duration-200"
                >Un caractère spécial</span
              >
            </div>
          </div>
        </div>

        <div class="input-group">
          <label
            for="confirmPassword"
            class="block text-sm font-medium text-gray-700 mb-1 transition-all duration-200"
          >
            Confirmer le mot de passe
          </label>
          <input
            id="confirmPassword"
            v-model="form.confirmPassword"
            type="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-forest-500 focus:border-transparent outline-none transition-all duration-300 hover:border-forest-300 focus:scale-[1.02]"
            placeholder="••••••••"
          />
          <p
            v-if="
              form.confirmPassword && form.password !== form.confirmPassword
            "
            class="text-xs text-red-600 mt-1 animate-shake"
          >
            Les mots de passe ne correspondent pas
          </p>
        </div>

        <div
          v-if="error"
          class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm alert-slide-in animate-shake"
        >
          {{ error }}
        </div>

        <div
          v-if="success"
          class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm alert-slide-in success-pulse"
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
          class="w-full bg-forest-600 hover:bg-forest-700 text-white font-semibold py-3 rounded-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-[1.02] hover:shadow-lg active:scale-[0.98]"
        >
          <span v-if="loading" class="inline-flex items-center">
            <svg
              class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            Inscription...
          </span>
          <span v-else>S'inscrire</span>
        </button>
      </form>

      <div class="mt-6 text-center links-fade-in">
        <p class="text-sm text-gray-600">
          Déjà un compte ?
          <router-link
            to="/login"
            class="text-forest-600 hover:text-forest-700 font-semibold transition-all duration-300 hover:underline"
          >
            Se connecter
          </router-link>
        </p>
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
  email: "",
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

const handleRegister = async () => {
  loading.value = true;
  error.value = "";
  success.value = "";

  try {
    await api.post("/users", {
      email: form.value.email,
      password: form.value.password,
    });
    success.value = "Compte créé avec succès ! Redirection...";
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

<style scoped>
@keyframes cardEntrance {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes shake {
  0%,
  100% {
    transform: translateX(0);
  }
  25% {
    transform: translateX(-5px);
  }
  75% {
    transform: translateX(5px);
  }
}

@keyframes pulse {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.02);
  }
}

@keyframes gradient {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

.card-entrance {
  animation: cardEntrance 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.header-fade-in {
  animation: fadeIn 0.8s ease-out 0.2s both;
}

.form-fade-in {
  animation: fadeIn 0.8s ease-out 0.4s both;
}

.links-fade-in {
  animation: fadeIn 0.8s ease-out 0.6s both;
}

.input-group {
  animation: fadeIn 0.6s ease-out both;
}

.input-group:nth-child(1) {
  animation-delay: 0.5s;
}
.input-group:nth-child(2) {
  animation-delay: 0.6s;
}
.input-group:nth-child(3) {
  animation-delay: 0.7s;
}

.password-checks {
  animation: slideIn 0.4s ease-out;
}

.check-item {
  opacity: 0.7;
  transition: all 0.3s ease;
}

.check-item.check-valid {
  opacity: 1;
}

.alert-slide-in {
  animation: slideIn 0.4s ease-out;
}

.animate-shake {
  animation: shake 0.4s ease-in-out;
}

.success-pulse {
  animation: slideIn 0.4s ease-out, pulse 1.5s ease-in-out 0.4s infinite;
}

.animate-gradient {
  background-size: 200% 200%;
  animation: gradient 15s ease infinite;
}

button:not(:disabled):hover {
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
}

input:focus {
  box-shadow: 0 0 0 3px rgba(74, 129, 84, 0.1);
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
