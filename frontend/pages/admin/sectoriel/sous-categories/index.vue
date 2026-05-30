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
            Gestion des Sous-Catégories
          </h1>
          <p class="text-white/80 text-sm md:text-base font-medium">
            Créez, modifiez ou supprimez les sous-catégories associées à votre secteur d'activité.
          </p>
        </div>
        <button 
          @click="openCreateModal"
          class="bg-[#8B7340] hover:bg-[#8B7340]/90 text-white font-bold px-6 py-4 rounded-2xl text-xs flex items-center justify-center gap-2 self-start md:self-auto shadow-lg shadow-[#8B7340]/20 transition-all hover:scale-105 active:scale-95 duration-300"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
          Ajouter une sous-catégorie
        </button>
      </div>
    </div>

    <!-- Search & Total Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center">
        <div class="relative w-full group">
          <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-gray-400 group-focus-within:text-[#2D5A27] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Rechercher une sous-catégorie..." 
            class="w-full pl-12 pr-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-[#2D5A27] outline-none transition-all text-sm font-medium"
          >
        </div>
      </div>

      <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center justify-between">
        <div class="space-y-1">
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Total Sous-Catégories</p>
          <h3 class="text-2xl font-black text-gray-900">{{ subcategories.length }}</h3>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-[#F5F0E8] text-[#8B7340] flex items-center justify-center font-bold">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
      </div>
    </div>

    <!-- Table List (Liste des sous-catégories avec leur catégorie parente) -->
    <div class="bg-white rounded-[2.5rem] border border-gray-100 overflow-hidden shadow-sm">
      <div v-if="loading" class="p-12 space-y-4">
        <!-- Skeleton Loading -->
        <div class="animate-pulse flex items-center space-x-4 p-4 border-b border-gray-50" v-for="i in 3" :key="i">
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
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nom de la Sous-Catégorie</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Description</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Catégorie Parente</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Produits Liés</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="sub in filteredSubcategories" :key="sub.id" class="hover:bg-[#F5F0E8]/20 transition-all duration-300">
              <!-- Nom -->
              <td class="px-8 py-5">
                <span class="text-sm font-black text-gray-900">{{ sub.nom }}</span>
              </td>
              <!-- Description -->
              <td class="px-8 py-5">
                <p class="text-xs text-gray-500 max-w-xs truncate" :title="sub.description">
                  {{ sub.description || 'Aucune description' }}
                </p>
              </td>
              <!-- Catégorie parente -->
              <td class="px-8 py-5">
                <span class="text-xs font-bold text-gray-700 bg-gray-100 px-3 py-1.5 rounded-full border border-gray-205">
                  {{ sub.categorie?.nom || currentSector }}
                </span>
              </td>
              <!-- Produits liés -->
              <td class="px-8 py-5 text-center">
                <span 
                  class="inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider"
                  :class="[
                    sub.produits_count > 0 
                      ? 'bg-[#2D5A27]/10 text-[#2D5A27]' 
                      : 'bg-gray-100 text-gray-400'
                  ]"
                >
                  {{ sub.produits_count || 0 }}
                </span>
              </td>
              <!-- Actions (Modifier et Supprimer) -->
              <td class="px-8 py-5 text-right">
                <div class="flex items-center justify-end gap-2">
                  <!-- Modifier -->
                  <button 
                    @click="openEditModal(sub)"
                    class="p-2.5 rounded-xl bg-[#F5F0E8] text-[#8B7340] hover:bg-[#8B7340]/10 transition-colors"
                    title="Modifier"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </button>
                  
                  <!-- Supprimer -->
                  <button 
                    @click="confirmDelete(sub)"
                    class="p-2.5 rounded-xl transition-all duration-300"
                    :class="[
                      sub.produits_count > 0 
                        ? 'bg-gray-50 text-gray-300 cursor-not-allowed' 
                        : 'bg-red-50 text-red-600 hover:bg-red-100'
                    ]"
                    :disabled="sub.produits_count > 0"
                    :title="sub.produits_count > 0 ? 'Impossible de supprimer : contient des produits' : 'Supprimer'"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredSubcategories.length === 0">
              <td colspan="5" class="px-8 py-16 text-center">
                <div class="max-w-md mx-auto space-y-3">
                  <div class="w-16 h-16 bg-[#F5F0E8] rounded-full flex items-center justify-center mx-auto text-[#8B7340]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                  </div>
                  <h3 class="text-base font-black text-gray-800 font-serif">Aucune sous-catégorie</h3>
                  <p class="text-xs text-gray-400 font-medium leading-relaxed">
                    Aucune sous-catégorie correspondante dans votre secteur {{ currentSector }}.
                  </p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create or Edit Modal (avec liste des catégories disponibles) -->
    <div v-if="formModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-[2.5rem] p-8 max-w-lg w-full border border-gray-100 shadow-2xl animate-in zoom-in duration-300 space-y-6">
        <div>
          <h3 class="text-2xl font-black text-gray-900 font-serif">
            {{ isEditMode ? 'Modifier la Sous-Catégorie' : 'Nouvelle Sous-Catégorie' }}
          </h3>
          <p class="text-xs text-gray-400 font-medium mt-1">
            Configurez les détails du catalogue pour le secteur {{ currentSector }}.
          </p>
        </div>

        <form @submit.prevent="saveSubcategory" class="space-y-5">
          <!-- Nom -->
          <div class="space-y-1.5">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nom de la sous-catégorie *</label>
            <input 
              v-model="form.nom"
              type="text" 
              placeholder="Ex: Fruits de saison, Légumes frais..."
              required
              class="w-full px-5 py-3.5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-[#2D5A27] outline-none transition-all text-sm font-medium"
            >
          </div>

          <!-- Description -->
          <div class="space-y-1.5">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Description</label>
            <textarea 
              v-model="form.description"
              rows="3" 
              placeholder="Description ou notes additionnelles..."
              class="w-full px-5 py-3.5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-[#2D5A27] outline-none transition-all text-sm font-medium resize-none"
            ></textarea>
          </div>

          <!-- Catégorie Parente (Liste des catégories disponibles) -->
          <div class="space-y-1.5">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Catégorie Parente</label>
            <div class="relative">
              <select 
                v-model="form.categorie_id"
                disabled
                class="w-full px-5 py-3.5 bg-gray-100 border-2 border-transparent rounded-2xl outline-none text-sm font-bold text-gray-600 appearance-none"
              >
                <option v-for="cat in categoriesList" :key="cat.id" :value="cat.id">
                  {{ cat.nom }} (Secteur actif)
                </option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </div>
            </div>
            <p class="text-[10px] text-gray-400 font-medium">
              Note: La catégorie est automatiquement verrouillée sur le secteur de votre compte par sécurité.
            </p>
          </div>

          <div class="flex gap-4 pt-2">
            <button 
              type="button"
              @click="formModalOpen = false" 
              class="flex-1 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl text-xs transition-colors"
            >
              Annuler
            </button>
            <button 
              type="submit" 
              class="flex-1 py-3.5 bg-[#2D5A27] hover:bg-[#1E3F1A] text-white font-bold rounded-2xl text-xs transition-all shadow-lg shadow-[#2D5A27]/10"
              :disabled="saving"
            >
              {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div v-if="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-[2.5rem] p-8 max-w-md w-full border border-gray-100 shadow-2xl animate-in zoom-in duration-300 space-y-6">
        <div class="text-center space-y-3">
          <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          </div>
          <h3 class="text-xl font-black text-gray-900 font-serif">Supprimer la sous-catégorie ?</h3>
          <p class="text-xs text-gray-500 leading-relaxed">
            Êtes-vous sûr de vouloir supprimer définitivement la sous-catégorie 
            <span class="font-bold text-gray-900">{{ selectedSubForDelete?.nom }}</span> ? 
            Cette opération est irréversible.
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
            @click="deleteSubcategory" 
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
const subcategories = ref([])
const categoriesList = ref([])
const loading = ref(true)
const saving = ref(false)
const searchQuery = ref('')
const toasts = ref([])

// Form Modal States
const formModalOpen = ref(false)
const isEditMode = ref(false)
const form = ref({
  id: null,
  nom: '',
  description: '',
  categorie_id: null
})

// Delete Modal States
const deleteModalOpen = ref(false)
const selectedSubForDelete = ref(null)

function addToast(message, type = 'success') {
  const id = Date.now()
  toasts.value.push({ id, message, type })
  setTimeout(() => removeToast(id), 5000)
}

function removeToast(id) {
  toasts.value = toasts.value.filter(t => t.id !== id)
}

// Charger les catégories parentes disponibles
async function loadCategories() {
  try {
    const config = useRuntimeConfig()
    const apiUrl = config.public.apiUrl || 'http://127.0.0.1:8000/api'
    const res = await $fetch(`${apiUrl}/categories`)
    categoriesList.value = res || []
  } catch (err) {
    console.error('Erreur chargement categories:', err)
  }
}

// Charger les sous-catégories
async function loadSubcategories() {
  loading.value = true
  const token = localStorage.getItem('token')
  try {
    const config = useRuntimeConfig()
    const apiUrl = config.public.apiUrl || 'http://127.0.0.1:8000/api'

    const res = await $fetch(`${apiUrl}/admin-sectoriel/sous-categories`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    subcategories.value = res || []
  } catch (err) {
    console.error('Erreur chargement sous-catégories:', err)
    addToast('Impossible de récupérer les sous-catégories.', 'error')
  } finally {
    loading.value = false
  }
}

// Ouvrir modal création
function openCreateModal() {
  isEditMode.value = false
  // Trouver la catégorie parente correspondant au secteur actuel
  const parentCat = categoriesList.value.find(c => c.nom.toLowerCase() === currentSector.value.toLowerCase())
  form.value = { 
    id: null, 
    nom: '', 
    description: '',
    categorie_id: parentCat ? parentCat.id : null
  }
  formModalOpen.value = true
}

// Ouvrir modal édition
function openEditModal(sub) {
  isEditMode.value = true
  form.value = { 
    id: sub.id,
    nom: sub.nom,
    description: sub.description,
    categorie_id: sub.categorie_id
  }
  formModalOpen.value = true
}

// Enregistrer
async function saveSubcategory() {
  saving.value = true
  const token = localStorage.getItem('token')
  
  try {
    const config = useRuntimeConfig()
    const apiUrl = config.public.apiUrl || 'http://127.0.0.1:8000/api'
    
    let res
    if (isEditMode.value) {
      res = await $fetch(`${apiUrl}/admin-sectoriel/sous-categories/${form.value.id}`, {
        method: 'PUT',
        headers: { Authorization: `Bearer ${token}` },
        body: {
          nom: form.value.nom,
          description: form.value.description
        }
      })
      addToast(res.message || 'Sous-catégorie mise à jour.', 'success')
      const idx = subcategories.value.findIndex(s => s.id === form.value.id)
      if (idx !== -1 && res.sous_categorie) {
        subcategories.value[idx] = res.sous_categorie
      } else {
        await loadSubcategories()
      }
    } else {
      res = await $fetch(`${apiUrl}/admin-sectoriel/sous-categories`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${token}` },
        body: {
          nom: form.value.nom,
          description: form.value.description
        }
      })
      addToast(res.message || 'Sous-catégorie créée.', 'success')
      if (res.sous_categorie) {
        subcategories.value.unshift(res.sous_categorie)
      } else {
        await loadSubcategories()
      }
    }
    
    formModalOpen.value = false
  } catch (err) {
    console.error('Erreur enregistrement sous-catégorie:', err)
    const errorMsg = err.data?.message || 'Erreur lors de l\'enregistrement.'
    addToast(errorMsg, 'error')
  } finally {
    saving.value = false
  }
}

// Ouvrir modal suppression
function confirmDelete(sub) {
  if (sub.produits_count > 0) {
    addToast('Impossible de supprimer une sous-catégorie contenant des produits.', 'error')
    return
  }
  selectedSubForDelete.value = sub
  deleteModalOpen.value = true
}

// Confirmer la suppression
async function deleteSubcategory() {
  if (!selectedSubForDelete.value) return
  const id = selectedSubForDelete.value.id
  const token = localStorage.getItem('token')
  
  try {
    const config = useRuntimeConfig()
    const apiUrl = config.public.apiUrl || 'http://127.0.0.1:8000/api'

    const res = await $fetch(`${apiUrl}/admin-sectoriel/sous-categories/${id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token}` }
    })

    addToast(res.message || 'Sous-catégorie supprimée avec succès.', 'success')
    subcategories.value = subcategories.value.filter(s => s.id !== id)
    deleteModalOpen.value = false
    selectedSubForDelete.value = null
  } catch (err) {
    console.error('Erreur suppression sous-catégorie:', err)
    const errorMsg = err.data?.message || 'Erreur lors de la suppression.'
    addToast(errorMsg, 'error')
  }
}

const filteredSubcategories = computed(() => {
  let result = subcategories.value

  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    result = result.filter(s => s.nom && s.nom.toLowerCase().includes(query))
  }

  return result
})

onMounted(async () => {
  const userStr = localStorage.getItem('user')
  if (userStr) {
    const user = JSON.parse(userStr)
    if (user.admin_sectorielle) {
      currentSector.value = user.admin_sectorielle.secteur || 'Agriculture'
    }
  }
  await loadCategories()
  await loadSubcategories()
})
</script>
