<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center"
      >
        <h1 class="text-2xl font-bold text-forest-600">Espace Client</h1>
        <button
          @click="handleLogout"
          class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition font-medium"
        >
          Se déconnecter
        </button>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-md p-6 md:p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
          Formulaire de contact
        </h2>

        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div>
            <label
              for="subject"
              class="block text-sm font-medium text-gray-700 mb-2"
            >
              Sujet <span class="text-red-500">*</span>
            </label>
            <input
              id="subject"
              v-model="form.subject"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-forest-500 focus:border-transparent outline-none transition"
              placeholder="Objet de votre message"
            />
          </div>

          <div>
            <label
              for="message"
              class="block text-sm font-medium text-gray-700 mb-2"
            >
              Message <span class="text-red-500">*</span>
            </label>
            <textarea
              id="message"
              v-model="form.message"
              required
              rows="6"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-forest-500 focus:border-transparent outline-none transition resize-none"
              placeholder="Décrivez votre demande..."
            ></textarea>
          </div>

          <div>
            <label
              for="attachment"
              class="block text-sm font-medium text-gray-700 mb-2"
            >
              Pièce jointe (optionnel, max 2 Mo)
            </label>
            <input
              id="attachment"
              type="file"
              @change="handleFileChange"
              accept="image/*,.pdf,.doc,.docx,.txt"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-forest-500 focus:border-transparent outline-none transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-forest-50 file:text-forest-700 hover:file:bg-forest-100"
            />
            <p class="text-xs text-gray-500 mt-1">
              Formats acceptés : images, PDF, Word, texte
            </p>
            <p v-if="fileError" class="text-xs text-red-600 mt-1">
              {{ fileError }}
            </p>
            <p v-if="form.attachment" class="text-xs text-green-600 mt-1">
              Fichier sélectionné : {{ form.attachment.name }} ({{
                formatFileSize(form.attachment.size)
              }})
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
            :disabled="loading || !!fileError"
            class="w-full bg-forest-600 hover:bg-forest-700 text-white font-semibold py-3 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? "Envoi en cours..." : "Envoyer le message" }}
          </button>
        </form>
      </div>

      <!-- Historique des messages (optionnel) -->
      <div
        v-if="messages.length > 0"
        class="mt-8 bg-white rounded-lg shadow-md p-6"
      >
        <h3 class="text-xl font-bold text-gray-800 mb-4">
          Historique de vos messages
        </h3>
        <div class="space-y-4">
          <div
            v-for="message in messages"
            :key="message.id"
            class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition"
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
</script>
