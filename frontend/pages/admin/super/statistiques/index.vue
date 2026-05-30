<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 font-serif">
          Statistiques <span class="text-[#2D5A27]">Analytiques</span> 📊
        </h1>
        <p class="text-gray-500 font-medium mt-1">Données détaillées d'activité de la plateforme Prime Market.</p>
      </div>
    </div>

    <!-- Counters Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-green-50 rounded-full opacity-50 scale-0 group-hover:scale-100 transition-all duration-700"></div>
        <div class="relative z-10">
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Utilisateurs</p>
          <h2 class="text-4xl font-black text-gray-900 font-serif">{{ stats.total_comptes }}</h2>
          <p class="text-xs text-gray-400 font-bold mt-2">Comptes enregistrés</p>
        </div>
      </div>

      <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 scale-0 group-hover:scale-100 transition-all duration-700"></div>
        <div class="relative z-10">
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Produits</p>
          <h2 class="text-4xl font-black text-gray-900 font-serif">{{ stats.total_produits }}</h2>
          <p class="text-xs text-gray-400 font-bold mt-2">Références au catalogue</p>
        </div>
      </div>

      <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 scale-0 group-hover:scale-100 transition-all duration-700"></div>
        <div class="relative z-10">
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Commandes</p>
          <h2 class="text-4xl font-black text-gray-900 font-serif">{{ stats.total_commandes }}</h2>
          <p class="text-xs text-gray-400 font-bold mt-2">Commandes finalisées ou en cours</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- User Roles Breakdown Chart (CSS progress bars) -->
      <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
        <h3 class="text-xl font-serif font-black text-gray-900 mb-6">Répartition des Utilisateurs</h3>
        <div class="space-y-6">
          <div v-for="role in userRolesBreakdown" :key="role.label" class="space-y-2">
            <div class="flex justify-between text-xs font-bold text-gray-600">
              <span class="flex items-center gap-2">
                <span :class="`w-3 h-3 rounded-full ${role.bgColor}`"></span>
                {{ role.label }}
              </span>
              <span>{{ role.count }} ({{ getPercentage(role.count, stats.total_comptes) }}%)</span>
            </div>
            <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
              <div 
                class="h-full rounded-full transition-all duration-1000 ease-out" 
                :class="role.bgColor"
                :style="{ width: `${getPercentage(role.count, stats.total_comptes)}%` }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Categories & Subcategories distribution -->
      <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
        <h3 class="text-xl font-serif font-black text-gray-900 mb-6">Volume de Sous-Catégories par Catégorie</h3>
        
        <div v-if="loadingCategories" class="space-y-4">
          <div v-for="i in 3" :key="i" class="h-10 bg-gray-50 animate-pulse rounded-xl"></div>
        </div>

        <div v-else class="space-y-6">
          <div v-for="cat in categoryData" :key="cat.id" class="space-y-2">
            <div class="flex justify-between text-xs font-bold text-gray-600">
              <span class="font-bold">{{ cat.nom }}</span>
              <span>{{ cat.sous_categories?.length || 0 }} sous-catégories</span>
            </div>
            <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
              <div 
                class="h-full rounded-full bg-[#8B7340] transition-all duration-1000 ease-out" 
                :style="{ width: `${getCategoryPercentage(cat.sous_categories?.length || 0)}%` }"
              ></div>
            </div>
          </div>

          <div v-if="categoryData.length === 0" class="text-sm text-gray-400 italic text-center py-6">
            Aucune donnée de catégorie disponible.
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard'
})

import { ref, computed, onMounted } from 'vue'

const stats = ref({
  total_comptes: 0,
  total_producteurs: 0,
  total_distributeurs: 0,
  total_consommateurs: 0,
  total_transporteurs: 0,
  total_produits: 0,
  total_commandes: 0,
  total_transactions: 0,
  revenu_global: 0
})

const categoryData = ref([])
const loadingCategories = ref(true)

const userRolesBreakdown = computed(() => [
  { label: 'Producteurs', count: stats.value.total_producteurs, bgColor: 'bg-green-600' },
  { label: 'Distributeurs', count: stats.value.total_distributeurs, bgColor: 'bg-amber-500' },
  { label: 'Consommateurs', count: stats.value.total_consommateurs, bgColor: 'bg-blue-500' },
  { label: 'Transporteurs', count: stats.value.total_transporteurs, bgColor: 'bg-purple-500' }
])

const totalSubCategories = computed(() => {
  return categoryData.value.reduce((sum, cat) => sum + (cat.sous_categories?.length || 0), 0) || 1
})

function getPercentage(count, total) {
  const t = total || 1
  return Math.round((count / t) * 100)
}

function getCategoryPercentage(subcount) {
  return Math.round((subcount / totalSubCategories.value) * 100)
}

onMounted(async () => {
  const token = localStorage.getItem('token')
  
  // 1. Fetch statistics
  try {
    const data = await $fetch('http://127.0.0.1:8000/api/super-admin/statistiques', {
      headers: { Authorization: `Bearer ${token}` }
    })
    if (data) stats.value = data
  } catch (err) {
    console.error('Erreur chargement statistiques:', err)
  }

  // 2. Fetch categories
  try {
    loadingCategories.value = true
    const catData = await $fetch('http://127.0.0.1:8000/api/super-admin/categories', {
      headers: { Authorization: `Bearer ${token}` }
    })
    if (catData) categoryData.value = catData
  } catch (err) {
    console.error('Erreur chargement categories pour statistiques:', err)
  } finally {
    loadingCategories.value = false
  }
})
</script>
