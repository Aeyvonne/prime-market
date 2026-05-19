<template>
  <div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-2xl font-black text-gray-800">Mes Produits</h1>
        <p class="text-sm text-gray-500 mt-1">Gérez votre catalogue de produits</p>
      </div>
      <NuxtLink
        to="/producteur/produits/ajouter"
        class="flex items-center gap-2 bg-[#2D5A27] text-white px-5 py-3 rounded-2xl font-bold text-sm hover:bg-[#234820] transition-all shadow-lg"
      >
        <span class="text-lg">+</span> Ajouter un produit
      </NuxtLink>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="w-10 h-10 border-4 border-[#2D5A27] border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- Liste produits -->
    <div v-else-if="products.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div
        v-for="product in products"
        :key="product.id"
        class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all"
      >
        <!-- Image -->
        <div class="h-48 bg-[#F5F0E8] flex items-center justify-center">
          <img
            v-if="product.image"
            :src="product.image"
            :alt="product.nom"
            class="h-full w-full object-cover"
          >
          <span v-else class="text-5xl">🌿</span>
        </div>

        <!-- Infos -->
        <div class="p-5">
          <div class="flex items-start justify-between mb-2">
            <h3 class="font-black text-gray-800 text-lg leading-tight">{{ product.nom }}</h3>
            <span class="text-xs font-bold bg-[#F5F0E8] text-[#2D5A27] px-3 py-1 rounded-full ml-2 whitespace-nowrap">
              {{ product.categorie?.nom }}
            </span>
          </div>
          <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ product.description || 'Aucune description' }}</p>

          <div class="flex items-center justify-between mb-4">
            <span class="text-xl font-black text-[#2D5A27]">{{ product.prix.toLocaleString() }} FCFA</span>
            <span :class="[product.stock > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600', 'text-xs font-bold px-3 py-1 rounded-full']">
              {{ product.stock > 0 ? `Stock: ${product.stock}` : 'Rupture' }}
            </span>
          </div>

          <!-- Actions -->
          <div class="flex gap-2">
            <NuxtLink
              :to="`/producteur/produits/modifier/${product.id}`"
              class="flex-1 text-center py-2.5 bg-[#F5F0E8] text-[#2D5A27] font-bold text-sm rounded-xl hover:bg-[#e8e0d0] transition-all"
            >
              ✏️ Modifier
            </NuxtLink>
            <button
              @click="supprimerProduit(product.id)"
              class="flex-1 py-2.5 bg-red-50 text-red-500 font-bold text-sm rounded-xl hover:bg-red-100 transition-all"
            >
              🗑️ Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Aucun produit -->
    <div v-else class="flex flex-col items-center justify-center h-64 bg-white rounded-3xl border border-dashed border-gray-200">
      <span class="text-5xl mb-4">🌱</span>
      <p class="text-gray-500 font-bold text-lg">Aucun produit pour l'instant</p>
      <p class="text-gray-400 text-sm mb-6">Ajoutez votre premier produit</p>
      <NuxtLink
        to="/producteur/produits/ajouter"
        class="bg-[#2D5A27] text-white px-6 py-3 rounded-2xl font-bold text-sm hover:bg-[#234820] transition-all"
      >
        + Ajouter un produit
      </NuxtLink>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const products = ref([])
const loading = ref(true)
const config = useRuntimeConfig()

onMounted(async () => {
  await chargerProduits()
})

async function chargerProduits() {
  loading.value = true
  try {
    const token = localStorage.getItem('token')
    const data = await $fetch(`${config.public.apiUrl}/producteur/produits`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    products.value = data
  } catch (error) {
    console.error('Erreur chargement produits:', error)
  } finally {
    loading.value = false
  }
}

async function supprimerProduit(id) {
  if (!confirm('Voulez-vous vraiment supprimer ce produit ?')) return
  try {
    const token = localStorage.getItem('token')
    await $fetch(`${config.public.apiUrl}/producteur/produits/${id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token}` }
    })
    await chargerProduits()
  } catch (error) {
    console.error('Erreur suppression:', error)
  }
}
</script>