<template>
  <div>
    <div class="mb-8">
      <h1 class="text-2xl font-black text-gray-800">Tableau de bord</h1>
      <p class="text-sm text-gray-500 mt-1">Bienvenue, {{ userName }} 👋</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <span class="text-3xl">📦</span>
          <span class="text-xs font-bold bg-green-100 text-green-600 px-3 py-1 rounded-full">Total</span>
        </div>
        <p class="text-3xl font-black text-gray-800">{{ stats.totalProduits }}</p>
        <p class="text-sm text-gray-500 font-medium mt-1">Produits</p>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <span class="text-3xl">🛒</span>
          <span class="text-xs font-bold bg-blue-100 text-blue-600 px-3 py-1 rounded-full">En attente</span>
        </div>
        <p class="text-3xl font-black text-gray-800">{{ stats.commandesEnAttente }}</p>
        <p class="text-sm text-gray-500 font-medium mt-1">Commandes</p>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <span class="text-3xl">💰</span>
          <span class="text-xs font-bold bg-yellow-100 text-yellow-600 px-3 py-1 rounded-full">Ce mois</span>
        </div>
        <p class="text-3xl font-black text-gray-800">{{ stats.revenus.toLocaleString() }}</p>
        <p class="text-sm text-gray-500 font-medium mt-1">FCFA</p>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <span class="text-3xl">⚠️</span>
          <span class="text-xs font-bold bg-red-100 text-red-600 px-3 py-1 rounded-full">Alerte</span>
        </div>
        <p class="text-3xl font-black text-gray-800">{{ stats.stockFaible }}</p>
        <p class="text-sm text-gray-500 font-medium mt-1">Stock faible</p>
      </div>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="flex items-center justify-between p-6 border-b border-gray-50">
        <h2 class="text-lg font-black text-gray-800">Mes derniers produits</h2>
        <NuxtLink to="/producteur/produits" class="text-sm font-bold text-[#2D5A27] hover:underline">
          Voir tout →
        </NuxtLink>
      </div>

      <div v-if="loading" class="flex justify-center items-center h-32">
        <div class="w-8 h-8 border-4 border-[#2D5A27] border-t-transparent rounded-full animate-spin"></div>
      </div>

      <div v-else-if="produits.length > 0" class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-[#F5F0E8]">
            <tr>
              <th class="text-left px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">Produit</th>
              <th class="text-left px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">Catégorie</th>
              <th class="text-left px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">Prix</th>
              <th class="text-left px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">Stock</th>
              <th class="text-left px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="product in produits.slice(0, 5)" :key="product.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 font-bold text-gray-800 text-sm">{{ product.nom }}</td>
              <td class="px-6 py-4">
                <span class="text-xs font-bold bg-[#F5F0E8] text-[#2D5A27] px-3 py-1 rounded-full">
                  {{ product.sousCategorie?.nom || '—' }}
                </span>
              </td>
              <td class="px-6 py-4 font-black text-[#2D5A27] text-sm">{{ product.prix?.toLocaleString() }} FCFA</td>
              <td class="px-6 py-4">
                <span :class="[product.quantite > 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600', 'text-xs font-bold px-3 py-1 rounded-full']">
                  {{ product.quantite }}
                </span>
              </td>
              <td class="px-6 py-4">
                <NuxtLink :to="`/producteur/produits/modifier/${product.id}`" class="text-xs font-bold text-[#2D5A27] hover:underline">
                  Modifier
                </NuxtLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="flex flex-col items-center justify-center h-32">
        <p class="text-gray-400 font-bold">Aucun produit pour l'instant</p>
        <NuxtLink to="/producteur/produits/ajouter" class="text-sm font-bold text-[#2D5A27] hover:underline mt-2">
          + Ajouter votre premier produit
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const config = useRuntimeConfig()
const loading = ref(true)
const produits = ref([])
const userName = ref('Producteur')

const stats = computed(() => ({
  totalProduits: produits.value.length,
  commandesEnAttente: 0,
  revenus: 0,
  stockFaible: produits.value.filter(p => p.quantite <= 5).length
}))

onMounted(async () => {
  const userStr = localStorage.getItem('user')
  if (userStr) {
    const user = JSON.parse(userStr)
    userName.value = user.prenom || user.nom || 'Producteur'
  }
  await chargerProduits()
})

async function chargerProduits() {
  loading.value = true
  try {
    const token = localStorage.getItem('token')
    const data = await $fetch(`${config.public.apiUrl}/producteur/produits`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    produits.value = data
  } catch (error) {
    console.error('Erreur:', error)
  } finally {
    loading.value = false
  }
}
</script>