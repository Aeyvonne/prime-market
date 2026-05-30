<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- Header Page -->
    <div class="bg-gradient-to-br from-[#2D5A27] to-[#1E3F1A] rounded-[2.5rem] p-8 md:p-12 text-white relative overflow-hidden shadow-xl shadow-[#2D5A27]/10">
      <div class="absolute -right-24 -bottom-24 w-96 h-96 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
      <div class="absolute -left-12 -top-12 w-64 h-64 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
      
      <div class="relative z-10 max-w-3xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
          <span class="px-4 py-1.5 rounded-full bg-[#8B7340] text-white text-[10px] font-black uppercase tracking-[0.2em] mb-4 inline-block">
            Secteur {{ currentSector }}
          </span>
          <h1 class="text-3xl md:text-4xl font-black font-serif leading-tight mb-2">
            Gestion des Comptes du Secteur
          </h1>
          <p class="text-white/80 text-sm md:text-base font-medium">
            Activez, suspendez ou supprimez les comptes des producteurs de votre secteur d'activité.
          </p>
        </div>
        <div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 rounded-3xl text-center min-w-[160px] self-start md:self-auto">
          <p class="text-[10px] font-black text-[#8B7340] uppercase tracking-[0.2em] mb-1">Total Secteur</p>
          <p class="text-3xl font-black">{{ accounts.length }}</p>
          <p class="text-[10px] text-white/60 font-medium mt-1">utilisateurs enregistrés</p>
        </div>
      </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="relative flex-grow max-w-md group">
        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
          <svg class="w-4 h-4 text-gray-400 group-focus-within:text-[#2D5A27] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Rechercher par nom, email..." 
          class="w-full pl-12 pr-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-[#2D5A27] outline-none transition-all text-sm font-medium"
        >
      </div>

      <!-- Filtres par statut (Tous, Actif, Suspendu, En attente) -->
      <div class="flex flex-wrap items-center gap-3">
        <button 
          v-for="filter in statusFilters" 
          :key="filter.value"
          @click="activeFilter = filter.value"
          class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all duration-300"
          :class="[
            activeFilter === filter.value 
              ? 'bg-[#2D5A27] text-white shadow-md shadow-[#2D5A27]/10' 
              : 'bg-[#F5F0E8] text-gray-600 hover:bg-[#8B7340]/10 hover:text-[#2D5A27]'
          ]"
        >
          {{ filter.label }}
        </button>
      </div>
    </div>

    <!-- Accounts List -->
    <div class="bg-white rounded-[2.5rem] border border-gray-100 overflow-hidden shadow-sm">
      <div v-if="loading" class="p-12 space-y-4">
        <!-- Skeleton Loading -->
        <div class="animate-pulse flex items-center space-x-4 p-4 border-b border-gray-50" v-for="i in 3" :key="i">
          <div class="rounded-full bg-gray-200 h-12 w-12"></div>
          <div class="flex-1 space-y-2 py-1">
            <div class="h-4 bg-gray-200 rounded w-1/4"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
          </div>
          <div class="h-8 bg-gray-200 rounded w-20"></div>
        </div>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-55/50 border-b border-gray-100">
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Utilisateur (Nom / Prénom)</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Email</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Rôle</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date d'inscription</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="user in filteredAccounts" :key="user.id" class="hover:bg-[#F5F0E8]/20 transition-all duration-300">
              <!-- Nom -->
              <td class="px-8 py-5">
                <div class="flex items-center gap-4">
                  <div class="w-11 h-11 rounded-2xl bg-[#2D5A27]/10 text-[#2D5A27] flex items-center justify-center font-black text-base shadow-inner">
                    {{ user.prenom?.[0] || 'U' }}
                  </div>
                  <div>
                    <h3 class="text-sm font-black text-gray-900 leading-snug">{{ user.prenom }} {{ user.nom }}</h3>
                    <p class="text-xs text-gray-400 font-medium">ID: PM-{{ 1000 + user.id }}</p>
                  </div>
                </div>
              </td>
              <!-- Email -->
              <td class="px-8 py-5 text-sm font-medium text-gray-700">
                {{ user.email }}
              </td>
              <!-- Rôle -->
              <td class="px-8 py-5">
                <span class="text-xs font-bold bg-[#F5F0E8] text-[#8B7340] px-3.5 py-1.5 rounded-full uppercase tracking-wider">
                  {{ user.role === 'producteur' ? 'Producteur' : user.role }}
                </span>
              </td>
              <!-- Date inscription -->
              <td class="px-8 py-5">
                <span class="text-xs font-bold text-gray-500">{{ formatDate(user.created_at) }}</span>
              </td>
              <!-- Badge statut coloré (actif = vert, suspendu = orange, en_attente = gris) -->
              <td class="px-8 py-5 text-center">
                <span 
                  class="inline-flex px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider transition-all duration-300"
                  :class="[
                    user.statut === 'actif' ? 'bg-emerald-100 text-emerald-800' : 
                    user.statut === 'suspendu' ? 'bg-amber-100 text-amber-805' : 
                    'bg-gray-100 text-gray-500'
                  ]"
                >
                  {{ user.statut === 'en_attente' ? 'En attente' : user.statut }}
                </span>
              </td>
              <!-- Actions (Activer (vert), Suspendre (orange), Supprimer (rouge)) -->
              <td class="px-8 py-5 text-right">
                <div class="flex items-center justify-end gap-2">
                  <!-- Activer (vert) -->
                  <button 
                    v-if="user.statut !== 'actif'"
                    @click="updateStatus(user.id, 'activer')"
                    class="p-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition-all hover:scale-105 active:scale-95 group relative"
                    title="Activer"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                  </button>

                  <!-- Suspendre (orange) -->
                  <button 
                    v-if="user.statut !== 'suspendu'"
                    @click="updateStatus(user.id, 'suspendre')"
                    class="p-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-600 transition-all hover:scale-105 active:scale-95 group relative"
                    title="Suspendre"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                  </button>

                  <!-- Documents (bleu) : visible pour les comptes en attente -->
                  <NuxtLink 
                    v-if="user.statut === 'en_attente'"
                    :to="`/admin/sectoriel/comptes/${user.id}/documents`"
                    class="p-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 transition-all hover:scale-105 active:scale-95 inline-flex"
                    title="Voir les documents"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                  </NuxtLink>

                  <!-- Supprimer (rouge) -->
                  <button 
                    @click="confirmDelete(user)"
                    class="p-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 transition-all hover:scale-105 active:scale-95"
                    title="Supprimer"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredAccounts.length === 0">
              <td colspan="6" class="px-8 py-16 text-center">
                <div class="max-w-md mx-auto space-y-3">
                  <div class="w-16 h-16 bg-[#F5F0E8] rounded-full flex items-center justify-center mx-auto text-[#8B7340]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                  </div>
                  <h3 class="text-base font-black text-gray-800 font-serif">Aucun producteur trouvé</h3>
                  <p class="text-xs text-gray-400 font-medium leading-relaxed">
                    Aucun compte producteur ne correspond à vos critères de filtrage ou à votre recherche pour le secteur {{ currentSector }}.
                  </p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div v-if="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-[2.5rem] p-8 max-w-md w-full border border-gray-100 shadow-2xl animate-in zoom-in duration-300 space-y-6">
        <div class="text-center space-y-3">
          <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          </div>
          <h3 class="text-xl font-black text-gray-900 font-serif">Supprimer le producteur ?</h3>
          <p class="text-xs text-gray-500 leading-relaxed">
            Êtes-vous sûr de vouloir supprimer définitivement le compte de 
            <span class="font-bold text-gray-900">{{ selectedUserForDelete?.prenom }} {{ selectedUserForDelete?.nom }}</span> ? 
            Cette action est irréversible et supprimera toutes les données associées.
          </p>
        </div>

        <div class="flex gap-4">
          <button 
            @click="deleteModalOpen = false" 
            class="flex-1 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl text-xs transition-colors"
          >
            Annuler
          </button>
          <button 
            @click="deleteAccount" 
            class="flex-1 py-3.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-2xl text-xs transition-colors shadow-lg shadow-red-600/10"
          >
            Supprimer
          </button>
        </div>
      </div>
    </div>

    <!-- Notification Toast System -->
    <div class="fixed bottom-6 right-6 z-50 space-y-2">
      <div 
        v-for="toast in toasts" 
        :key="toast.id" 
        class="flex items-center gap-3 px-6 py-4 rounded-2xl text-white shadow-2xl border transition-all duration-500 animate-in slide-in-from-bottom duration-300"
        :class="[
          toast.type === 'success' ? 'bg-[#2D5A27] border-[#8B7340]' : 'bg-red-600 border-red-700'
        ]"
      >
        <span class="text-xs font-bold">{{ toast.message }}</span>
        <button @click="removeToast(toast.id)" class="text-white/60 hover:text-white transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard'
})

import { ref, onMounted, computed } from 'vue'

const currentSector = ref('Agriculture')
const accounts = ref([])
const loading = ref(true)
const searchQuery = ref('')
const activeFilter = ref('tous')
const deleteModalOpen = ref(false)
const selectedUserForDelete = ref(null)
const toasts = ref([])

const statusFilters = [
  { label: 'Tous', value: 'tous' },
  { label: 'Actifs', value: 'actif' },
  { label: 'Suspendus', value: 'suspendu' },
  { label: 'En attente', value: 'en_attente' }
]

function addToast(message, type = 'success') {
  const id = Date.now()
  toasts.value.push({ id, message, type })
  setTimeout(() => removeToast(id), 5000)
}

function removeToast(id) {
  toasts.value = toasts.value.filter(t => t.id !== id)
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  })
}

// Charger les comptes producteurs
async function loadAccounts() {
  loading.value = true
  const token = localStorage.getItem('token')
  try {
    const config = useRuntimeConfig()
    const apiUrl = config.public.apiUrl || 'http://127.0.0.1:8000/api'

    const res = await $fetch(`${apiUrl}/admin-sectoriel/comptes`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    accounts.value = res.comptes || []
    if (res.secteur) {
      currentSector.value = res.secteur
    }
  } catch (err) {
    console.error('Erreur chargement comptes:', err)
    addToast('Impossible de récupérer les comptes producteurs.', 'error')
  } finally {
    loading.value = false
  }
}

// Activer / Suspendre un compte
async function updateStatus(id, action) {
  const token = localStorage.getItem('token')
  try {
    const config = useRuntimeConfig()
    const apiUrl = config.public.apiUrl || 'http://127.0.0.1:8000/api'

    const res = await $fetch(`${apiUrl}/admin-sectoriel/comptes/${id}/${action}`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}` }
    })

    addToast(res.message || `Compte mis à jour avec succès.`, 'success')

    if (res.user) {
      const index = accounts.value.findIndex(a => a.id === id)
      if (index !== -1) {
        accounts.value[index] = {
          ...accounts.value[index],
          ...res.user,
          secteur_travail: res.user.producteur?.secteur_travail || accounts.value[index].secteur_travail,
        }
      }
    } else {
      await loadAccounts()
    }
  } catch (err) {
    console.error(`Erreur ${action} compte:`, err)
    addToast(`Erreur lors de la mise à jour du compte.`, 'error')
  }
}

// Supprimer un compte
async function deleteAccount() {
  if (!selectedUserForDelete.value) return
  const id = selectedUserForDelete.value.id
  const token = localStorage.getItem('token')

  try {
    const config = useRuntimeConfig()
    const apiUrl = config.public.apiUrl || 'http://127.0.0.1:8000/api'

    const res = await $fetch(`${apiUrl}/admin-sectoriel/comptes/${id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token}` }
    })

    addToast(res.message || 'Compte supprimé avec succès.', 'success')
    accounts.value = accounts.value.filter(a => a.id !== id)
    deleteModalOpen.value = false
    selectedUserForDelete.value = null
  } catch (err) {
    console.error('Erreur suppression compte:', err)
    addToast('Impossible de supprimer ce compte.', 'error')
  }
}

// Filtrage & Recherche locale
const filteredAccounts = computed(() => {
  let result = accounts.value

  if (activeFilter.value !== 'tous') {
    result = result.filter(a => a.statut === activeFilter.value)
  }

  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    result = result.filter(a => {
      return (
        (a.prenom && a.prenom.toLowerCase().includes(query)) ||
        (a.nom && a.nom.toLowerCase().includes(query)) ||
        (a.email && a.email.toLowerCase().includes(query))
      )
    })
  }

  return result
})

onMounted(async () => {
  await loadAccounts()
})
</script>
