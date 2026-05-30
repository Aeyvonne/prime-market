<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <div>
      <h1 class="text-3xl font-black text-gray-900 font-serif">Ventes</h1>
      <p class="text-gray-500 font-medium mt-1">Commandes reçues sur vos produits.</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <p class="text-gray-400 font-medium italic">Chargement...</p>
    </div>

    <div v-else-if="error" class="text-center py-20">
      <p class="text-red-400 font-medium">{{ error }}</p>
    </div>

    <template v-else>
      <div class="flex flex-wrap gap-2">
        <button v-for="f in ['Toutes', 'EnCours', 'Valider', 'Annuler']" :key="f"
          @click="filtre = f"
          :class="`px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all ${
            filtre === f
              ? 'bg-[#2D5A27] text-white shadow-lg'
              : 'bg-white text-gray-500 hover:bg-gray-50 border border-gray-200'
          }`">
          {{ f === 'Toutes' ? 'Toutes' : f }}
        </button>
      </div>

      <div v-if="filtered.length === 0" class="text-center py-20">
        <p class="text-gray-400 font-medium italic">Aucune vente trouvée.</p>
      </div>

      <div v-for="cmd in filtered" :key="cmd.id"
        class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 hover:shadow-xl transition-all">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
              <h3 class="text-lg font-black text-gray-900">Commande #{{ cmd.id }}</h3>
              <span :class="`inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider ${statusStyle(cmd.status)}`">{{ cmd.status }}</span>
            </div>
            <div class="flex flex-wrap gap-x-6 gap-y-1 text-sm text-gray-600">
              <span><span class="font-bold text-gray-900">Acheteur :</span> {{ cmd.acheteur?.prenom }} {{ cmd.acheteur?.nom }}</span>
              <span><span class="font-bold text-gray-900">Date :</span> {{ formatDate(cmd.date_commande) }}</span>
              <span><span class="font-bold text-gray-900">Montant :</span> <span class="font-black text-[#2D5A27]">{{ formatPrice(cmd.montant_total) }}</span></span>
            </div>
            <div v-if="cmd.lignes_commande" class="flex flex-wrap gap-x-4 gap-y-1 mt-2 text-xs text-gray-500">
              <span v-for="l in cmd.lignes_commande" :key="l.id">{{ l.produit?.nom }} × {{ l.quantite }}</span>
            </div>
            <div v-if="cmd.motif_annulation" class="mt-2 text-xs text-red-500 bg-red-50 rounded-lg px-3 py-1.5 inline-block">
              Motif : {{ cmd.motif_annulation }}
            </div>
          </div>

          <div class="flex gap-2 flex-shrink-0" v-if="cmd.status === 'EnCours'">
            <button @click="openConfirmModal(cmd)"
              class="px-5 py-2.5 bg-emerald-500 text-white rounded-xl text-xs font-bold hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-200 active:scale-95">
              Confirmer
            </button>
            <button @click="openRefuseModal(cmd)"
              class="px-5 py-2.5 bg-red-500 text-white rounded-xl text-xs font-bold hover:bg-red-600 transition-all shadow-lg shadow-red-200 active:scale-95">
              Refuser
            </button>
          </div>
        </div>
      </div>
    </template>

    <!-- Modal Confirmation -->
    <Teleport to="body">
      <div v-if="modalConfirm.open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="modalConfirm.open = false">
        <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 w-full max-w-md mx-4 animate-in fade-in zoom-in-95 duration-200">
          <h3 class="text-xl font-black text-gray-900 mb-2">Confirmer la commande #{{ modalConfirm.cmd?.id }}</h3>
          <p class="text-sm text-gray-500 mb-6">Cette action va confirmer la commande, déduire le stock et créer une livraison.</p>

          <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6">
            <p class="text-xs font-bold text-amber-700">Vérification des stocks</p>
            <div v-for="l in modalConfirm.cmd?.lignes_commande" :key="l.id"
              class="flex justify-between text-xs mt-2"
              :class="stockOk(l) ? 'text-gray-600' : 'text-red-600 font-bold'">
              <span>{{ l.produit?.nom }} × {{ l.quantite }}</span>
              <span>{{ l.produit?.quantite ?? '?' }} dispo</span>
            </div>
          </div>

          <div class="flex gap-3">
            <button @click="confirmerAction"
              :disabled="confirmLoading"
              class="flex-1 px-6 py-3 bg-emerald-500 text-white rounded-2xl font-bold text-sm hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-200 active:scale-95 disabled:opacity-50">
              {{ confirmLoading ? 'Traitement...' : 'Oui, confirmer' }}
            </button>
            <button @click="modalConfirm.open = false"
              class="flex-1 px-6 py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-200 transition-all">
              Annuler
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Modal Refus -->
    <Teleport to="body">
      <div v-if="modalRefuse.open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="modalRefuse.open = false">
        <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 w-full max-w-md mx-4 animate-in fade-in zoom-in-95 duration-200">
          <h3 class="text-xl font-black text-gray-900 mb-2">Refuser la commande #{{ modalRefuse.cmd?.id }}</h3>
          <p class="text-sm text-gray-500 mb-4">Veuillez préciser le motif du refus (optionnel).</p>

          <textarea v-model="modalRefuse.motif"
            placeholder="Motif du refus..."
            class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm font-medium resize-none focus:outline-none focus:border-[#2D5A27] focus:ring-1 focus:ring-[#2D5A27] transition-all"
            rows="3" />

          <div class="flex gap-3 mt-6">
            <button @click="refuserAction"
              :disabled="refuseLoading"
              class="flex-1 px-6 py-3 bg-red-500 text-white rounded-2xl font-bold text-sm hover:bg-red-600 transition-all shadow-lg shadow-red-200 active:scale-95 disabled:opacity-50">
              {{ refuseLoading ? 'Traitement...' : 'Oui, refuser' }}
            </button>
            <button @click="modalRefuse.open = false"
              class="flex-1 px-6 py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-200 transition-all">
              Annuler
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

import { ref, reactive, computed, onMounted } from 'vue'

const ventes = ref([])
const filtre = ref('Toutes')
const loading = ref(true)
const error = ref(null)
const confirmLoading = ref(false)
const refuseLoading = ref(false)

const modalConfirm = reactive({ open: false, cmd: null })
const modalRefuse = reactive({ open: false, cmd: null, motif: '' })

const filtered = computed(() => {
  if (filtre.value === 'Toutes') return ventes.value
  return ventes.value.filter(c => c.status === filtre.value)
})

function stockOk(ligne) {
  return (ligne.produit?.quantite ?? 0) >= ligne.quantite
}

function openConfirmModal(cmd) {
  modalConfirm.cmd = cmd
  modalConfirm.open = true
}

function openRefuseModal(cmd) {
  modalRefuse.cmd = cmd
  modalRefuse.motif = ''
  modalRefuse.open = true
}

async function fetchVentes() {
  const token = localStorage.getItem('token')
  if (!token) return navigateTo('/login')
  loading.value = true
  error.value = null
  try {
    ventes.value = await $fetch('http://127.0.0.1:8000/api/distributeur/ventes', {
      headers: { Authorization: `Bearer ${token}` }
    })
  } catch (err) {
    console.error('Erreur chargement ventes:', err)
    error.value = 'Erreur lors du chargement des ventes.'
  } finally {
    loading.value = false
  }
}

async function confirmerAction() {
  const token = localStorage.getItem('token')
  const id = modalConfirm.cmd?.id
  if (!id) return
  confirmLoading.value = true
  try {
    const res = await $fetch(`http://127.0.0.1:8000/api/distributeur/ventes/${id}/confirmer`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}` }
    })
    const idx = ventes.value.findIndex(c => c.id === id)
    if (idx !== -1) ventes.value[idx] = res.commande
    modalConfirm.open = false
  } catch (err) {
    console.error('Erreur confirmation:', err)
    const msg = err.data?.message || 'Erreur lors de la confirmation.'
    alert(msg)
  } finally {
    confirmLoading.value = false
  }
}

async function refuserAction() {
  const token = localStorage.getItem('token')
  const id = modalRefuse.cmd?.id
  if (!id) return
  refuseLoading.value = true
  try {
    const res = await $fetch(`http://127.0.0.1:8000/api/distributeur/ventes/${id}/refuser`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}` },
      body: { motif: modalRefuse.motif || undefined },
    })
    const idx = ventes.value.findIndex(c => c.id === id)
    if (idx !== -1) ventes.value[idx] = res.commande
    modalRefuse.open = false
  } catch (err) {
    console.error('Erreur refus:', err)
    alert(err.data?.message || 'Erreur lors du refus.')
  } finally {
    refuseLoading.value = false
  }
}

function statusStyle(status) {
  switch (status) {
    case 'Valider': return 'bg-emerald-100 text-emerald-700'
    case 'Annuler': return 'bg-red-100 text-red-700'
    case 'EnCours': return 'bg-amber-100 text-amber-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

function formatPrice(price) {
  if (!price) return '0 F'
  return new Intl.NumberFormat('fr-FR').format(price) + ' F'
}

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(fetchVentes)
</script>
