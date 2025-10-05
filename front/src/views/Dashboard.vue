<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm header-slide-down">
      <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center"
      >
        <h1 class="text-2xl font-bold text-forest-600">Espace Client</h1>
        <button
          @click="handleLogout"
          class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-all duration-300 font-medium transform hover:scale-105 hover:shadow-lg active:scale-95"
        >
          Se déconnecter
        </button>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-md p-6 md:p-8 card-entrance">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 header-fade-in">
          Formulaire de contact
        </h2>

        <form @submit.prevent="handleSubmit" class="space-y-6 form-fade-in">
          <div class="input-group">
            <label
              for="subject"
              class="block text-sm font-medium text-gray-700 mb-2 transition-all duration-200"
            >
              Sujet <span class="text-red-500">*</span>
            </label>
            <input
              id="subject"
              v-model="form.subject"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-forest-500 focus:border-transparent outline-none transition-all duration-300 hover:border-forest-300 focus:scale-[1.01]"
              placeholder="Objet de votre message"
            />
          </div>

          <div class="input-group">
            <label
              for="message"
              class="block text-sm font-medium text-gray-700 mb-2 transition-all duration-200"
            >
              Message <span class="text-red-500">*</span>
            </label>
            <textarea
              id="message"
              v-model="form.message"
              required
              rows="6"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-forest-500 focus:border-transparent outline-none transition-all duration-300 resize-none hover:border-forest-300 focus:scale-[1.01]"
              placeholder="Décrivez votre demande..."
            ></textarea>
          </div>

          <div class="input-group">
            <label
              for="attachment"
              class="block text-sm font-medium text-gray-700 mb-2 transition-all duration-200"
            >
              Pièce jointe (optionnel, max 2 Mo)
            </label>
            <input
              id="attachment"
              type="file"
              @change="handleFileChange"
              accept="image/*,.pdf,.doc,.docx,.txt"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-forest-500 focus:border-transparent outline-none transition-all duration-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-forest-50 file:text-forest-700 hover:file:bg-forest-100 file:transition-all file:duration-200 file:cursor-pointer hover:border-forest-300"
            />
            <p class="text-xs text-gray-500 mt-1">
              Formats acceptés : images, PDF, Word, texte
            </p>
            <p v-if="fileError" class="text-xs text-red-600 mt-1 animate-shake">
              {{ fileError }}
            </p>
            <p
              v-if="form.attachment"
              class="text-xs text-green-600 mt-1 file-selected"
            >
              Fichier sélectionné : {{ form.attachment.name }} ({{
                formatFileSize(form.attachment.size)
              }})
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
            :disabled="loading || !!fileError"
            class="w-full bg-forest-600 hover:bg-forest-700 text-white font-semibold py-3 rounded-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-lg active:scale-[0.98] transform"
          >
            <span v-if="loading" class="inline-flex items-center">
              <svg
                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block"
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
              Envoi en cours...
            </span>
            <span v-else>Envoyer le message</span>
          </button>
        </form>
      </div>

      <!-- Historique des messages (optionnel) -->
      <div
        v-if="messages.length > 0"
        class="mt-8 bg-white rounded-lg shadow-md p-6 card-entrance"
      >
        <h3 class="text-xl font-bold text-gray-800 mb-4 header-fade-in">
          Historique de vos messages
        </h3>
        <div class="space-y-4">
          <div
            v-for="(message, index) in messages"
            :key="message.id"
            class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-all duration-300 hover:shadow-md hover:scale-[1.01] transform message-item"
            :style="`animation-delay: ${index * 0.1}s`"
          >
            <div class="flex justify-between items-start mb-2">
              <h4 class="font-semibold text-gray-800">{{ message.subject }}</h4>
              <span class="text-xs text-gray-500">{{
                formatDate(message.createdAt)
              }}</span>
            </div>
            <p class="text-sm text-gray-600">{{ message.message }}</p>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import api from "../services/api";

const router = useRouter();

const form = ref({
  subject: "",
  message: "",
  attachment: null as File | null,
});

const loading = ref(false);
const error = ref("");
const success = ref("");
const fileError = ref("");
const messages = ref<any[]>([]);

const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2 Mo en octets

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];

  fileError.value = "";

  if (file) {
    if (file.size > MAX_FILE_SIZE) {
      fileError.value = "Le fichier ne doit pas dépasser 2 Mo";
      form.value.attachment = null;
      target.value = "";
    } else {
      form.value.attachment = file;
    }
  } else {
    form.value.attachment = null;
  }
};

const formatFileSize = (bytes: number): string => {
  if (bytes < 1024) return bytes + " octets";
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " Ko";
  return (bytes / (1024 * 1024)).toFixed(1) + " Mo";
};

const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleDateString("fr-FR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

const handleSubmit = async () => {
  loading.value = true;
  error.value = "";
  success.value = "";

  try {
    const formData = new FormData();
    formData.append("subject", form.value.subject);
    formData.append("message", form.value.message);

    if (form.value.attachment) {
      formData.append("attachment", form.value.attachment);
    }

    await api.post("/contact", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    success.value = "Votre message a été envoyé avec succès !";

    // Réinitialiser le formulaire
    form.value = {
      subject: "",
      message: "",
      attachment: null,
    };

    // Réinitialiser le champ fichier
    const fileInput = document.getElementById("attachment") as HTMLInputElement;
    if (fileInput) fileInput.value = "";

    // Optionnel : recharger l'historique des messages
    // await loadMessages()
  } catch (err: any) {
    error.value =
      err.response?.data?.message ||
      "Une erreur est survenue lors de l'envoi du message.";
  } finally {
    loading.value = false;
  }
};

const handleLogout = () => {
  localStorage.removeItem("token");
  router.push("/login");
};

// Optionnel : charger l'historique des messages
// const loadMessages = async () => {
//   try {
//     const response = await api.get('/contact/history')
//     messages.value = response.data
//   } catch (err) {
//     console.error('Erreur lors du chargement des messages', err)
//   }
// }

// onMounted(() => {
//   loadMessages()
// })
//   }
// }
</script>

<style scoped>
/* Card Entrance Animation */
@keyframes cardEntrance {
  0% {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.card-entrance {
  animation: cardEntrance 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Fade In Animation */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.header-fade-in {
  animation: fadeIn 0.8s ease-in-out;
}

.form-fade-in {
  animation: fadeIn 1s ease-in-out 0.2s both;
}

.links-fade-in {
  animation: fadeIn 1s ease-in-out 0.4s both;
}

/* Slide Down Animation for Header */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.header-slide-down {
  animation: slideDown 0.5s ease-out;
}

/* Slide In Animation */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.input-group {
  animation: slideIn 0.5s ease-out;
}

.input-group:nth-child(1) {
  animation-delay: 0.1s;
}

.input-group:nth-child(2) {
  animation-delay: 0.2s;
}

.input-group:nth-child(3) {
  animation-delay: 0.3s;
}

/* Alert Slide In */
@keyframes alertSlide {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.alert-slide-in {
  animation: alertSlide 0.3s ease-out;
}

/* Shake Animation for Errors */
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

.animate-shake {
  animation: shake 0.3s ease-in-out;
}

/* Success Pulse */
@keyframes successPulse {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.02);
  }
}

.success-pulse {
  animation: successPulse 0.5s ease-in-out;
}

/* Gradient Animation */
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

.animate-gradient {
  background-size: 200% 200%;
  animation: gradient 15s ease infinite;
}

/* Spin Animation for Loading */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* File Selected Animation */
@keyframes fileSelected {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.file-selected {
  animation: fileSelected 0.3s ease-out;
}

/* Message Item Stagger */
@keyframes messageItem {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.message-item {
  animation: messageItem 0.4s ease-out backwards;
}

/* Hover Effects */
.input-group input:focus,
.input-group textarea:focus {
  box-shadow: 0 0 0 3px rgba(34, 139, 34, 0.1);
}

/* Button Hover Enhancement */
button:not(:disabled):hover {
  box-shadow: 0 10px 20px -10px rgba(34, 139, 34, 0.4);
}
</style>
