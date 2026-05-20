<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- En-tête de bienvenue -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 font-serif">
          Bonjour, <span class="text-[#2D5A27]">{{ userName }}</span> 👋
        </h1>
        <p class="text-gray-500 font-medium mt-1">Voici l'état de votre activité de production aujourd'hui.</p>
      </div>
      <div class="flex items-center gap-3">
        <NuxtLink to="/producteur/produits/ajouter" class="px-6 py-3 bg-[#2D5A27] text-white rounded-2xl font-bold text-sm shadow-lg hover:shadow-[#2D5A27]/20 transition-all hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Ajouter un produit
        </NuxtLink>
      </div>
    </div>

    <!-- Grille de Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="(stat, index) in stats" :key="index" 
        class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-500 group relative overflow-hidden"
      >
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#F5F0E8] rounded-full opacity-0 group-hover:opacity-100 transition-all duration-700 scale-0 group-hover:scale-100"></div>
        
        <div class="relative z-10">
          <div :class="`w-12 h-12 rounded-2xl ${stat.bgColor} ${stat.textColor} flex items-center justify-center mb-4 shadow-inner`">
            <component :is="stat.icon" class="w-6 h-6" />
          </div>
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">{{ stat.label }}</p>
          <div class="flex items-baseline gap-2">
            <h3 class="text-2xl font-black text-gray-900">{{ stat.value }}</h3>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Tableau des derniers produits -->
      <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
          <h3 class="text-xl font-black text-gray-900">Mes Derniers Produits</h3>
          <NuxtLink to="/producteur/produits" class="text-xs font-bold text-[#2D5A27] hover:underline">Voir tout →</NuxtLink>
        </div>

        <div v-if="loading" class="p-12 space-y-4">
          <div v-for="i in 3" :key="i" class="h-12 bg-gray-50 animate-pulse rounded-xl"></div>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-50/50">
                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Produit</th>
                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Catégorie</th>
                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Stock</th>
                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Prix</th>
                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="product in produits.slice(0, 5)" :key="product.id" class="hover:bg-[#F5F0E8]/30 transition-colors group">
                <td class="px-8 py-5">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#F5F0E8] overflow-hidden flex items-center justify-center text-xl group-hover:scale-105 transition-transform">
                      <img v-if="product.photo" :src="`http://127.0.0.1:8000/storage/products/${product.photo}`" class="w-full h-full object-cover" />
                      <span v-else>{{ getProductIcon(product.sous_categorie?.categorie?.nom) }}</span>
                    </div>
                    <div>
                      <span class="text-sm font-bold text-gray-900">{{ product.nom }}</span>
                    </div>
                  </div>
                </td>
                <td class="px-8 py-5">
                  <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-[10px] font-black uppercase tracking-wider">
                    {{ product.sous_categorie?.nom || '—' }}
                  </span>
                </td>
                <td class="px-8 py-5 text-center">
                  <span :class="`text-sm font-black ${product.quantite < 10 ? 'text-red-500' : 'text-gray-700'}`">
                    {{ product.quantite }}
                  </span>
                </td>
                <td class="px-8 py-5">
                  <span class="text-sm font-black text-[#2D5A27]">{{ formatPrice(product.prix) }}</span>
                </td>
                <td class="px-8 py-5 text-right">
                  <NuxtLink :to="`/producteur/produits/modifier/${product.id}`" class="text-xs font-bold text-[#2D5A27] hover:underline">
                    Modifier
                  </NuxtLink>
                </td>
              </tr>
              <tr v-if="produits.length === 0">
                <td colspan="5" class="px-8 py-12 text-center text-gray-400 font-medium italic">
                  Aucun produit enregistré.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Produits en vedette ou Activité récente -->
      <div class="bg-[#2D5A27] rounded-[2.5rem] shadow-xl p-8 text-white relative overflow-hidden flex flex-col justify-between">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
        
        <div>
          <h3 class="text-2xl font-black font-serif leading-tight mb-6">Prêt à vendre de nouveaux produits ?</h3>
          <p class="text-white/70 text-sm font-medium leading-relaxed mb-8">
            Ajoutez vos récoltes, élevages ou produits transformés au catalogue pour attirer plus d'acheteurs.
          </p>
          
          <div class="space-y-4 mb-8">
            <div class="flex items-center gap-4 p-4 bg-white/10 rounded-2xl border border-white/10 group hover:bg-white/15 transition-all">
              <div class="w-10 h-10 rounded-xl bg-[#8B7340] flex items-center justify-center text-white text-xl">🌱</div>
              <div>
                <p class="text-xs font-bold">Publier vos produits</p>
                <p class="text-[10px] text-white/50">Visibles par les distributeurs</p>
              </div>
            </div>
          </div>
        </div>

        <NuxtLink to="/producteur/produits/ajouter" class="w-full py-4 bg-[#8B7340] text-white rounded-2xl font-bold text-sm shadow-xl hover:shadow-[#8B7340]/30 transition-all text-center hover:bg-[#A68B4D]">
          Ajouter un Produit
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard'
})

import { ref, computed, onMounted, h } from 'vue'

const userName = ref('Producteur')
const produits = ref([])
const loading = ref(true)

const stats = ref([
  { label: 'Commandes reçues', value: '0', icon: IconOrders, bgColor: 'bg-amber-50', textColor: 'text-amber-600' },
  { label: 'Produits en vente', value: '0', icon: IconProducts, bgColor: 'bg-emerald-50', textColor: 'text-emerald-600' },
  { label: 'Revenus (Mois)', value: '0 FCFA', icon: IconWallet, bgColor: 'bg-blue-50', textColor: 'text-blue-600' },
  { label: 'Stock faible', value: '0', icon: IconWarning, bgColor: 'bg-red-50', textColor: 'text-red-500' }
])

// Icônes
function IconOrders() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' })])
}
function IconProducts() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' })])
}
function IconWallet() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' })])
}
function IconWarning() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' })])
}

onMounted(async () => {
  const userStr = localStorage.getItem('user')
  if (userStr) {
    const user = JSON.parse(userStr)
    userName.value = user.prenom || user.nom || 'Producteur'
  }
  await chargerDashboardData()
})

async function chargerDashboardData() {
  loading.value = true
  try {
    const token = localStorage.getItem('token')
    
    // 1. Charger les produits
    const data = await $fetch('http://127.0.0.1:8000/api/producteur/produits', {
      headers: { Authorization: `Bearer ${token}` }
    })
    produits.value = data || []
    
    // 2. Mettre à jour les stats
    stats.value[0].value = '0' // Endpoint commandes vide pour le moment
    stats.value[1].value = produits.value.length.toString()
    stats.value[2].value = '0 FCFA' // Pas encore de commandes validées implémentées
    stats.value[3].value = produits.value.filter(p => p.quantite <= 5).length.toString()
  } catch (error) {
    console.error('Erreur chargement dashboard producteur:', error)
  } finally {
    loading.value = false
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