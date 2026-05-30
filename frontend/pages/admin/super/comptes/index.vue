<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 font-serif">
          Gestion des <span class="text-[#2D5A27]">Comptes</span> 👥
        </h1>
        <p class="text-gray-500 font-medium mt-1">Créez, modifiez, suspendez ou supprimez des comptes utilisateurs.</p>
      </div>
      <div>
        <button 
          @click="openAddModal" 
          class="px-6 py-4 bg-[#2D5A27] text-white rounded-2xl font-black text-sm shadow-xl shadow-[#2D5A27]/20 hover:-translate-y-0.5 active:scale-95 transition-all flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
          Ajouter un compte
        </button>
      </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 items-center justify-between">
      <div class="relative w-full md:max-w-xs group">
        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
          <svg class="w-4 h-4 text-gray-400 group-focus-within:text-[#2D5A27] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <input 
          v-model="filters.search" 
          type="text" 
          placeholder="Rechercher par nom, email..." 
          class="w-full pl-12 pr-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:bg-white focus:border-[#2D5A27] outline-none transition-all text-sm font-medium"
        >
      </div>

      <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
        <!-- Role filter -->
        <select v-model="filters.role" class="px-6 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-bold text-gray-700 text-xs">
          <option value="">Tous les Rôles</option>
          <option value="producteur">Producteur</option>
          <option value="distributeur">Distributeur</option>
          <option value="consommateur">Consommateur</option>
          <option value="transporteur">Transporteur</option>
          <option value="admin_sectoriel">Admin Sectoriel</option>
          <option value="super_administrateur">Super Admin</option>
        </select>

        <!-- Status filter -->
        <select v-model="filters.status" class="px-6 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-bold text-gray-700 text-xs">
          <option value="">Tous les Statuts</option>
          <option value="actif">Actif</option>
          <option value="en_attente">En attente</option>
          <option value="suspendu">Suspendu</option>
        </select>
      </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
      <div v-if="loading" class="p-12 space-y-4">
        <div v-for="i in 5" :key="i" class="h-14 bg-gray-50 animate-pulse rounded-xl"></div>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Utilisateur</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Email</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Téléphone</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Rôle</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-[#F5F0E8]/30 transition-colors group">
              <td class="px-8 py-5">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-[#2D5A27]/10 flex items-center justify-center text-[#2D5A27] text-sm font-black">
                    {{ user.nom?.[0]?.toUpperCase() || 'U' }}
                  </div>
                  <div>
                    <span class="text-sm font-bold text-gray-900 block">{{ user.prenom }} {{ user.nom }}</span>
                    <span class="text-[10px] text-gray-400 font-medium">Inscrit le {{ formatDate(user.created_at) }}</span>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5 text-sm text-gray-600 font-medium">{{ user.email }}</td>
              <td class="px-8 py-5 text-sm text-gray-600 font-bold">{{ user.telephone || '—' }}</td>
              <td class="px-8 py-5">
                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-[10px] font-black uppercase tracking-wider">
                  {{ formatRole(user.role) }}
                </span>
              </td>
              <td class="px-8 py-5 text-center">
                <span :class="`inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider ${statusStyle(user.statut)}`">
                  {{ user.statut }}
                </span>
              </td>
              <td class="px-8 py-5 text-right space-x-2">
                <button 
                  @click="toggleStatus(user)" 
                  class="px-3 py-1.5 text-[10px] font-black rounded-lg transition-colors border"
                  :class="user.statut === 'actif' ? 'bg-amber-50 text-amber-600 border-amber-200 hover:bg-amber-100' : 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100'"
                >
                  {{ user.statut === 'actif' ? 'Suspendre' : 'Activer' }}
                </button>
                <button @click="openEditModal(user)" class="text-xs font-bold text-[#8B7340] hover:underline">Modifier</button>
                <button @click="confirmDelete(user)" class="text-xs font-bold text-red-500 hover:underline">Supprimer</button>
              </td>
            </tr>
            <tr v-if="filteredUsers.length === 0">
              <td colspan="6" class="px-8 py-12 text-center text-gray-400 italic">
                Aucun utilisateur trouvé.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Ajouter / Modifier -->
    <div v-if="modal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-in fade-in duration-300">
      <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 max-w-lg w-full max-h-[90vh] overflow-y-auto animate-in zoom-in-95 duration-300">
        <h3 class="text-2xl font-black font-serif mb-6 text-gray-900">
          {{ modal.isEdit ? 'Modifier le Compte' : 'Ajouter un Compte' }}
        </h3>
        
        <form @submit.prevent="saveUser" class="space-y-6">
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Prénom *</label>
              <input v-model="form.prenom" required class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm transition-all">
            </div>
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Nom *</label>
              <input v-model="form.nom" required class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm transition-all">
            </div>
          </div>

          <div class="space-y-1">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Email *</label>
            <input v-model="form.email" type="email" required class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm transition-all">
          </div>

          <div class="space-y-1">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Téléphone *</label>
            <input v-model="form.telephone" required class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm transition-all">
          </div>

          <div class="space-y-1">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Mot de passe {{ modal.isEdit ? '(Optionnel)' : '*' }}</label>
            <input v-model="form.password" type="password" :required="!modal.isEdit" class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm transition-all">
          </div>

          <div class="grid grid-cols-2 gap-4" v-if="!modal.isEdit">
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Rôle *</label>
              <select v-model="form.role" required class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm font-bold text-gray-700 transition-all">
                <option value="producteur">Producteur</option>
                <option value="distributeur">Distributeur</option>
                <option value="consommateur">Consommateur</option>
                <option value="transporteur">Transporteur</option>
                <option value="admin_sectoriel">Admin Sectoriel</option>
                <option value="super_administrateur">Super Admin</option>
              </select>
            </div>
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Statut *</label>
              <select v-model="form.statut" required class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm font-bold text-gray-700 transition-all">
                <option value="actif">Actif</option>
                <option value="en_attente">En attente</option>
                <option value="suspendu">Suspendu</option>
              </select>
            </div>
          </div>

          <!-- Dynamic Role fields for creation -->
          <div v-if="!modal.isEdit" class="space-y-4 pt-2 border-t border-gray-100">
            <!-- Producteur specific fields -->
            <div v-if="form.role === 'producteur'" class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Secteur d'activité *</label>
              <select v-model="form.secteur_travail" required class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm font-bold text-gray-700 transition-all">
                <option value="Agriculture">Agriculture</option>
                <option value="Elevage">Elevage</option>
                <option value="Peche">Peche</option>
              </select>
            </div>

            <!-- Admin Sectoriel specific fields -->
            <div v-if="form.role === 'admin_sectoriel'" class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Secteur *</label>
              <select v-model="form.secteur" required class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm font-bold text-gray-700 transition-all">
                <option value="Agriculture">Agriculture</option>
                <option value="Elevage">Elevage</option>
                <option value="Peche">Peche</option>
              </select>
            </div>

            <!-- Distributeur specific fields -->
            <div v-if="form.role === 'distributeur'" class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Type de Distributeur *</label>
              <select v-model="form.type_distributeur" required class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm font-bold text-gray-700 transition-all">
                <option value="Grossiste">Grossiste</option>
                <option value="Detaillant">Détaillant</option>
              </select>
            </div>

            <!-- Transporteur specific fields -->
            <div v-if="form.role === 'transporteur'" class="grid grid-cols-2 gap-4">
              <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Type de véhicule *</label>
                <select v-model="form.type_vehicule" required class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm font-bold text-gray-700 transition-all">
                  <option value="Camion">Camion</option>
                  <option value="Voiture">Voiture</option>
                  <option value="Moto">Moto</option>
                </select>
              </div>
              <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Zone d'intervention</label>
                <input v-model="form.zone_intervention" placeholder="Ex: Dakar, Thies" class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm transition-all">
              </div>
            </div>
          </div>

          <div class="space-y-1">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Adresse</label>
            <textarea v-model="form.adresse" rows="2" class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm transition-all resize-none"></textarea>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-50">
            <button type="button" @click="modal.show = false" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">Annuler</button>
            <button type="submit" class="px-8 py-3 bg-[#2D5A27] text-white rounded-xl font-black text-sm shadow-lg hover:shadow-[#2D5A27]/25 transition-all">
              Enregistrer
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Alert Modal popup -->
    <div v-if="alertPopup.show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-in fade-in duration-300">
      <div class="bg-white rounded-[2rem] shadow-2xl p-8 max-w-sm w-full text-center animate-in zoom-in-95 duration-300">
        <div class="w-20 h-20 mx-auto rounded-full bg-red-100 text-red-500 flex items-center justify-center mb-6">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-2xl font-black font-serif mb-2 text-gray-900">
          Supprimer ?
        </h3>
        <p class="text-gray-500 font-medium mb-8">Êtes-vous sûr de vouloir supprimer définitivement le compte de <strong>{{ alertPopup.user?.prenom }} {{ alertPopup.user?.nom }}</strong> ?</p>
        <div class="flex items-center gap-4">
          <button @click="alertPopup.show = false" class="flex-1 py-4 bg-gray-100 text-gray-600 rounded-2xl font-black transition-all hover:bg-gray-200">Annuler</button>
          <button @click="deleteUser" class="flex-1 py-4 bg-red-500 text-white rounded-2xl font-black transition-all hover:bg-red-600">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard'
})

import { ref, reactive, computed, onMounted } from 'vue'

const users = ref([])
const loading = ref(true)
const filters = reactive({ search: '', role: '', status: '' })

const modal = reactive({ show: false, isEdit: false, userId: null })
const alertPopup = reactive({ show: false, user: null })

const form = reactive({
  nom: '',
  prenom: '',
  email: '',
  password: '',
  telephone: '',
  role: 'consommateur',
  statut: 'actif',
  adresse: '',
  // Conditional fields
  secteur: 'Agriculture',
  secteur_travail: 'Agriculture',
  type_distributeur: 'Grossiste',
  type_vehicule: 'Camion',
  zone_intervention: ''
})

async function fetchUsers() {
  loading.value = true
  try {
    const data = await $fetch('http://127.0.0.1:8000/api/super-admin/comptes', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    users.value = data || []
  } catch (err) {
    console.error('Erreur chargement utilisateurs:', err)
  } finally {
    loading.value = false
  }
}

onMounted(fetchUsers)

const filteredUsers = computed(() => {
  return users.value.filter(u => {
    const searchMatch = !filters.search || 
      `${u.prenom} ${u.nom}`.toLowerCase().includes(filters.search.toLowerCase()) || 
      u.email.toLowerCase().includes(filters.search.toLowerCase())
    
    const roleMatch = !filters.role || u.role === filters.role
    const statusMatch = !filters.status || u.statut === filters.status

    return searchMatch && roleMatch && statusMatch
  })
})

function resetForm() {
  form.nom = ''
  form.prenom = ''
  form.email = ''
  form.password = ''
  form.telephone = ''
  form.role = 'consommateur'
  form.statut = 'actif'
  form.adresse = ''
  form.secteur = 'Agriculture'
  form.secteur_travail = 'Agriculture'
  form.type_distributeur = 'Grossiste'
  form.type_vehicule = 'Camion'
  form.zone_intervention = ''
}

function openAddModal() {
  resetForm()
  modal.isEdit = false
  modal.userId = null
  modal.show = true
}

function openEditModal(user) {
  modal.isEdit = true
  modal.userId = user.id
  form.nom = user.nom
  form.prenom = user.prenom
  form.email = user.email
  form.password = ''
  form.telephone = user.telephone
  form.role = user.role
  form.statut = user.statut
  form.adresse = user.adresse || ''
  modal.show = true
}

async function saveUser() {
  const token = localStorage.getItem('token')
  try {
    if (modal.isEdit) {
      // Modification
      await $fetch(`http://127.0.0.1:8000/api/super-admin/comptes/${modal.userId}`, {
        method: 'PUT',
        headers: { Authorization: `Bearer ${token}` },
        body: {
          nom: form.nom,
          prenom: form.prenom,
          email: form.email,
          telephone: form.telephone,
          statut: form.statut,
          adresse: form.adresse,
          password: form.password || undefined
        }
      })
    } else {
      // Ajout
      await $fetch('http://127.0.0.1:8000/api/super-admin/comptes', {
        method: 'POST',
        headers: { Authorization: `Bearer ${token}` },
        body: form
      })
    }
    modal.show = false
    await fetchUsers()
  } catch (err) {
    alert(err.data?.message || 'Une erreur est survenue.')
  }
}

async function toggleStatus(user) {
  const token = localStorage.getItem('token')
  const newStatus = user.statut === 'actif' ? 'suspendu' : 'actif'
  try {
    await $fetch(`http://127.0.0.1:8000/api/super-admin/comptes/${user.id}`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}` },
      body: { statut: newStatus }
    })
    await fetchUsers()
  } catch (err) {
    console.error('Erreur bascule statut:', err)
  }
}

function confirmDelete(user) {
  alertPopup.user = user
  alertPopup.show = true
}

async function deleteUser() {
  if (!alertPopup.user) return
  const token = localStorage.getItem('token')
  try {
    await $fetch(`http://127.0.0.1:8000/api/super-admin/comptes/${alertPopup.user.id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token}` }
    })
    alertPopup.show = false
    alertPopup.user = null
    await fetchUsers()
  } catch (err) {
    alert(err.data?.message || 'Erreur lors de la suppression.')
  }
}

function statusStyle(status) {
  switch (status) {
    case 'actif': return 'bg-green-100 text-green-700'
    case 'suspendu': return 'bg-red-100 text-red-700'
    case 'en_attente': return 'bg-amber-100 text-amber-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

function formatRole(role) {
  const roles = {
    producteur: 'Producteur',
    distributeur: 'Distributeur',
    consommateur: 'Consommateur',
    transporteur: 'Transporteur',
    admin_sectoriel: 'Admin Sectoriel',
    super_administrateur: 'Super Admin'
  }
  return roles[role] || role
}

function formatDate(dateStr) {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}
</script>
