<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <div class="flex items-center justify-between">
      <div>
        <NuxtLink to="/admin/sectoriel/validations" class="text-sm font-bold text-[#8B7340] hover:underline mb-2 inline-block">← Retour aux validations</NuxtLink>
        <h1 class="text-3xl font-black text-gray-900 font-serif">Documents de {{ producteur?.prenom }} {{ producteur?.nom }}</h1>
        <p class="text-gray-500 font-medium">{{ producteur?.email }} — {{ producteur?.secteur_travail }}</p>
      </div>
      <span class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider"
        :class="producteur?.statut === 'actif' ? 'bg-green-100 text-green-700' : producteur?.statut === 'suspendu' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'"
      >{{ producteur?.statut }}</span>
    </div>

    <div v-if="loading" class="space-y-4">
      <div v-for="i in 3" :key="i" class="h-24 bg-gray-50 animate-pulse rounded-2xl"></div>
    </div>

    <div v-else-if="documents.length === 0" class="bg-white rounded-[2.5rem] p-12 text-center border border-gray-100 shadow-sm">
      <p class="text-gray-500 font-medium">Aucun document trouvé pour ce producteur.</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="doc in documents" :key="doc.id"
        class="bg-white rounded-[2.5rem] border p-6 shadow-sm transition-all"
        :class="doc.statut_verification === 'verifie' ? 'border-green-200' : doc.statut_verification === 'rejete' ? 'border-red-200' : 'border-amber-200'"
      >
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="font-bold text-gray-900">{{ docTypes[doc.type] || doc.type }}</h3>
            <p class="text-xs text-gray-400 font-medium">{{ formatDate(doc.created_at) }}</p>
          </div>
          <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase"
            :class="doc.statut_verification === 'verifie' ? 'bg-green-100 text-green-700' : doc.statut_verification === 'rejete' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'"
          >{{ doc.statut_verification }}</span>
        </div>

        <a v-if="doc.fichier" :href="`http://127.0.0.1:8000/storage/${doc.fichier}`" target="_blank"
          class="inline-flex items-center gap-2 px-4 py-2 bg-[#F5F0E8] text-gray-700 rounded-xl font-bold text-xs hover:bg-[#e5e0d8] transition-colors mb-4"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          Voir le document
        </a>

        <div v-if="doc.statut_verification === 'rejete' && doc.motif_rejet" class="p-3 bg-red-50 rounded-xl text-sm text-red-600 font-medium mb-4">
          Motif : {{ doc.motif_rejet }}
        </div>

        <div v-if="doc.statut_verification !== 'verifie'" class="flex items-center gap-3 pt-2">
          <button @click="verifyDoc(doc.id)"
            class="flex-1 py-3 bg-[#2D5A27] text-white rounded-2xl font-bold text-xs shadow-lg hover:shadow-[#2D5A27]/20 transition-all active:scale-95"
          >Vérifier</button>
          <button @click="openRejectModal(doc)"
            class="flex-1 py-3 bg-red-50 text-red-600 rounded-2xl font-bold text-xs hover:bg-red-100 transition-all active:scale-95"
          >Rejeter</button>
        </div>
      </div>
    </div>

    <div v-if="rejectModal.doc" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-[2.5rem] p-8 max-w-md w-full shadow-2xl animate-in zoom-in-95 duration-300 space-y-6">
        <h3 class="text-xl font-black font-serif text-gray-900">Rejeter le document</h3>
        <p class="text-sm text-gray-500 font-medium">Motif du rejet pour <strong>{{ docTypes[rejectModal.doc.type] }}</strong> :</p>
        <textarea v-model="rejectModal.motif" rows="3" placeholder="Expliquez le motif du rejet..."
          class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-red-400 focus:bg-white outline-none text-sm transition-all resize-none"
        ></textarea>
        <div class="flex gap-3">
          <button @click="rejectModal.doc = null" class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold text-xs">Annuler</button>
          <button @click="rejectDoc" :disabled="!rejectModal.motif.trim()"
            class="flex-1 py-3 bg-red-600 text-white rounded-2xl font-bold text-xs shadow-lg disabled:opacity-50 transition-all"
          >Confirmer le rejet</button>
        </div>
      </div>
    </div>

    <!-- Bannière d'activation -->
    <div v-if="allDocumentsVerified" class="bg-gradient-to-r from-[#2D5A27] to-[#1E3F1A] rounded-[2.5rem] p-8 text-white shadow-xl">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-xl font-black font-serif">Tous les documents sont vérifiés</h3>
          <p class="text-white/70 text-sm mt-1">Activez le compte pour que le producteur puisse utiliser la plateforme.</p>
        </div>
        <button @click="activateAccount"
          class="px-8 py-4 bg-white text-[#2D5A27] rounded-2xl font-bold text-sm shadow-lg hover:shadow-white/20 transition-all hover:-translate-y-0.5 active:scale-95"
        >Activer le compte</button>
      </div>
    </div>

    <div class="fixed bottom-6 right-6 z-50 space-y-2">
      <div v-for="t in toasts" :key="t.id"
        class="flex items-center gap-3 px-6 py-4 rounded-2xl text-white shadow-2xl border transition-all animate-in slide-in-from-bottom duration-300"
        :class="t.type === 'success' ? 'bg-[#2D5A27]' : 'bg-red-600'"
      >
        <span class="text-xs font-bold">{{ t.message }}</span>
        <button @click="toasts = toasts.filter(x => x.id !== t.id)" class="text-white/60 hover:text-white">✕</button>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard', middleware: ['auth', 'role'] })

const config = useRuntimeConfig()
const apiUrl = config.public.apiUrl || 'http://127.0.0.1:8000/api'
const route = useRoute()

const producteur = ref(null)
const documents = ref([])
const docTypes = ref({})
const loading = ref(true)
const toasts = ref([])
const rejectModal = reactive({ doc: null, motif: '' })

const allDocumentsVerified = computed(() => {
  if (!documents.value.length || producteur.value?.statut === 'actif') return false
  return documents.value.every(d => d.statut_verification === 'verifie')
})

function formatDate(d) { return d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-' }
function addToast(msg, type = 'success') { const id = Date.now(); toasts.value.push({ id, message: msg, type }); setTimeout(() => toasts.value = toasts.value.filter(t => t.id !== id), 5000) }

async function activateAccount() {
  const token = localStorage.getItem('token')
  try {
    await $fetch(`${apiUrl}/admin-sectoriel/comptes/${producteur.value.id}/activer`, {
      method: 'PUT', headers: { Authorization: `Bearer ${token}` }
    })
    addToast('Compte activé avec succès ! Le producteur peut maintenant se connecter.')
    await load()
  } catch (err) { addToast('Erreur lors de l\'activation du compte.', 'error') }
}

async function load() {
  const token = localStorage.getItem('token')
  try {
    const res = await $fetch(`${apiUrl}/admin-sectoriel/comptes/${route.params.id}/documents`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    documents.value = res.documents || []
    producteur.value = res.producteur
    docTypes.value = res.types || {}
  } catch (err) { console.error(err) }
  finally { loading.value = false }
}

async function verifyDoc(id) {
  const token = localStorage.getItem('token')
  try {
    const res = await $fetch(`${apiUrl}/admin-sectoriel/documents/${id}/verifier`, {
      method: 'PUT', headers: { Authorization: `Bearer ${token}` }
    })
    addToast(res.message || 'Document vérifié.')
    await load()
  } catch (err) { addToast('Erreur lors de la vérification.', 'error') }
}

function openRejectModal(doc) {
  rejectModal.doc = doc
  rejectModal.motif = ''
}

async function rejectDoc() {
  if (!rejectModal.motif.trim()) return
  const token = localStorage.getItem('token')
  try {
    const res = await $fetch(`${apiUrl}/admin-sectoriel/documents/${rejectModal.doc.id}/rejeter`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}` },
      body: { motif_rejet: rejectModal.motif }
    })
    addToast(res.message || 'Document rejeté.')
    rejectModal.doc = null
    await load()
  } catch (err) { addToast('Erreur lors du rejet.', 'error') }
}

onMounted(load)
</script>
