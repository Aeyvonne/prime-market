<template>
  <div class="min-h-screen bg-[#F5F0E8] font-sans pb-20 animate-in fade-in duration-700">
    <div v-if="pending" class="flex items-center justify-center h-96">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#2D5A27]"></div>
    </div>

    <div v-else-if="product" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 space-y-8">
      <NuxtLink to="/catalogue" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#2D5A27] transition-colors group">
        <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Retour au catalogue
      </NuxtLink>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 bg-white p-8 md:p-12 rounded-[3rem] shadow-sm border border-gray-100">
        <div class="space-y-6">
          <div class="h-[400px] bg-[#F5F0E8] rounded-[2.5rem] flex items-center justify-center text-9xl relative overflow-hidden group">
            <img v-if="product.photo" :src="`http://127.0.0.1:8000/storage/products/${product.photo}`" :alt="product.nom" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
            <span v-else class="group-hover:scale-110 transition-transform duration-700 opacity-20">{{ getProductIcon(product.categorie) }}</span>
            <div class="absolute inset-0 bg-gradient-to-t from-black/5 to-transparent"></div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="bg-emerald-50 text-[#2D5A27] px-5 py-4 rounded-2xl">
              <p class="text-[10px] font-black uppercase tracking-widest opacity-60">Status</p>
              <p class="text-sm font-black">{{ product.status }}</p>
            </div>
            <div class="bg-amber-50 text-[#8B7340] px-5 py-4 rounded-2xl">
              <p class="text-[10px] font-black uppercase tracking-widest opacity-60">Disponible</p>
              <p class="text-sm font-black">{{ product.quantite }} unité{{ product.quantite > 1 ? 's' : '' }}</p>
            </div>
          </div>
        </div>

        <div class="flex flex-col">
          <div>
            <span class="inline-block px-4 py-1.5 bg-[#8B7340]/10 text-[#8B7340] rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-4">{{ product.sous_categorie }}</span>
            <h1 class="text-4xl font-black text-gray-900 font-serif leading-tight mb-4">{{ product.nom }}</h1>

            <div class="flex items-center gap-4 p-4 bg-[#F5F0E8]/50 rounded-2xl border border-[#2D5A27]/10 mb-6">
              <div class="w-12 h-12 bg-[#2D5A27] rounded-xl flex items-center justify-center text-white font-black text-xl shadow-lg">{{ vendeur?.nom?.[0] || 'V' }}</div>
              <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">{{ vendeur?.type === 'producteur' ? 'Producteur' : 'Distributeur' }}</p>
                <p class="text-sm font-black text-gray-800">{{ vendeur?.nom || 'Vendeur' }}</p>
              </div>
            </div>

            <p class="text-gray-500 font-medium leading-relaxed mb-8">{{ product.description || 'Aucune description disponible.' }}</p>
          </div>

          <div class="bg-[#F5F0E8]/50 rounded-[2rem] p-6 mb-6">
            <h3 class="text-sm font-black text-gray-900 mb-4 flex items-center gap-2">
              <svg class="w-4 h-4 text-[#2D5A27]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
              Traçabilité du produit
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
              <div class="bg-white rounded-xl p-3">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Origine</p>
                <p class="font-bold text-gray-800">{{ vendeur?.secteur_travail || 'Non spécifiée' }}</p>
              </div>
              <div class="bg-white rounded-xl p-3">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ vendeur?.type === 'producteur' ? 'Producteur' : 'Distributeur' }}</p>
                <p class="font-bold text-gray-800">{{ vendeur?.prenom }} {{ vendeur?.nom }}</p>
              </div>
              <div class="bg-white rounded-xl p-3">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Contact</p>
                <p class="font-bold text-gray-800">{{ vendeur?.telephone || '—' }}</p>
              </div>
            </div>
          </div>

          <div v-if="userRole === 'distributeur' || userRole === 'consommateur'" class="mt-auto bg-gray-50 p-8 rounded-[2rem] border border-gray-100">
            <div class="flex items-center justify-between mb-6">
              <div>
                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Prix unitaire</span>
                <span class="text-3xl font-black text-[#2D5A27] block">{{ formatPrice(product.prix) }}</span>
              </div>
              <div class="flex items-center bg-white rounded-xl border border-gray-200 p-1">
                <button @click="decrementerQuantite" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-400 font-bold">−</button>
                <input v-model="quantity" type="number" readonly class="w-12 text-center font-black text-gray-800 bg-transparent outline-none">
                <button @click="incrementerQuantite" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-400 font-bold">+</button>
              </div>
            </div>

            <button @click="ajouterPanier"
              class="w-full py-5 bg-[#2D5A27] text-white rounded-2xl font-black text-lg shadow-xl shadow-[#2D5A27]/20 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center gap-3">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
              <span>Ajouter au panier ({{ formatPrice(product.prix * quantity) }})</span>
            </button>
          </div>
          <div v-else class="mt-auto bg-gray-50 p-8 rounded-[2rem] border border-gray-100">
            <p class="text-3xl font-black text-[#2D5A27] mb-2">{{ formatPrice(product.prix) }}</p>
            <p class="text-sm text-gray-500">Connectez-vous pour ajouter au panier.</p>
          </div>
        </div>
      </div>
    </div>
    <!-- Cart icon -->
    <div v-if="userRole === 'distributeur' || userRole === 'consommateur'"
      class="fixed top-6 right-6 z-50">
      <NuxtLink to="/consommateur/panier"
        class="relative flex items-center gap-2 bg-white rounded-2xl shadow-lg px-4 py-3 hover:shadow-xl transition-all group">
        <svg class="w-5 h-5 text-[#2D5A27] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
        <span class="text-xs font-bold text-[#2D5A27]">Panier</span>
        <span v-if="cartCount > 0"
          class="absolute -top-2 -right-2 min-w-[20px] h-5 bg-red-500 text-white rounded-full text-[10px] font-black flex items-center justify-center px-1 shadow-md">
          {{ cartCount }}
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
definePageMeta({ layout: 'default' })

const route = useRoute()
const quantity = ref(1)
const product = ref(null)
const pending = ref(true)
const userRole = ref(null)
const rawData = ref(null)

const cartStore = useCartStore()
const cartCount = computed(() => cartStore.count)

const toastMessage = ref('')
const toastVisible = ref(false)
let toastTimer = null

function showToast(msg) {
  toastMessage.value = msg
  toastVisible.value = true
  if (toastTimer) clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toastVisible.value = false }, 2000)
}

const vendeur = computed(() => {
  if (product.value?.producteur) return { ...product.value.producteur, type: 'producteur' }
  if (product.value?.distributeur) return { ...product.value.distributeur, type: 'distributeur' }
  return null
})

function decrementerQuantite() {
  if (quantity.value > 1) quantity.value--
}

function incrementerQuantite() {
  if (quantity.value < product.value.quantite) quantity.value++
}

onMounted(async () => {
  const userStr = localStorage.getItem('user')
  if (userStr) {
    const user = JSON.parse(userStr)
    userRole.value = user.role
  }

  pending.value = true
  try {
    const raw = await $fetch(`http://127.0.0.1:8000/api/produits/${route.params.id}`)
    rawData.value = raw
    product.value = {
      id: raw.id,
      nom: raw.nom,
      description: raw.description,
      prix: raw.prix,
      photo: raw.photo,
      quantite: raw.quantite,
      status: raw.status,
      categorie: raw.sous_categorie?.categorie?.nom || null,
      sous_categorie: raw.sous_categorie?.nom || null,
      producteur: raw.producteur ? {
        nom: raw.producteur.user?.nom || 'Inconnu',
        prenom: raw.producteur.user?.prenom || '',
        telephone: raw.producteur.user?.telephone || '',
        adresse: raw.producteur.adresse || '',
        secteur_travail: raw.producteur.secteur_travail || null,
      } : null,
      distributeur: raw.distributeur ? {
        nom: raw.distributeur.user?.nom || 'Inconnu',
        prenom: raw.distributeur.user?.prenom || '',
        telephone: raw.distributeur.user?.telephone || '',
        adresse: raw.distributeur.adresse || '',
        secteur_travail: raw.distributeur.secteur_travail || null,
      } : null,
    }
  } catch (err) {
    console.error('Erreur chargement détail:', err)
  } finally {
    pending.value = false
  }
})

function ajouterPanier() {
  if (!rawData.value) return
  const store = useCartStore()
  store.ajouter({
    id: rawData.value.id,
    nom: rawData.value.nom,
    prix: rawData.value.prix,
    photo: rawData.value.photo || null,
    quantite_stock: rawData.value.quantite || 999,
    vendeur_id: rawData.value.producteur_id ?? (rawData.value.proprietaire_type === 'distributeur' ? rawData.value.proprietaire_id : null),
    vendeur_nom: rawData.value.producteur?.user?.nom || rawData.value.distributeur?.user?.nom || '',
  })
  store.modifierQuantite(rawData.value.id, quantity.value)
  showToast('Produit ajouté au panier')
}

function formatPrice(price) {
  if (!price) return '0 FCFA'
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}

function getProductIcon(category) {
  const icons = { 'Agriculture': '🥦', 'Élevage': '🐄', 'Pêche': '🐟', 'Transformés': '🍯' }
  return icons[category] || '📦'
}
</script>
