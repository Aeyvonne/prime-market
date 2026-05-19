<template>
  <div class="animate-in slide-in-from-bottom duration-700">
    <div v-if="pending" class="flex items-center justify-center h-96">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#2D5A27]"></div>
    </div>

    <div v-else-if="product" class="max-w-6xl mx-auto space-y-8">
      <!-- Navigation Back -->
      <NuxtLink to="/catalogue" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#2D5A27] transition-colors group">
        <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Retour au catalogue
      </NuxtLink>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 bg-white p-8 md:p-12 rounded-[3rem] shadow-sm border border-gray-100">
        <!-- Visual Column -->
        <div class="space-y-6">
          <div class="h-[400px] bg-[#F5F0E8] rounded-[2.5rem] flex items-center justify-center text-9xl relative overflow-hidden group">
            <img v-if="product.photo" :src="`http://127.0.0.1:8000/storage/products/${product.photo}`" :alt="product.nom" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
            <span v-else class="group-hover:scale-110 transition-transform duration-700 opacity-20">{{ getProductIcon(product.sous_categorie?.categorie?.nom) }}</span>
            <div class="absolute inset-0 bg-gradient-to-t from-black/5 to-transparent"></div>
          </div>
          
          <div class="flex items-center gap-4">
            <div class="bg-emerald-50 text-[#2D5A27] px-6 py-4 rounded-2xl flex-grow">
              <p class="text-[10px] font-black uppercase tracking-widest opacity-60">Status</p>
              <p class="text-sm font-black">{{ product.status }}</p>
            </div>
            <div class="bg-amber-50 text-[#8B7340] px-6 py-4 rounded-2xl flex-grow">
              <p class="text-[10px] font-black uppercase tracking-widest opacity-60">Quantité Dispo</p>
              <p class="text-sm font-black">{{ product.quantite }} Unités</p>
            </div>
          </div>
        </div>

        <!-- Info Column -->
        <div class="flex flex-col">
          <div class="mb-8">
            <span class="inline-block px-4 py-1.5 bg-[#8B7340]/10 text-[#8B7340] rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-4">
              {{ product.sous_categorie?.nom }}
            </span>
            <h1 class="text-4xl font-black text-gray-900 font-serif leading-tight mb-4">{{ product.nom }}</h1>
            
            <!-- Seller Profile -->
            <div class="flex items-center gap-4 p-4 bg-[#F5F0E8]/50 rounded-2xl border border-[#2D5A27]/10 mb-6">
              <div class="w-12 h-12 bg-[#2D5A27] rounded-xl flex items-center justify-center text-white font-black text-xl shadow-lg">
                {{ (product.producteur?.user?.nom || product.distributeur?.user?.nom || 'P')[0] }}
              </div>
              <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">{{ product.producteur ? 'Producteur Certifié' : 'Distributeur' }}</p>
                <p class="text-sm font-black text-gray-800">{{ product.producteur?.user?.nom || product.distributeur?.user?.nom || 'Vendeur Inconnu' }}</p>
              </div>
            </div>

            <p class="text-gray-500 font-medium leading-relaxed mb-8">
              {{ product.description || 'Ce produit de haute qualité est cultivé avec soin par nos producteurs locaux selon des normes respectueuses de l environnement.' }}
            </p>
          </div>

          <!-- Checkout Box -->
          <div class="mt-auto bg-gray-50 p-8 rounded-[2rem] border border-gray-100">
            <div class="flex items-center justify-between mb-6">
              <div class="flex flex-col">
                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Prix Total</span>
                <span class="text-3xl font-black text-[#2D5A27]">{{ formatPrice(product.prix * quantity) }}</span>
              </div>
              <div class="flex items-center bg-white rounded-xl border border-gray-200 p-1">
                <button @click="quantity > 1 && quantity--" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-400">-</button>
                <input v-model="quantity" type="number" readonly class="w-12 text-center font-black text-gray-800 bg-transparent outline-none">
                <button @click="quantity < product.quantite && quantity++" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-400">+</button>
              </div>
            </div>

            <button 
              @click="handleOrder" 
              :disabled="ordering || product.quantite === 0"
              class="w-full py-5 bg-[#2D5A27] text-white rounded-2xl font-black text-lg shadow-xl shadow-[#2D5A27]/20 hover:-translate-y-1 transition-all active:scale-95 disabled:opacity-50 disabled:grayscale flex items-center justify-center gap-3"
            >
              <span>{{ ordering ? 'Traitement en cours...' : 'Commander maintenant' }}</span>
              <svg v-if="!ordering" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

definePageMeta({
  layout: 'default'
})

const route = useRoute()
const quantity = ref(1)
const ordering = ref(false)
const product = ref(null)
const pending = ref(true)

onMounted(async () => {
  pending.value = true
  try {
    product.value = await $fetch(`http://127.0.0.1:8000/api/produits/${route.params.id}`)
  } catch (err) {
    console.error('Erreur chargement détail:', err)
  } finally {
    pending.value = false
  }
})

async function handleOrder() {
  ordering.value = true
  try {
    const payload = {
      vendeur_id: product.value.producteur?.id,
      items: [
        {
          produit_id: product.value.id,
          quantite: quantity.value
        }
      ],
      mode_livraison: 'Standard',
      adresse_livraison: JSON.parse(localStorage.getItem('user'))?.adresse || 'Adresse par défaut'
    }

    const token = localStorage.getItem('token')
    if (!token) {
      alert('Veuillez vous connecter pour passer une commande.')
      return navigateTo('/login')
    }

    const role = JSON.parse(localStorage.getItem('user'))?.role || 'consommateur'
    let orderUrl = 'http://127.0.0.1:8000/api/consommateur/commandes'
    let redirectUrl = '/consommateur/commandes'

    if (role === 'distributeur') {
      orderUrl = 'http://127.0.0.1:8000/api/distributeur/commandes'
      redirectUrl = '/distributeur/commandes'
    }

    const response = await $fetch(orderUrl, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token}`
      },
      body: payload
    })

    alert('Commande passée avec succès ! Redirection vers vos commandes...')
    navigateTo(redirectUrl)
  } catch (err) {
    console.error('Erreur commande:', err)
    alert('Une erreur est survenue lors de la commande.')
  } finally {
    ordering.value = false
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
