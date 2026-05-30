<template>
  <div class="space-y-8 animate-in fade-in duration-700 max-w-3xl mx-auto">
    <NuxtLink to="/catalogue" class="inline-flex items-center gap-2 text-sm font-bold text-[#2D5A27] hover:underline">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Retour au catalogue
    </NuxtLink>

    <div v-if="success" class="bg-emerald-50 border border-emerald-200 rounded-[2rem] p-8 text-center">
      <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      </div>
      <h2 class="text-2xl font-black text-gray-900 font-serif mb-2">Commande confirmée !</h2>
      <p class="text-gray-500 mb-6">Votre commande #{{ successId }} a été créée avec succès.</p>
      <div class="flex gap-3 justify-center">
        <NuxtLink :to="`/distributeur/commandes/${successId}`"
          class="px-6 py-3 bg-[#2D5A27] text-white rounded-2xl font-bold text-sm hover:bg-[#2D5A27]/90 transition-all">
          Voir la commande
        </NuxtLink>
        <NuxtLink to="/catalogue"
          class="px-6 py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-200 transition-all">
          Continuer mes achats
        </NuxtLink>
      </div>
    </div>

    <template v-else>
      <div>
        <h1 class="text-3xl font-black text-gray-900 font-serif">Nouvelle commande</h1>
        <p class="text-gray-500 font-medium mt-1">Renseignez les détails de votre commande.</p>
      </div>

      <div v-if="loading" class="text-center py-20">
        <p class="text-gray-400 font-medium italic">Chargement...</p>
      </div>

      <div v-else-if="error" class="text-center py-20">
        <p class="text-red-400 font-medium">{{ error }}</p>
      </div>

      <form v-else @submit.prevent="submitCommande" class="space-y-6">
        <!-- Récapitulatif produit -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
          <h3 class="text-lg font-black text-gray-900 mb-4">Produit</h3>
          <div class="flex items-center gap-4">
            <div v-if="product.photo" class="w-20 h-20 rounded-2xl bg-[#F5F0E8] overflow-hidden flex-shrink-0">
              <img :src="`http://127.0.0.1:8000/storage/products/${product.photo}`" :alt="product.nom" class="w-full h-full object-cover">
            </div>
            <div v-else class="w-20 h-20 rounded-2xl bg-[#F5F0E8] flex items-center justify-center text-3xl flex-shrink-0">
              📦
            </div>
            <div class="flex-1">
              <h4 class="text-lg font-black text-gray-900">{{ product.nom }}</h4>
              <p class="text-sm text-gray-500">Vendeur : {{ product.vendeur }}</p>
              <p class="text-sm font-black text-[#2D5A27]">{{ formatPrice(product.prix) }} / unité</p>
            </div>
          </div>

          <div class="mt-4 flex items-center gap-4 bg-[#F5F0E8]/50 rounded-xl p-4">
            <div>
              <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Quantité</p>
              <div class="flex items-center bg-white rounded-xl border border-gray-200 p-1">
                <button type="button" @click="quantite > 1 && quantite--" class="w-9 h-9 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-400 font-bold">−</button>
                <input v-model="quantite" type="number" min="1" :max="product.stock"
                  class="w-14 text-center font-black text-gray-800 bg-transparent outline-none text-sm" readonly>
                <button type="button" @click="quantite < product.stock && quantite++" class="w-9 h-9 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-400 font-bold">+</button>
              </div>
            </div>
            <div class="ml-auto text-right">
              <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Total</p>
              <p class="text-xl font-black text-[#2D5A27]">{{ formatPrice(product.prix * quantite) }}</p>
            </div>
          </div>
        </div>

        <!-- Livraison -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
          <h3 class="text-lg font-black text-gray-900 mb-4">Livraison</h3>
          <div class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1.5">Mode de livraison</label>
              <div class="flex gap-3">
                <button type="button" v-for="mode in ['Domicile', 'Retrait']" :key="mode"
                  @click="modeLivraison = mode"
                  :class="`flex-1 px-4 py-3 rounded-xl text-sm font-bold border-2 transition-all ${
                    modeLivraison === mode
                      ? 'bg-[#2D5A27] text-white border-[#2D5A27] shadow-lg'
                      : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'
                  }`">
                  {{ mode }}
                </button>
              </div>
            </div>
            <div v-if="modeLivraison === 'Domicile'">
              <label class="block text-xs font-bold text-gray-700 mb-1.5">Adresse de livraison</label>
              <input v-model="adresseLivraison" type="text" placeholder="Entrez votre adresse..."
                class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-medium focus:outline-none focus:border-[#2D5A27] focus:ring-1 focus:ring-[#2D5A27] transition-all">
            </div>
          </div>
        </div>

        <!-- Paiement -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
          <h3 class="text-lg font-black text-gray-900 mb-4">Paiement</h3>
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Méthode de paiement</label>
            <div class="flex gap-3">
              <button type="button" v-for="method in ['Wave', 'OrangeMoney']" :key="method"
                @click="methodePaiement = method"
                :class="`flex-1 px-4 py-3 rounded-xl text-sm font-bold border-2 transition-all ${
                  methodePaiement === method
                    ? 'bg-[#2D5A27] text-white border-[#2D5A27] shadow-lg'
                    : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'
                }`">
                {{ method }}
              </button>
            </div>
          </div>
        </div>

        <!-- Récapitulatif -->
        <div class="bg-[#2D5A27] text-white rounded-[2rem] p-6">
          <div class="flex items-center justify-between mb-4">
            <span class="text-sm opacity-70">Sous-total</span>
            <span class="text-sm font-bold">{{ formatPrice(product.prix * quantite) }}</span>
          </div>
          <div class="border-t border-white/20 pt-4 flex items-center justify-between">
            <span class="text-lg font-black">Total à payer</span>
            <span class="text-2xl font-black">{{ formatPrice(product.prix * quantite) }}</span>
          </div>
        </div>

        <button type="submit" :disabled="submitting"
          class="w-full py-4 bg-[#2D5A27] text-white rounded-2xl font-black text-lg shadow-xl shadow-[#2D5A27]/20 hover:-translate-y-1 transition-all active:scale-95 disabled:opacity-50">
          {{ submitting ? 'Traitement en cours...' : 'Confirmer la commande' }}
        </button>
      </form>
    </template>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const route = useRoute()
const product = ref({ nom: '', prix: 0, photo: null, stock: 0, vendeur: '' })
const quantite = ref(parseInt(route.query.quantite) || 1)
const modeLivraison = ref('Domicile')
const adresseLivraison = ref('')
const methodePaiement = ref('Wave')
const submitting = ref(false)
const loading = ref(true)
const error = ref(null)
const success = ref(false)
const successId = ref(null)

let rawProduct = null

onMounted(async () => {
  const produitId = route.query.produit_id
  if (!produitId) {
    error.value = 'Aucun produit sélectionné.'
    loading.value = false
    return
  }
  try {
    rawProduct = await $fetch(`http://127.0.0.1:8000/api/produits/${produitId}`)
    product.value = {
      nom: rawProduct.nom,
      prix: rawProduct.prix,
      photo: rawProduct.photo,
      stock: rawProduct.quantite,
      vendeur: rawProduct.producteur?.user
        ? `${rawProduct.producteur.user.prenom || ''} ${rawProduct.producteur.user.nom || ''}`.trim()
        : rawProduct.proprietaire_type === 'distributeur'
          ? 'Autre distributeur'
          : 'Producteur',
    }
  } catch (err) {
    error.value = 'Erreur lors du chargement du produit.'
  } finally {
    loading.value = false
  }
})

async function submitCommande() {
  const token = localStorage.getItem('token')
  if (!token) return navigateTo('/login')

  const produitId = route.query.produit_id
  if (!produitId) return

  if (modeLivraison.value === 'Domicile' && !adresseLivraison.value.trim()) {
    alert('Veuillez renseigner une adresse de livraison avant de payer.')
    submitting.value = false
    return
  }

  submitting.value = true
  try {
    const res = await $fetch('http://127.0.0.1:8000/api/distributeur/commandes', {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` },
      body: {
        vendeur_id: rawProduct.producteur_id ?? (rawProduct.proprietaire_type === 'distributeur' ? rawProduct.proprietaire_id : null),
        items: [{ produit_id: parseInt(produitId), quantite: quantite.value }],
        mode_livraison: modeLivraison.value,
        adresse_livraison: modeLivraison.value === 'Domicile' ? adresseLivraison.value : null,
        methode_paiement: methodePaiement.value,
      },
    })
    success.value = true
    successId.value = res.commande.id
  } catch (err) {
    const msg = err.data?.message || err.data?.errors?.[0]?.message || 'Erreur lors de la commande.'
    alert(msg)
  } finally {
    submitting.value = false
  }
}

function formatPrice(price) {
  if (!price) return '0 FCFA'
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' })
    .format(price).replace('XOF', 'FCFA')
}
</script>
