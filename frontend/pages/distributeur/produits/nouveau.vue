<template>
  <div class="max-w-4xl mx-auto animate-in slide-in-from-bottom duration-700">
    <!-- Header -->
    <div class="mb-8">
      <NuxtLink to="/distributeur/produits" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#2D5A27] transition-colors group mb-4">
        <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Retour à la liste
      </NuxtLink>
      <h1 class="text-3xl font-black text-gray-900 font-serif">Mettre en <span class="text-[#2D5A27]">Vente</span></h1>
      <p class="text-gray-500 font-medium">Ajoutez un nouveau produit à votre catalogue de revente.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 p-8 md:p-12">
      <form @submit.prevent="handleSubmit" class="space-y-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Nom du Produit -->
          <div class="space-y-2">
            <label class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Nom du Produit</label>
            <input v-model="form.nom" required placeholder="Ex: Panier de Tomates Bio" class="w-full px-6 py-4 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-medium">
          </div>

          <!-- Catégorie -->
          <div class="space-y-2">
            <label class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Catégorie</label>
            <select v-model="selectedCategory" class="w-full px-6 py-4 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-bold text-gray-700">
              <option value="">Sélectionner une catégorie</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.nom }}</option>
            </select>
          </div>

          <!-- Sous-Catégorie -->
          <div class="space-y-2">
            <label class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Sous-Catégorie</label>
            <select v-model="form.sous_categorie_id" :disabled="!selectedCategory" class="w-full px-6 py-4 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-bold text-gray-700 disabled:opacity-50">
              <option value="">Sélectionner une sous-catégorie</option>
              <option v-for="sc in availableSousCategories" :key="sc.id" :value="sc.id">{{ sc.nom }}</option>
            </select>
          </div>

          <!-- Prix -->
          <div class="space-y-2">
            <label class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Prix Unitaire (FCFA)</label>
            <div class="relative">
              <input v-model="form.prix" type="number" required placeholder="0" class="w-full px-6 py-4 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-bold">
              <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400">FCFA</span>
            </div>
          </div>

          <!-- Quantité -->
          <div class="space-y-2">
            <label class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Quantité en Stock</label>
            <input v-model="form.quantite" type="number" required placeholder="0" class="w-full px-6 py-4 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-bold">
          </div>

          <!-- Photo Upload -->
          <div class="space-y-2 md:col-span-2">
            <label class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Photo du Produit</label>
            <div class="flex flex-col md:flex-row gap-6 items-start">
              <div 
                class="w-full md:w-48 h-48 bg-[#F5F0E8]/40 border-2 border-dashed border-gray-200 rounded-[2rem] flex items-center justify-center overflow-hidden group hover:border-[#2D5A27] transition-all cursor-pointer relative"
                @click="$refs.fileInput.click()"
              >
                <img v-if="previewUrl" :src="previewUrl" class="w-full h-full object-cover">
                <div v-else class="flex flex-col items-center text-gray-400 group-hover:text-[#2D5A27]">
                  <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  <span class="text-[10px] font-black uppercase">Choisir une photo</span>
                </div>
                <input type="file" ref="fileInput" class="hidden" accept="image/png, image/jpeg, image/jpg, image/webp" @change="handleFileChange">
              </div>
              <div class="flex-grow pt-2">
                <p class="text-sm font-medium text-gray-500 mb-4">Formats acceptés : JPG, PNG, WEBP. Taille max : 2MB.</p>
                <button type="button" @click="$refs.fileInput.click()" class="px-6 py-3 bg-[#F5F0E8] text-[#2D5A27] rounded-xl font-bold text-xs hover:bg-[#2D5A27] hover:text-white transition-all">
                  {{ previewUrl ? 'Changer la photo' : 'Sélectionner un fichier' }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div class="space-y-2">
          <label class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Description détaillée</label>
          <textarea v-model="form.description" rows="4" placeholder="Décrivez les caractéristiques de votre produit..." class="w-full px-6 py-4 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-medium resize-none"></textarea>
        </div>

        <!-- Submit -->
        <div class="pt-6 border-t border-gray-50 flex items-center justify-between gap-4">
          <p class="text-xs text-gray-400 font-medium max-w-sm">
            Assurez-vous que les informations sont exactes avant de valider la mise en vente.
          </p>
          <div class="flex items-center gap-4">
            <NuxtLink to="/distributeur/produits" class="px-8 py-4 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">Annuler</NuxtLink>
            <button 
              type="submit" 
              :disabled="loading"
              class="px-10 py-4 bg-[#2D5A27] text-white rounded-2xl font-black text-sm shadow-xl shadow-[#2D5A27]/20 hover:-translate-y-1 transition-all active:scale-95 disabled:opacity-50 disabled:grayscale flex items-center gap-3"
            >
              <span>{{ loading ? 'Publication...' : 'Publier le produit' }}</span>
              <svg v-if="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
          </div>
        </div>
      </form>
    </div>
    
    <!-- Popup Modal -->
    <div v-if="popup.show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-in fade-in duration-300">
      <div class="bg-white rounded-[2rem] shadow-2xl p-8 max-w-sm w-full text-center animate-in zoom-in-95 duration-300">
        <div 
          class="w-20 h-20 mx-auto rounded-full flex items-center justify-center mb-6"
          :class="popup.type === 'success' ? 'bg-green-100 text-[#2D5A27]' : 'bg-red-100 text-red-500'"
        >
          <svg v-if="popup.type === 'success'" class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
          <svg v-else class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <h3 class="text-2xl font-black font-serif mb-2" :class="popup.type === 'success' ? 'text-gray-900' : 'text-red-500'">
          {{ popup.title }}
        </h3>
        <p class="text-gray-500 font-medium mb-8">{{ popup.message }}</p>
        <button 
          @click="closePopup" 
          class="w-full py-4 text-white rounded-2xl font-black shadow-lg transition-all active:scale-95"
          :class="popup.type === 'success' ? 'bg-[#2D5A27] hover:shadow-[#2D5A27]/20' : 'bg-red-500 hover:shadow-red-500/20'"
        >
          {{ popup.type === 'success' ? 'Continuer' : 'Fermer' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard'
})

const loading = ref(false)
const selectedCategory = ref('')
const previewUrl = ref(null)
const selectedFile = ref(null)
const popup = reactive({ show: false, type: 'success', title: '', message: '', redirect: null })

function showPopup(type, title, message, redirect = null) {
  popup.type = type
  popup.title = title
  popup.message = message
  popup.redirect = redirect
  popup.show = true
}

function closePopup() {
  popup.show = false
  if (popup.redirect) {
    navigateTo(popup.redirect)
  }
}

const form = reactive({
  nom: '',
  description: '',
  prix: '',
  quantite: '',
  sous_categorie_id: '',
})

const { data: categories } = await useFetch('http://127.0.0.1:8000/api/categories')

const availableSousCategories = computed(() => {
  if (!selectedCategory.value || !categories.value) return []
  const cat = categories.value.find(c => c.id === parseInt(selectedCategory.value))
  return cat ? cat.sous_categories : []
})

function handleFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      showPopup('error', 'Fichier trop lourd', 'La photo ne doit pas dépasser 2MB.')
      return
    }
    selectedFile.value = file
    previewUrl.value = URL.createObjectURL(file)
  }
}

async function handleSubmit() {
  if (!selectedFile.value) {
    showPopup('error', 'Photo requise', 'Veuillez sélectionner une photo pour le produit.')
    return
  }

  loading.value = true
  try {
    const formData = new FormData()
    formData.append('nom', form.nom)
    formData.append('description', form.description || '')
    formData.append('prix', form.prix)
    formData.append('quantite', form.quantite)
    formData.append('sous_categorie_id', form.sous_categorie_id)
    formData.append('photo', selectedFile.value)

    await $fetch('http://127.0.0.1:8000/api/distributeur/produits', {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      },
      body: formData
    })
    
    showPopup('success', 'Félicitations !', 'Produit mis en vente avec succès.', '/distributeur/produits')
  } catch (err) {
    console.error('Erreur ajout produit:', err)
    showPopup('error', 'Erreur de publication', err.data?.message || 'Une erreur est survenue lors de l\'ajout du produit.')
  } finally {
    loading.value = false
  }
}
</script>
