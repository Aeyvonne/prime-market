<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 font-serif">
          Gestion des <span class="text-[#2D5A27]">Catégories</span> 📂
        </h1>
        <p class="text-gray-500 font-medium mt-1">Gérez le catalogue des catégories et des sous-catégories.</p>
      </div>
      <div>
        <button 
          @click="openAddModal" 
          class="px-6 py-4 bg-[#2D5A27] text-white rounded-2xl font-black text-sm shadow-xl shadow-[#2D5A27]/20 hover:-translate-y-0.5 active:scale-95 transition-all flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
          Nouvelle catégorie
        </button>
      </div>
    </div>

    <!-- Category Grid Cards -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div v-for="i in 3" :key="i" class="bg-white rounded-[2rem] p-8 h-48 border border-gray-100 animate-pulse"></div>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div v-for="cat in categories" :key="cat.id" 
        class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-500 relative overflow-hidden group"
      >
        <div>
          <!-- Icon placeholder / Category initial -->
          <div class="w-12 h-12 rounded-2xl bg-[#F5F0E8] text-[#2D5A27] font-black text-xl flex items-center justify-center mb-6">
            {{ cat.nom?.[0]?.toUpperCase() }}
          </div>
          <h3 class="text-xl font-black text-gray-900 font-serif mb-2">{{ cat.nom }}</h3>
          <p class="text-sm text-gray-500 font-medium line-clamp-3 mb-6">{{ cat.description || 'Pas de description.' }}</p>

          <!-- Subcategories list tags -->
          <div class="space-y-2">
            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sous-Catégories</h4>
            <div class="flex flex-wrap gap-2">
              <span v-for="sc in cat.sous_categories" :key="sc.id" class="px-3 py-1 bg-[#F5F0E8]/50 text-[#8B7340] rounded-full text-xs font-bold">
                {{ sc.nom }}
              </span>
              <span v-if="!cat.sous_categories || cat.sous_categories.length === 0" class="text-xs text-gray-400 font-medium italic">
                Aucune sous-catégorie.
              </span>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 pt-6 border-t border-gray-50 flex items-center justify-end gap-4">
          <button @click="openEditModal(cat)" class="text-xs font-bold text-[#8B7340] hover:underline">Modifier</button>
          <button @click="confirmDelete(cat)" class="text-xs font-bold text-red-500 hover:underline">Supprimer</button>
        </div>
      </div>
      
      <div v-if="categories.length === 0" class="col-span-full bg-white rounded-[2.5rem] p-12 text-center border border-gray-100 text-gray-400 italic">
        Aucune catégorie enregistrée.
      </div>
    </div>

    <!-- Modal Ajouter / Modifier -->
    <div v-if="modal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-in fade-in duration-300">
      <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 max-w-md w-full animate-in zoom-in-95 duration-300">
        <h3 class="text-2xl font-black font-serif mb-6 text-gray-900">
          {{ modal.isEdit ? 'Modifier la Catégorie' : 'Ajouter une Catégorie' }}
        </h3>
        
        <form @submit.prevent="saveCategory" class="space-y-6">
          <div class="space-y-1">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Nom de la catégorie *</label>
            <input v-model="form.nom" required placeholder="Ex: Fruits et Légumes" class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm font-medium transition-all">
          </div>

          <div class="space-y-1">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Description</label>
            <textarea v-model="form.description" rows="3" placeholder="Description de la catégorie..." class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm font-medium transition-all resize-none"></textarea>
          </div>

          <!-- Sous-catégories -->
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Sous-catégories</label>
              <button type="button" @click="addSubCategoryField" class="text-xs font-bold text-[#2D5A27] hover:underline flex items-center gap-1">
                + Ajouter
              </button>
            </div>
            <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
              <div v-for="(sub, index) in form.sous_categories" :key="index" class="flex items-center gap-2">
                <input 
                  v-model="form.sous_categories[index]" 
                  required 
                  placeholder="Nom de la sous-catégorie" 
                  class="flex-grow px-4 py-2 bg-[#F5F0E8]/40 border border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-xs font-medium transition-all"
                >
                <button 
                  v-if="form.sous_categories.length > 1" 
                  type="button" 
                  @click="removeSubCategoryField(index)" 
                  class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-50">
            <button type="button" @click="modal.show = false" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">Annuler</button>
            <button type="submit" class="px-8 py-3 bg-[#2D5A27] text-white rounded-xl font-black text-sm shadow-lg hover:shadow-[#2D5A27]/25 transition-all">
              {{ modal.isEdit ? 'Modifier' : 'Ajouter' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Alert Modal delete popup -->
    <div v-if="alertPopup.show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-in fade-in duration-300">
      <div class="bg-white rounded-[2rem] shadow-2xl p-8 max-w-sm w-full text-center animate-in zoom-in-95 duration-300">
        <div class="w-20 h-20 mx-auto rounded-full bg-red-100 text-red-500 flex items-center justify-center mb-6">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-2xl font-black font-serif mb-2 text-gray-900">
          Supprimer ?
        </h3>
        <p class="text-gray-500 font-medium mb-8">
          Cette catégorie contient <strong>{{ alertPopup.category?.sous_categories?.length || 0 }}</strong> sous-catégories. Êtes-vous sûr de vouloir tout supprimer ?
        </p>
        <div class="flex items-center gap-4">
          <button @click="alertPopup.show = false" class="flex-1 py-4 bg-gray-100 text-gray-600 rounded-2xl font-black transition-all hover:bg-gray-200">Annuler</button>
          <button @click="deleteCategory" class="flex-1 py-4 bg-red-500 text-white rounded-2xl font-black transition-all hover:bg-red-600">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard'
})

import { ref, reactive, onMounted } from 'vue'

const categories = ref([])
const loading = ref(true)

const modal = reactive({ show: false, isEdit: false, categoryId: null })
const alertPopup = reactive({ show: false, category: null })

const form = reactive({
  nom: '',
  description: '',
  sous_categories: []
})

async function fetchCategories() {
  loading.value = true
  try {
    const data = await $fetch('http://127.0.0.1:8000/api/super-admin/categories', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    categories.value = data || []
  } catch (err) {
    console.error('Erreur chargement categories:', err)
  } finally {
    loading.value = false
  }
}

onMounted(fetchCategories)

function resetForm() {
  form.nom = ''
  form.description = ''
  form.sous_categories = ['']
}

function addSubCategoryField() {
  form.sous_categories.push('')
}

function removeSubCategoryField(index) {
  form.sous_categories.splice(index, 1)
}

function openAddModal() {
  resetForm()
  modal.isEdit = false
  modal.categoryId = null
  modal.show = true
}

function openEditModal(category) {
  modal.isEdit = true
  modal.categoryId = category.id
  form.nom = category.nom
  form.description = category.description || ''
  form.sous_categories = category.sous_categories && category.sous_categories.length > 0
    ? category.sous_categories.map(sc => sc.nom)
    : ['']
  modal.show = true
}

async function saveCategory() {
  const token = localStorage.getItem('token')
  try {
    if (modal.isEdit) {
      await $fetch(`http://127.0.0.1:8000/api/super-admin/categories/${modal.categoryId}`, {
        method: 'PUT',
        headers: { Authorization: `Bearer ${token}` },
        body: form
      })
    } else {
      await $fetch('http://127.0.0.1:8000/api/super-admin/categories', {
        method: 'POST',
        headers: { Authorization: `Bearer ${token}` },
        body: form
      })
    }
    modal.show = false
    await fetchCategories()
  } catch (err) {
    alert(err.data?.message || 'Erreur lors de l\'enregistrement.')
  }
}

function confirmDelete(category) {
  alertPopup.category = category
  alertPopup.show = true
}

async function deleteCategory() {
  if (!alertPopup.category) return
  const token = localStorage.getItem('token')
  try {
    await $fetch(`http://127.0.0.1:8000/api/super-admin/categories/${alertPopup.category.id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token}` }
    })
    alertPopup.show = false
    alertPopup.category = null
    await fetchCategories()
  } catch (err) {
    alert(err.data?.message || 'Erreur lors de la suppression.')
  }
}
</script>
