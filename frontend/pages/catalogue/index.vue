<template>
  <div class="min-h-screen bg-[#F5F0E8] font-sans pb-20">
    <!-- 1. Hero Section -->
    <div class="bg-[#2D5A27] pt-32 pb-24 px-4 relative overflow-hidden">
      <!-- Decorative background elements -->
      <div class="absolute inset-0 opacity-10 pointer-events-none">
        <svg class="absolute -top-24 -left-24 w-96 h-96 text-white" fill="currentColor" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"><path d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,81.3,-46.3C90.8,-33.5,96.8,-18,97.3,-2.3C97.8,13.4,92.8,29.3,83.1,42.5C73.4,55.7,59.1,66.2,43.5,73.4C27.9,80.6,11,84.5,-5.2,85.6C-21.4,86.7,-36.9,85.1,-50.2,77.7C-63.5,70.3,-74.6,57.1,-81.4,41.9C-88.2,26.7,-90.7,9.5,-87.3,-6.2C-83.9,-21.9,-74.6,-36.1,-62.4,-46.8C-50.2,-57.5,-35.1,-64.7,-20.9,-71.4C-6.7,-78.1,6.6,-84.3,21.3,-84.3C36,-84.3,50.7,-78.1,44.7,-76.4Z" transform="translate(100 100)" /></svg>
      </div>
      
      <div class="max-w-4xl mx-auto text-center relative z-10">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white font-serif mb-6 drop-shadow-sm">Catalogue des Produits</h1>
        <p class="text-[#D4E8CC] text-lg md:text-xl font-medium mb-12 max-w-2xl mx-auto">Découvrez tous les produits du secteur primaire sénégalais et trouvez les meilleurs ingrédients frais et locaux.</p>
        
        <!-- Barre de recherche -->
        <div class="relative max-w-2xl mx-auto group">
          <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none">
            <svg class="w-6 h-6 text-gray-400 group-focus-within:text-[#2D5A27] transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </div>
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Rechercher une tomate, du poisson, du maïs..." 
            class="w-full pl-16 pr-6 py-4 md:py-5 bg-white border-none rounded-full shadow-2xl text-gray-800 text-lg focus:ring-4 focus:ring-[#8B7340]/30 outline-none transition-all placeholder-gray-400"
          >
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-20">
      
      <!-- 2. Filtres par catégorie -->
      <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 mb-12 flex flex-wrap items-center justify-center gap-4 border border-gray-100">
        <button 
          @click="selectedCategory = ''"
          class="flex items-center gap-2 px-6 py-3 rounded-full font-bold text-sm transition-all duration-300"
          :class="selectedCategory === '' ? 'bg-[#2D5A27] text-white shadow-md' : 'bg-white border-2 border-[#2D5A27] text-[#2D5A27] hover:bg-[#2D5A27]/5'"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
          Tous les produits
        </button>
        
        <button 
          v-for="cat in categories" 
          :key="cat.id"
          @click="selectedCategory = cat.id"
          class="flex items-center gap-2 px-6 py-3 rounded-full font-bold text-sm transition-all duration-300"
          :class="selectedCategory === cat.id ? 'bg-[#2D5A27] text-white shadow-md' : 'bg-white border-2 border-[#2D5A27] text-[#2D5A27] hover:bg-[#2D5A27]/5'"
        >
          <span class="text-base leading-none">{{ getProductIcon(cat.nom) }}</span>
          {{ cat.nom }}
        </button>
      </div>

      <!-- 5. Compteur de résultats -->
      <div class="mb-8 flex items-center justify-between">
        <h2 class="text-2xl font-black text-[#1A1A1A] font-serif">Résultats</h2>
        <span class="px-4 py-1.5 bg-[#8B7340]/10 text-[#8B7340] rounded-full text-sm font-black tracking-widest">
          {{ pending ? '...' : filteredProducts.length }} produit{{ filteredProducts.length > 1 ? 's' : '' }} disponible{{ filteredProducts.length > 1 ? 's' : '' }}
        </span>
      </div>

      <!-- 3. Grille de Produits -->
      <div v-if="pending" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div v-for="i in 8" :key="i" class="h-[420px] bg-white animate-pulse rounded-2xl shadow-sm border border-gray-100"></div>
      </div>

      <div v-else-if="filteredProducts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div v-for="product in filteredProducts" :key="product.id" 
          class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 flex flex-col border border-gray-100"
        >
          <!-- Image Section -->
          <div class="relative h-[220px] overflow-hidden bg-[#F5F0E8] w-full">
            <img 
              v-if="product.photo" 
              :src="`http://127.0.0.1:8000/storage/products/${product.photo}`" 
              :alt="product.nom" 
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
            >
            <div v-else class="w-full h-full flex items-center justify-center text-6xl group-hover:scale-110 transition-transform duration-500 opacity-20">
              {{ getProductIcon(product.sous_categorie?.categorie?.nom) }}
            </div>
            
            <!-- Badge Catégorie -->
            <div class="absolute top-4 left-4 px-3 py-1.5 bg-white/95 backdrop-blur-md rounded-lg text-[10px] font-black text-[#2D5A27] uppercase tracking-widest shadow-sm">
              {{ product.sous_categorie?.categorie?.nom || 'Général' }}
            </div>
          </div>

          <!-- Content Section -->
          <div class="p-6 flex flex-col flex-grow">
            <!-- Vendeur info -->
            <div class="flex items-center gap-2 mb-3">
              <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest line-clamp-1">
                {{ product.producteur?.user?.nom || product.distributeur?.user?.nom || 'Vendeur Inconnu' }}
              </span>
            </div>

            <!-- Titre et Description -->
            <h4 class="text-xl font-black text-[#1A1A1A] font-serif mb-2 line-clamp-1 group-hover:text-[#2D5A27] transition-colors">{{ product.nom }}</h4>
            <p class="text-gray-500 text-xs font-medium mb-6 line-clamp-2 leading-relaxed">
              {{ product.description || 'Produit local de haute qualité, fraîchement récolté.' }}
            </p>
            
            <div class="mt-auto flex items-end justify-between">
              <!-- Prix -->
              <div class="flex flex-col">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Prix</span>
                <p class="text-2xl font-black text-[#2D5A27]">{{ formatPrice(product.prix) }}</p>
              </div>
            </div>

            <!-- CTA -->
            <div class="mt-6 grid grid-cols-2 gap-2">
              <NuxtLink :to="`/catalogue/${product.id}`"
                class="py-3.5 rounded-xl border-2 border-[#2D5A27] text-[#2D5A27] font-bold text-xs transition-all duration-300 flex items-center justify-center gap-1 hover:bg-[#2D5A27] hover:text-white">
                Détails
              </NuxtLink>
              <template v-if="userRole === 'distributeur' || userRole === 'consommateur'">
                <button @click="ajouterPanier(product)"
                  class="py-3.5 rounded-xl bg-[#2D5A27] text-white font-bold text-xs transition-all duration-300 flex items-center justify-center gap-1 hover:bg-[#1e3d1a] shadow-lg shadow-[#2D5A27]/20">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                  Ajouter
                </button>
              </template>
            </div>
          </div>
        </div>
      </div>

      <!-- 4. État vide -->
      <div v-if="!pending && filteredProducts.length === 0" class="py-24 px-4 text-center bg-white rounded-[2rem] shadow-sm border border-gray-100 mt-8">
        <svg class="w-24 h-24 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
        </svg>
        <h3 class="text-2xl font-black text-gray-900 font-serif mb-2">Aucun produit trouvé</h3>
        <p class="text-gray-500 font-medium">Il semble qu'aucun produit ne corresponde à votre recherche.</p>
        <button @click="searchQuery = ''; selectedCategory = ''" class="mt-8 px-8 py-3 bg-[#2D5A27] text-white rounded-full font-bold shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all">
          Réinitialiser les filtres
        </button>
      </div>

    </div>

    <!-- Cart icon -->
    <div v-if="userRole === 'distributeur' || userRole === 'consommateur'"
      class="fixed top-6 right-6 z-50">
      <NuxtLink to="/consommateur/panier"
        class="relative flex items-center gap-2 bg-white rounded-2xl shadow-lg px-4 py-3 hover:shadow-xl transition-all group">
        <svg class="w-5 h-5 text-[#2D5A27] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
        <span class="text-xs font-bold text-[#2D5A27]">Panier</span>
        <span v-if="cartStore.count > 0"
          class="absolute -top-2 -right-2 min-w-[20px] h-5 bg-red-500 text-white rounded-full text-[10px] font-black flex items-center justify-center px-1 shadow-md">
          {{ cartStore.count }}
        </span>
      </NuxtLink>
    </div>

    <!-- Toast notification -->
    <div v-if="toastVisible"
      class="fixed bottom-8 right-8 z-50 bg-[#2D5A27] text-white px-6 py-3 rounded-2xl shadow-2xl text-sm font-bold animate-in slide-in-from-bottom-2 duration-300">
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'default'
})

const cartStore = useCartStore()

const searchQuery = ref('')
const selectedCategory = ref('')
const products = ref([])
const pending = ref(true)
const userRole = ref(null)

const { data: categories } = await useFetch('http://127.0.0.1:8000/api/categories', {
  server: false
})

onMounted(async () => {
  const userStr = localStorage.getItem('user')
  if (userStr) {
    const user = JSON.parse(userStr)
    userRole.value = user.role
  }

  pending.value = true
  try {
    const response = await $fetch('http://127.0.0.1:8000/api/produits')
    products.value = response || []
  } catch (err) {
    console.error('Erreur chargement catalogue:', err)
    products.value = []
  } finally {
    pending.value = false
  }
})

const filteredProducts = computed(() => {
  if (!products.value) return []
  return products.value.filter(p => {
    const matchesSearch = p.nom.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesCategory = !selectedCategory.value || p.sous_categorie?.categorie_id === parseInt(selectedCategory.value)
    return matchesSearch && matchesCategory
  })
})

function ajouterPanier(produit) {
  const store = useCartStore()
  store.ajouter({
    id: produit.id,
    nom: produit.nom,
    prix: produit.prix,
    photo: produit.photo || null,
    quantite_stock: produit.quantite || 999,
    vendeur_id: produit.producteur_id ?? (produit.proprietaire_type === 'distributeur' ? produit.proprietaire_id : null),
    vendeur_nom: produit.producteur?.user?.nom || produit.distributeur?.user?.nom || '',
  })
  showToast('Produit ajouté au panier')
}

const toastMessage = ref('')
const toastVisible = ref(false)
let toastTimer = null

function showToast(msg) {
  toastMessage.value = msg
  toastVisible.value = true
  if (toastTimer) clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toastVisible.value = false }, 2000)
}

function formatPrice(price) {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}

function getProductIcon(category) {
  const icons = {
    'Agriculture': '🥦',
    'Élevage': '🐄',
    'Pêche': '🐟',
    'Transformés': '🍯'
  }
  return icons[category] || '📦'
}
</script>
