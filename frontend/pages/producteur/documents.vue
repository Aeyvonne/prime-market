<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <div class="bg-gradient-to-br from-[#2D5A27] to-[#1E3F1A] rounded-[2.5rem] p-8 md:p-12 text-white relative overflow-hidden shadow-xl shadow-[#2D5A27]/10">
      <div class="absolute -right-24 -bottom-24 w-96 h-96 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
      <div class="absolute -left-12 -top-12 w-64 h-64 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
      <div class="relative z-10">
        <h1 class="text-3xl md:text-4xl font-black font-serif leading-tight mb-2">Documents Justificatifs</h1>
        <p class="text-white/80 text-sm md:text-base font-medium">
          Téléchargez vos pièces justificatives pour validation de votre compte producteur.
        </p>
      </div>
    </div>

    <div v-if="userStatus === 'en_attente'" class="bg-amber-50 border border-amber-200 rounded-[2rem] p-6 flex items-start gap-4">
      <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center flex-shrink-0">
        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      </div>
      <div>
        <h3 class="font-bold text-amber-800">Compte en attente de validation</h3>
        <p class="text-sm text-amber-700 mt-1">Veuillez fournir tous les documents requis. Un administrateur vérifiera vos documents et activera votre compte.</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8">
          <h3 class="text-xl font-black text-gray-900 mb-6">Mes Documents</h3>

          <div v-if="loading" class="space-y-4">
            <div v-for="i in 3" :key="i" class="h-16 bg-gray-50 animate-pulse rounded-xl"></div>
          </div>

          <div v-else-if="documents.length === 0" class="text-center py-12">
            <div class="w-16 h-16 bg-[#F5F0E8] rounded-full flex items-center justify-center mx-auto text-[#8B7340] mb-4">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-gray-500 font-medium">Aucun document téléchargé pour le moment.</p>
          </div>

          <div v-else class="space-y-4">
            <div v-for="doc in documents" :key="doc.id"
              class="flex items-center justify-between p-4 rounded-2xl border transition-all"
              :class="[statusBorderClass(doc.statut_verification)]"
            >
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-[#F5F0E8] flex items-center justify-center text-[#8B7340]">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                  <p class="font-bold text-gray-900">{{ docTypes[doc.type] || doc.type }}</p>
                  <p class="text-xs text-gray-400 font-medium">{{ formatDate(doc.created_at) }}</p>
                  <p v-if="doc.statut_verification === 'rejete' && doc.motif_rejet" class="text-xs text-red-500 font-medium mt-1">
                    Motif : {{ doc.motif_rejet }}
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <span class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider"
                  :class="[statusClass(doc.statut_verification)]"
                >
                  {{ statusLabel(doc.statut_verification) }}
                </span>
                <button v-if="doc.statut_verification !== 'verifie'"
                  @click="deleteDocument(doc.id)"
                  class="p-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 transition-all"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-6">
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8">
          <h3 class="text-lg font-black text-gray-900 mb-6">Ajouter un document</h3>
          <form @submit.prevent="uploadDocument" class="space-y-4">
            <div class="space-y-2">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Type de document</label>
              <select v-model="form.type" required class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm font-bold text-gray-700 transition-all">
                <option value="">Sélectionner un type</option>
                <option v-for="(label, key) in docTypes" :key="key" :value="key">{{ label }}</option>
              </select>
            </div>
            <div class="space-y-2">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Fichier (PDF, JPG, PNG - max 5Mo)</label>
              <div class="relative">
                <input ref="fileInput" type="file" accept=".pdf,.jpg,.jpeg,.png" @change="onFileChange" required
                  class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-dashed border-gray-300 rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#2D5A27] file:text-white hover:file:bg-[#1E3F1A] outline-none transition-all text-sm cursor-pointer"
                >
              </div>
              <p v-if="form.fichierName" class="text-xs text-[#2D5A27] font-medium">{{ form.fichierName }}</p>
            </div>
            <button type="submit" :disabled="uploading"
              class="w-full py-4 bg-[#2D5A27] text-white rounded-2xl font-bold text-sm shadow-lg hover:shadow-[#2D5A27]/20 transition-all hover:-translate-y-0.5 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ uploading ? 'Upload en cours...' : 'Télécharger' }}
            </button>
            <p v-if="uploadError" class="text-xs text-red-500 font-medium">{{ uploadError }}</p>
          </form>
        </div>

        <div class="bg-[#F5F0E8]/50 rounded-[2rem] p-6 border border-[#2D5A27]/10">
          <h4 class="text-xs font-black text-[#2D5A27] uppercase tracking-wider mb-3">Documents requis</h4>
          <ul class="space-y-2 text-sm text-gray-600 font-medium">
            <li class="flex items-center gap-2">
              <span :class="hasDoc('carte_identite') ? 'text-green-500' : 'text-gray-300'">✓</span>
              Carte d'identité nationale
            </li>
            <li class="flex items-center gap-2">
              <span :class="hasDoc('registre_commerce') ? 'text-green-500' : 'text-gray-300'">✓</span>
              Registre de commerce
            </li>
            <li class="flex items-center gap-2">
              <span :class="hasDoc('certificat_agricole') ? 'text-green-500' : 'text-gray-300'">✓</span>
              Certificat agricole
            </li>
            <li class="flex items-center gap-2">
              <span :class="hasDoc('attestation_terrain') ? 'text-green-500' : 'text-gray-300'">✓</span>
              Attestation de terrain
            </li>
            <li class="flex items-center gap-2">
              <span :class="hasDoc('photo_exploitation') ? 'text-green-500' : 'text-gray-300'">✓</span>
              Photo de l'exploitation
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard',
  middleware: ['auth', 'role'],
})

import { ref, reactive, onMounted, computed } from 'vue'

const config = useRuntimeConfig()
const apiUrl = config.public.apiUrl || 'http://127.0.0.1:8000/api'

const documents = ref([])
const docTypes = ref({})
const loading = ref(true)
const uploading = ref(false)
const uploadError = ref('')
const fileInput = ref(null)

const userStatus = computed(() => {
  try {
    return JSON.parse(localStorage.getItem('user') || '{}').statut
  } catch { return '' }
})

const form = reactive({
  type: '',
  fichier: null,
  fichierName: '',
})

function onFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    form.fichier = file
    form.fichierName = file.name
  }
}

function hasDoc(type) {
  return documents.value.some(d => d.type === type && d.statut_verification === 'verifie')
}

function statusClass(status) {
  switch (status) {
    case 'verifie': return 'bg-green-100 text-green-700'
    case 'rejete': return 'bg-red-100 text-red-700'
    default: return 'bg-amber-100 text-amber-700'
  }
}

function statusBorderClass(status) {
  switch (status) {
    case 'verifie': return 'border-green-200 bg-green-50/30'
    case 'rejete': return 'border-red-200 bg-red-50/30'
    default: return 'border-amber-200 bg-amber-50/30'
  }
}

function statusLabel(status) {
  switch (status) {
    case 'verifie': return 'Vérifié'
    case 'rejete': return 'Rejeté'
    default: return 'En attente'
  }
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('fr-FR', {
    day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
  })
}

async function loadDocuments() {
  const token = localStorage.getItem('token')
  try {
    const res = await $fetch(`${apiUrl}/producteur/documents`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    documents.value = res.documents || []
    docTypes.value = res.types || {}
  } catch (err) {
    console.error('Erreur chargement documents:', err)
  } finally {
    loading.value = false
  }
}

async function uploadDocument() {
  if (!form.type || !form.fichier) return
  uploading.value = true
  uploadError.value = ''

  const token = localStorage.getItem('token')
  const formData = new FormData()
  formData.append('type', form.type)
  formData.append('fichier', form.fichier)

  try {
    await $fetch(`${apiUrl}/producteur/documents`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` },
      body: formData,
    })
    form.type = ''
    form.fichier = null
    form.fichierName = ''
    if (fileInput.value) fileInput.value.value = ''
    await loadDocuments()
  } catch (err) {
    uploadError.value = err.data?.message || "Erreur lors de l'upload"
  } finally {
    uploading.value = false
  }
}

async function deleteDocument(id) {
  const token = localStorage.getItem('token')
  try {
    await $fetch(`${apiUrl}/producteur/documents/${id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token}` }
    })
    await loadDocuments()
  } catch (err) {
    console.error('Erreur suppression document:', err)
  }
}

onMounted(loadDocuments)
</script>
