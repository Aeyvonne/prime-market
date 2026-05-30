<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-black text-gray-900 font-serif">Mes <span class="text-[#2D5A27]">Produits</span></h1>
        <p class="text-gray-500 font-medium mt-1">Gérez votre catalogue de produits locaux.</p>
      </div>
      
      <NuxtLink to="/producteur/produits/ajouter" class="px-8 py-4 bg-[#2D5A27] text-white rounded-2xl font-black text-sm shadow-xl shadow-[#2D5A27]/20 hover:-translate-y-1 transition-all active:scale-95 flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajouter un produit
      </NuxtLink>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
      <div v-if="pending" class="p-12 space-y-4">
        <div v-for="i in 5" :key="i" class="h-12 bg-gray-50 animate-pulse rounded-xl"></div>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Produit</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Catégorie</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Stock</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Prix Unitaire</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Statut</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <template v-if="products && products.length > 0">
              <tr v-for="product in products" :key="product.id" class="hover:bg-[#F5F0E8]/30 transition-colors group">
              <td class="px-8 py-6">
                <div class="flex items-center gap-4">
                  <!-- Photo Preview or Icon -->
                  <div class="w-12 h-12 rounded-xl bg-[#F5F0E8] overflow-hidden flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <img v-if="product.photo" :src="`http://127.0.0.1:8000/storage/products/${product.photo}`" class="w-full h-full object-cover" />
                    <span v-else>{{ getProductIcon(product.sous_categorie?.categorie?.nom) }}</span>
                  </div>
                  <div>
                    <p class="text-sm font-black text-gray-900 leading-none mb-1">{{ product.nom }}</p>
                    <p class="text-[10px] text-gray-400 font-bold truncate max-w-[200px]">{{ product.description || 'Pas de description' }}</p>
                  </div>
                </div>
              </td>
              <td class="px-8 py-6">
                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-[10px] font-black uppercase tracking-wider">
                  {{ product.sous_categorie?.nom }}
                </span>
              </td>
              <td class="px-8 py-6 text-center">
                <span :class="`text-sm font-black ${product.quantite < 10 ? 'text-red-500' : 'text-gray-700'}`">
                  {{ product.quantite }}
                </span>
              </td>
              <td class="px-8 py-6">
                <span class="text-sm font-black text-[#2D5A27]">{{ formatPrice(product.prix) }}</span>
              </td>
              <td class="px-8 py-6">
                <div class="flex items-center gap-2">
                  <div :class="`w-2 h-2 rounded-full ${product.status === 'Disponible' ? 'bg-emerald-500' : 'bg-red-500'}`"></div>
                  <span class="text-xs font-bold text-gray-700">{{ product.status }}</span>
                </div>
              </td>
              <td class="px-8 py-6 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button @click="editProduct(product.id)" class="p-2 text-gray-400 hover:text-[#2D5A27] hover:bg-emerald-50 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </button>
                  <button @click="confirmDelete(product.id)" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </td>
              </tr>
            </template>
            <tr v-if="!pending && (!products || products.length === 0)">
              <td colspan="6" class="px-8 py-20 text-center">
                <div class="text-5xl mb-4 opacity-10">📦</div>
                <p class="text-gray-400 font-medium italic text-sm">Vous n'avez aucun produit en vente pour le moment.</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Confirm Modal -->
    <div v-if="confirmDialog.show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-in fade-in duration-300">
      <div class="bg-white rounded-[2rem] shadow-2xl p-8 max-w-sm w-full text-center animate-in zoom-in-95 duration-300">
        <div class="w-20 h-20 mx-auto rounded-full bg-red-50 flex items-center justify-center text-red-500 mb-6">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-2xl font-black font-serif text-gray-900 mb-2">Attention</h3>
        <p class="text-gray-500 font-medium mb-8">Êtes-vous sûr de vouloir supprimer ce produit ? Cette action est irréversible.</p>
        <div class="flex gap-4">
          <button @click="closeConfirm" class="flex-1 py-3 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition-all">Annuler</button>
          <button @click="executeDelete" class="flex-1 py-3 bg-red-500 text-white font-bold rounded-xl shadow-lg hover:shadow-red-500/20 transition-all active:scale-95">Supprimer</button>
        </div>
      </div>
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
          Fermer
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard'
})

import { ref, onMounted, reactive } from 'vue'

const products = ref([])
const pending = ref(true)

const popup = reactive({ show: false, type: 'success', title: '', message: '' })
const confirmDialog = reactive({ show: false, productId: null })

function showPopup(type, title, message) {
  popup.type = type
  popup.title = title
  popup.message = message
  popup.show = true
}

function closePopup() {
  popup.show = false
}

function closeConfirm() {
  confirmDialog.show = false
  confirmDialog.productId = null
}

onMounted(() => {
  fetchProducts()
})

async function fetchProducts() {
  pending.value = true
  try {
    const token = localStorage.getItem('token')
    const response = await $fetch('http://127.0.0.1:8000/api/producteur/produits', {
      headers: {
        Authorization: token ? `Bearer ${token}` : ''
      }
    })
    products.value = response || []
  } catch (err) {
    console.error('Erreur chargement:', err)
    products.value = []
  } finally {
    pending.value = false
  }
}

function editProduct(id) {
  navigateTo(`/producteur/produits/modifier/${id}`)
}

function confirmDelete(id) {
  confirmDialog.productId = id
  confirmDialog.show = true
}

async function executeDelete() {
  const id = confirmDialog.productId
  closeConfirm()

  try {
    const token = localStorage.getItem('token')
    
    await $fetch(`http://127.0.0.1:8000/api/producteur/produits/${id}`, {
      method: 'DELETE',
      headers: {
        Authorization: token ? `Bearer ${token}` : ''
      }
    })
    showPopup('success', 'Supprimé !', 'Le produit a été supprimé avec succès.')
    await fetchProducts()
  } catch (err) {
    console.error('Erreur suppression:', err)
    showPopup('error', 'Erreur', 'Impossible de supprimer le produit.')
  }
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