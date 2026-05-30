<template>
  <div class="max-w-5xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-12">
    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-24 space-y-4">
      <div class="w-12 h-12 border-4 border-[#2D5A27] border-t-transparent rounded-full animate-spin"></div>
      <p class="text-gray-500 font-medium">Chargement de votre profil...</p>
    </div>

    <template v-else-if="user">
      <!-- Section 1 : Header Profil -->
      <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none" :class="roleColorBg"></div>
        <div class="flex items-center gap-6 relative z-10">
          <!-- Avatar -->
          <div 
            class="w-24 h-24 rounded-full flex items-center justify-center text-3xl font-black text-white shadow-xl transform hover:scale-105 transition-transform"
            :class="roleColor"
          >
            {{ initials }}
          </div>
          <!-- Infos principales -->
          <div>
            <h1 class="text-3xl font-black text-gray-900 font-serif mb-2 flex items-center gap-3">
              {{ user.prenom }} {{ user.nom }}
              <div class="w-3 h-3 rounded-full shadow-sm" :class="statusColor" :title="`Statut: ${user.statut}`"></div>
            </h1>
            <div class="flex items-center gap-3">
              <span 
                class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider text-white shadow-sm"
                :class="roleColor"
              >
                {{ formatRole(user.role) }}
              </span>
              <span class="text-sm font-medium text-gray-500">Membre depuis {{ formatDate(user.created_at || new Date()) }}</span>
            </div>
          </div>
        </div>

        <div class="relative z-10">
          <button 
            v-if="!editMode" 
            @click="toggleEdit" 
            class="w-full md:w-auto px-6 py-3 bg-[#F5F0E8] text-[#2D5A27] rounded-xl font-bold hover:bg-[#2D5A27] hover:text-white transition-all flex items-center justify-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Modifier le profil
          </button>
        </div>
      </div>

      <!-- Section 5 : Statistiques (en mode lecture uniquement) -->
      <div v-if="!editMode" class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-in fade-in duration-500">
        <div v-for="(stat, key) in formattedStats" :key="key" class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
          <div class="w-12 h-12 rounded-full bg-[#F5F0E8] text-[#8B7340] flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
          </div>
          <div>
            <p class="text-xs font-bold text-gray-400 uppercase">{{ formatStatLabel(key) }}</p>
            <p class="text-2xl font-black text-gray-900">{{ stat }}</p>
          </div>
        </div>
      </div>

      <!-- Grille Principale (Lecture ou Formulaire) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Informations Personnelles -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
          <h2 class="text-xl font-black text-[#1A1A1A] font-serif mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#8B7340]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Informations Personnelles
          </h2>
          
          <div class="space-y-6">
            <!-- Lecture -->
            <template v-if="!editMode">
              <div class="flex justify-between border-b border-gray-50 pb-4">
                <span class="text-sm font-bold text-gray-400">Nom complet</span>
                <span class="text-sm font-bold text-gray-900">{{ user.prenom }} {{ user.nom }}</span>
              </div>
              <div class="flex justify-between border-b border-gray-50 pb-4">
                <span class="text-sm font-bold text-gray-400">Email</span>
                <span class="text-sm font-bold text-gray-900">{{ user.email }}</span>
              </div>
              <div class="flex justify-between border-b border-gray-50 pb-4">
                <span class="text-sm font-bold text-gray-400">Téléphone</span>
                <span class="text-sm font-bold text-gray-900">{{ user.telephone }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm font-bold text-gray-400">Adresse</span>
                <span class="text-sm font-bold text-gray-900 text-right max-w-[200px]">{{ user.adresse || 'Non renseignée' }}</span>
              </div>
            </template>
            
            <!-- Édition -->
            <template v-else>
              <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                  <label class="text-xs font-bold text-gray-400 uppercase">Prénom</label>
                  <input v-model="form.prenom" type="text" class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium">
                </div>
                <div class="space-y-1">
                  <label class="text-xs font-bold text-gray-400 uppercase">Nom</label>
                  <input v-model="form.nom" type="text" class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium">
                </div>
              </div>
              <div class="space-y-1">
                <label class="text-xs font-bold text-gray-400 uppercase">Email</label>
                <input v-model="form.email" type="email" class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium">
              </div>
              <div class="space-y-1">
                <label class="text-xs font-bold text-gray-400 uppercase">Téléphone</label>
                <input v-model="form.telephone" type="text" class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium">
              </div>
              <div class="space-y-1">
                <label class="text-xs font-bold text-gray-400 uppercase">Adresse</label>
                <textarea v-model="form.adresse" rows="2" class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium resize-none"></textarea>
              </div>
            </template>
          </div>
        </div>

        <!-- Informations Spécifiques -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 flex flex-col">
          <h2 class="text-xl font-black text-[#1A1A1A] font-serif mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#8B7340]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Détails {{ formatRole(user.role) }}
          </h2>

          <div class="space-y-6 flex-grow">
            <!-- Lecture Producteur -->
            <template v-if="user.role === 'producteur' && !editMode">
              <div class="flex justify-between">
                <span class="text-sm font-bold text-gray-400">Secteur d'activité</span>
                <span class="text-sm font-bold text-gray-900">{{ roleData?.secteur_travail || 'Non défini' }}</span>
              </div>
            </template>
            <!-- Edition Producteur -->
            <template v-if="user.role === 'producteur' && editMode">
              <div class="space-y-1">
                <label class="text-xs font-bold text-gray-400 uppercase">Secteur d'activité</label>
                <select v-model="form.secteur_travail" class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium">
                  <option value="Agriculture">Agriculture</option>
                  <option value="Elevage">Élevage</option>
                  <option value="Peche">Pêche</option>
                </select>
              </div>
            </template>

            <!-- Lecture Distributeur -->
            <template v-if="user.role === 'distributeur' && !editMode">
              <div class="flex justify-between border-b border-gray-50 pb-4">
                <span class="text-sm font-bold text-gray-400">Type</span>
                <span class="text-sm font-bold text-gray-900">{{ roleData?.type_distributeur || 'Non défini' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm font-bold text-gray-400">Note moyenne</span>
                <div class="flex items-center text-yellow-400">
                  <svg v-for="i in 5" :key="i" class="w-4 h-4" :class="i <= (roleData?.note || 0) ? 'fill-current' : 'text-gray-200'" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </div>
              </div>
            </template>
            <!-- Edition Distributeur -->
            <template v-if="user.role === 'distributeur' && editMode">
              <div class="space-y-1">
                <label class="text-xs font-bold text-gray-400 uppercase">Type de distributeur</label>
                <select v-model="form.type_distributeur" class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium">
                  <option value="Grossiste">Grossiste</option>
                  <option value="Detaillant">Détaillant</option>
                </select>
              </div>
            </template>

            <!-- Lecture Transporteur -->
            <template v-if="user.role === 'transporteur' && !editMode">
              <div class="flex justify-between border-b border-gray-50 pb-4">
                <span class="text-sm font-bold text-gray-400">Véhicule</span>
                <span class="text-sm font-bold text-gray-900">{{ roleData?.type_vehicule || 'Non défini' }}</span>
              </div>
              <div class="flex justify-between border-b border-gray-50 pb-4">
                <span class="text-sm font-bold text-gray-400">Zone</span>
                <span class="text-sm font-bold text-gray-900">{{ roleData?.zone_intervention || 'Non définie' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm font-bold text-gray-400">Disponibilité</span>
                <span class="text-sm font-bold" :class="roleData?.disponibilite ? 'text-emerald-500' : 'text-red-500'">
                  {{ roleData?.disponibilite ? 'Disponible' : 'Indisponible' }}
                </span>
              </div>
            </template>
            <!-- Edition Transporteur -->
            <template v-if="user.role === 'transporteur' && editMode">
              <div class="space-y-1">
                <label class="text-xs font-bold text-gray-400 uppercase">Véhicule</label>
                <select v-model="form.type_vehicule" class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium">
                  <option value="Camion">Camion</option>
                  <option value="Voiture">Voiture</option>
                  <option value="Moto">Moto</option>
                </select>
              </div>
              <div class="space-y-1 mt-4">
                <label class="text-xs font-bold text-gray-400 uppercase">Zone d'intervention</label>
                <input v-model="form.zone_intervention" type="text" class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium">
              </div>
              <div class="mt-4 flex items-center gap-3">
                <input v-model="form.disponibilite" type="checkbox" id="dispo" class="w-5 h-5 accent-[#2D5A27] rounded">
                <label for="dispo" class="text-sm font-bold text-gray-900">Je suis disponible pour des missions</label>
              </div>
            </template>

            <!-- Consommateur / Admin -->
            <template v-if="['consommateur', 'admin_sectoriel'].includes(user.role) && !editMode">
              <div class="flex items-center justify-center h-full text-center">
                <p class="text-gray-400 font-medium italic text-sm">Les informations de base suffisent pour ce profil.</p>
              </div>
            </template>
            <template v-if="['consommateur', 'admin_sectoriel'].includes(user.role) && editMode">
              <div class="flex items-center justify-center h-full text-center">
                <p class="text-gray-400 font-medium italic text-sm">Aucune information supplémentaire à modifier.</p>
              </div>
            </template>
          </div>

          <!-- Boutons d'édition -->
          <div v-if="editMode" class="mt-8 pt-6 border-t border-gray-100 flex gap-4">
            <button @click="cancelEdit" class="flex-1 py-3 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition-all">Annuler</button>
            <button @click="saveProfile" :disabled="saving" class="flex-1 py-3 bg-[#2D5A27] text-white font-bold rounded-xl shadow-lg hover:shadow-[#2D5A27]/20 transition-all active:scale-95 disabled:opacity-50">
              {{ saving ? 'Sauvegarde...' : 'Sauvegarder' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Section 6 : Sécurité -->
      <div v-if="!editMode" class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 mt-8">
        <h2 class="text-xl font-black text-[#1A1A1A] font-serif mb-6 flex items-center gap-2">
          <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          Sécurité & Mot de passe
        </h2>

        <form @submit.prevent="updatePassword" class="max-w-2xl grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
          <div class="space-y-1">
            <label class="text-xs font-bold text-gray-400 uppercase">Ancien mot de passe</label>
            <input v-model="passwordForm.current_password" type="password" required class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium">
          </div>
          <div class="space-y-1">
            <label class="text-xs font-bold text-gray-400 uppercase">Nouveau mot de passe</label>
            <input v-model="passwordForm.new_password" type="password" required minlength="8" class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium">
          </div>
          <div class="space-y-1 md:col-span-2">
            <label class="text-xs font-bold text-gray-400 uppercase">Confirmer le nouveau mot de passe</label>
            <div class="flex gap-4">
              <input v-model="passwordForm.new_password_confirmation" type="password" required class="flex-1 px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium">
              <button type="submit" :disabled="savingPassword" class="px-6 py-3 bg-[#1A1A1A] text-white font-bold rounded-xl shadow-lg hover:shadow-black/20 transition-all active:scale-95 disabled:opacity-50 whitespace-nowrap">
                {{ savingPassword ? 'Mise à jour...' : 'Mettre à jour' }}
              </button>
            </div>
          </div>
        </form>
      </div>

    </template>

    <!-- Popup Modal -->
    <div v-if="popup.show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-in fade-in duration-300">
      <div class="bg-white rounded-[2rem] shadow-2xl p-8 max-w-sm w-full text-center animate-in zoom-in-95 duration-300">
        <div class="w-20 h-20 mx-auto rounded-full flex items-center justify-center mb-6" :class="popup.type === 'success' ? 'bg-green-100 text-[#2D5A27]' : 'bg-red-100 text-red-500'">
          <svg v-if="popup.type === 'success'" class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
          <svg v-else class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <h3 class="text-2xl font-black font-serif mb-2" :class="popup.type === 'success' ? 'text-gray-900' : 'text-red-500'">{{ popup.title }}</h3>
        <p class="text-gray-500 font-medium mb-8">{{ popup.message }}</p>
        <button @click="closePopup" class="w-full py-4 text-white rounded-2xl font-black shadow-lg transition-all active:scale-95" :class="popup.type === 'success' ? 'bg-[#2D5A27] hover:shadow-[#2D5A27]/20' : 'bg-red-500 hover:shadow-red-500/20'">
          Fermer
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'dashboard'
})

const user = ref(null)
const stats = ref({})
const loading = ref(true)
const editMode = ref(false)
const saving = ref(false)
const savingPassword = ref(false)

const form = reactive({
  prenom: '',
  nom: '',
  email: '',
  telephone: '',
  adresse: '',
  secteur_travail: '',
  type_distributeur: '',
  type_vehicule: '',
  zone_intervention: '',
  disponibilite: false,
})

const passwordForm = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const popup = reactive({ show: false, type: 'success', title: '', message: '' })

function showPopup(type, title, message) {
  popup.type = type
  popup.title = title
  popup.message = message
  popup.show = true
}

function closePopup() {
  popup.show = false
}

// Computed properties pour le design et données dynamiques
const initials = computed(() => {
  if (!user.value) return ''
  return `${user.value.prenom.charAt(0)}${user.value.nom.charAt(0)}`.toUpperCase()
})

const roleData = computed(() => {
  if (!user.value) return null
  return user.value[user.value.role] || null
})

const formattedStats = computed(() => {
  return stats.value || {}
})

function formatStatLabel(key) {
  const map = {
    produits_count: 'Produits en ligne',
    commandes_recues: 'Commandes reçues',
    ventes_mois: 'Ventes du mois',
    commandes_passees: 'Commandes passées',
    produits_vente: 'Produits en vente',
    paiements_effectues: 'Paiements',
    livraisons_recues: 'Livraisons',
    evaluations_donnees: 'Avis donnés',
    missions_effectuees: 'Missions terminées',
    missions_en_cours: 'En cours',
    remunerations_mois: 'Gains'
  }
  return map[key] || key.replace('_', ' ')
}

const roleColor = computed(() => {
  if (!user.value) return 'bg-gray-500'
  const map = {
    producteur: 'bg-[#2D5A27]',
    distributeur: 'bg-[#8B7340]',
    consommateur: 'bg-blue-500',
    transporteur: 'bg-purple-500',
    admin_sectoriel: 'bg-orange-500',
    super_administrateur: 'bg-red-900'
  }
  return map[user.value.role] || 'bg-gray-500'
})

const roleColorBg = computed(() => {
  if (!user.value) return 'bg-gray-500'
  const map = {
    producteur: 'bg-[#2D5A27]',
    distributeur: 'bg-[#8B7340]',
    consommateur: 'bg-blue-500',
    transporteur: 'bg-purple-500',
    admin_sectoriel: 'bg-orange-500',
    super_administrateur: 'bg-red-900'
  }
  return map[user.value.role] || 'bg-gray-500'
})

const statusColor = computed(() => {
  if (!user.value) return 'bg-gray-300'
  const map = {
    actif: 'bg-emerald-500',
    en_attente: 'bg-orange-400',
    suspendu: 'bg-red-500'
  }
  return map[user.value.statut] || 'bg-gray-300'
})

function formatRole(role) {
  if (!role) return ''
  return role.replace('_', ' ')
}

function formatDate(dateStr) {
  const date = new Date(dateStr)
  return new Intl.DateTimeFormat('fr-FR', { month: 'long', year: 'numeric' }).format(date)
}

// Actions
onMounted(async () => {
  await fetchProfile()
})

async function fetchProfile() {
  loading.value = true
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      navigateTo('/login')
      return
    }

    const response = await $fetch('http://127.0.0.1:8000/api/profil', {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    user.value = response.user
    stats.value = response.stats
    initForm()
  } catch (err) {
    console.error('Erreur de chargement du profil', err)
  } finally {
    loading.value = false
  }
}

function initForm() {
  if (!user.value) return
  form.prenom = user.value.prenom || ''
  form.nom = user.value.nom || ''
  form.email = user.value.email || ''
  form.telephone = user.value.telephone || ''
  form.adresse = user.value.adresse || ''
  
  if (roleData.value) {
    form.secteur_travail = roleData.value.secteur_travail || ''
    form.type_distributeur = roleData.value.type_distributeur || ''
    form.type_vehicule = roleData.value.type_vehicule || ''
    form.zone_intervention = roleData.value.zone_intervention || ''
    form.disponibilite = roleData.value.disponibilite ?? false
  }
}

function toggleEdit() {
  initForm()
  editMode.value = true
}

function cancelEdit() {
  editMode.value = false
}

async function saveProfile() {
  saving.value = true
  try {
    const token = localStorage.getItem('token')
    
    // Nettoyer les champs vides pour ne pas envoyer d'erreurs de validation
    const payload = {}
    Object.keys(form).forEach(k => {
      if (form[k] !== '' && form[k] !== null) {
        payload[k] = form[k]
      }
    })

    await $fetch('http://127.0.0.1:8000/api/profil', {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}` },
      body: payload
    })
    
    await fetchProfile()
    editMode.value = false
    showPopup('success', 'Mise à jour réussie', 'Votre profil a été mis à jour avec succès.')
  } catch (err) {
    console.error(err)
    showPopup('error', 'Erreur de sauvegarde', err.data?.message || 'Vérifiez vos informations et réessayez.')
  } finally {
    saving.value = false
  }
}

async function updatePassword() {
  if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
    showPopup('error', 'Erreur', 'Les mots de passe ne correspondent pas.')
    return
  }

  savingPassword.value = true
  try {
    const token = localStorage.getItem('token')
    await $fetch('http://127.0.0.1:8000/api/profil/password', {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}` },
      body: {
        current_password: passwordForm.current_password,
        new_password: passwordForm.new_password,
        new_password_confirmation: passwordForm.new_password_confirmation
      }
    })
    
    passwordForm.current_password = ''
    passwordForm.new_password = ''
    passwordForm.new_password_confirmation = ''
    
    showPopup('success', 'Sécurité à jour', 'Votre mot de passe a été modifié.')
  } catch (err) {
    console.error(err)
    showPopup('error', 'Action refusée', err.data?.message || 'Impossible de mettre à jour le mot de passe.')
  } finally {
    savingPassword.value = false
  }
}
</script>
