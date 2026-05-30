<template>
  <div class="animate-in fade-in duration-700">
    <div v-if="loading" class="flex items-center justify-center h-96">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#2D5A27]"></div>
    </div>

    <div v-else-if="error" class="max-w-lg mx-auto py-20 text-center">
      <div class="text-6xl mb-4 opacity-20">⚠️</div>
      <h3 class="text-xl font-black text-gray-900">Erreur</h3>
      <p class="text-gray-500 font-medium mt-2">{{ error }}</p>
      <NuxtLink to="/catalogue" class="mt-6 inline-block px-6 py-3 bg-[#2D5A27] text-white rounded-xl font-bold text-sm">Retour au catalogue</NuxtLink>
    </div>

    <div v-else-if="!produit" class="max-w-lg mx-auto py-20 text-center">
      <div class="text-6xl mb-4 opacity-20">📦</div>
      <h3 class="text-xl font-black text-gray-900">Produit introuvable</h3>
      <p class="text-gray-500 font-medium mt-2">Aucun produit sélectionné.</p>
      <NuxtLink to="/catalogue" class="mt-6 inline-block px-6 py-3 bg-[#2D5A27] text-white rounded-xl font-bold text-sm">Voir le catalogue</NuxtLink>
    </div>

    <div v-else class="max-w-4xl mx-auto space-y-8">
      <div class="flex items-center gap-4">
        <NuxtLink to="/catalogue" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#2D5A27] transition-colors group">
          <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
          Catalogue
        </NuxtLink>
        <span class="text-gray-300">/</span>
        <span class="text-sm font-bold text-gray-400">Nouvelle commande</span>
      </div>

      <h1 class="text-3xl font-black text-gray-900 font-serif">Passer une <span class="text-[#2D5A27]">commande</span></h1>

      <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <div class="lg:col-span-3 space-y-6">
          <div class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-lg font-black text-gray-900 mb-6">Récapitulatif du produit</h3>
            <div class="flex items-center gap-5">
              <div class="w-20 h-20 rounded-2xl bg-[#F5F0E8] flex items-center justify-center text-3xl flex-shrink-0 overflow-hidden">
                <img v-if="produit.photo" :src="`http://127.0.0.1:8000/storage/products/${produit.photo}`" :alt="produit.nom" class="w-full h-full object-cover">
                <span v-else class="opacity-30">📦</span>
              </div>
              <div>
                <p class="text-sm font-black text-gray-900">{{ produit.nom }}</p>
                <p class="text-xs text-gray-400 font-medium">{{ produit.sous_categorie }}</p>
                <p class="text-sm font-black text-[#2D5A27] mt-1">{{ formatPrice(produit.prix) }} <span class="text-[10px] font-medium text-gray-400">/ unité</span></p>
              </div>
            </div>
          </div>

          <div class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-lg font-black text-gray-900 mb-6">Quantité</h3>
            <div class="flex items-center gap-4">
              <button @click="decrementerQuantite" class="w-12 h-12 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-lg transition-all">−</button>
              <input v-model.number="form.quantite" type="number" readonly
                class="w-20 text-center py-3 bg-[#F5F0E8]/50 border-2 border-gray-200 rounded-2xl font-black text-gray-900 text-lg outline-none">
              <button @click="incrementerQuantite" class="w-12 h-12 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-lg transition-all">+</button>
              <span class="text-xs text-gray-400 font-medium">Stock : {{ produit.quantite }} unité{{ produit.quantite > 1 ? 's' : '' }}</span>
            </div>
            <p v-if="stockError" class="text-red-500 text-xs font-bold mt-3">{{ stockError }}</p>
          </div>

          <div class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-lg font-black text-gray-900 mb-6">Mode de livraison</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <button type="button" @click="form.mode_livraison = 'Domicile'"
                :class="`p-5 rounded-2xl border-2 text-left transition-all ${form.mode_livraison === 'Domicile' ? 'border-[#2D5A27] bg-[#2D5A27]/5' : 'border-gray-100 bg-gray-50 hover:border-gray-200'}`">
                <div class="w-10 h-10 rounded-xl bg-[#2D5A27]/10 flex items-center justify-center text-xl mb-3">🏠</div>
                <p class="text-sm font-black text-gray-900">Livraison à domicile</p>
                <p class="text-xs text-gray-400 font-medium mt-1">Recommandé pour les grandes quantités</p>
              </button>
              <button type="button" @click="form.mode_livraison = 'Retrait'"
                :class="`p-5 rounded-2xl border-2 text-left transition-all ${form.mode_livraison === 'Retrait' ? 'border-[#2D5A27] bg-[#2D5A27]/5' : 'border-gray-100 bg-gray-50 hover:border-gray-200'}`">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-xl mb-3">📦</div>
                <p class="text-sm font-black text-gray-900">Retrait sur place</p>
                <p class="text-xs text-gray-400 font-medium mt-1">Disponible chez le producteur</p>
              </button>
            </div>
            <div v-if="form.mode_livraison === 'Domicile'" class="mt-5">
              <label class="text-xs font-black text-gray-400 uppercase tracking-widest block mb-2">Adresse de livraison</label>
              <input v-model="form.adresse_livraison" type="text" placeholder="Entrez votre adresse complète"
                class="w-full px-5 py-3 bg-[#F5F0E8]/50 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-medium text-gray-700">
            </div>
          </div>

          <div class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-lg font-black text-gray-900 mb-6">Méthode de paiement</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <button type="button" @click="form.methode_paiement = 'Wave'"
                :class="`p-5 rounded-2xl border-2 text-left transition-all ${form.methode_paiement === 'Wave' ? 'border-[#2D5A27] bg-[#2D5A27]/5' : 'border-gray-100 bg-gray-50 hover:border-gray-200'}`">
                <div class="flex items-center gap-3 mb-2">
                  <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-lg font-black text-blue-600">W</div>
                  <div>
                    <p class="text-sm font-black text-gray-900">Wave</p>
                    <p class="text-xs text-gray-400 font-medium">Paiement mobile</p>
                  </div>
                </div>
              </button>
              <button type="button" @click="form.methode_paiement = 'OrangeMoney'"
                :class="`p-5 rounded-2xl border-2 text-left transition-all ${form.methode_paiement === 'OrangeMoney' ? 'border-[#2D5A27] bg-[#2D5A27]/5' : 'border-gray-100 bg-gray-50 hover:border-gray-200'}`">
                <div class="flex items-center gap-3 mb-2">
                  <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-lg font-black text-orange-600">O</div>
                  <div>
                    <p class="text-sm font-black text-gray-900">Orange Money</p>
                    <p class="text-xs text-gray-400 font-medium">Paiement mobile</p>
                  </div>
                </div>
              </button>
            </div>
          </div>
        </div>

        <div class="lg:col-span-2">
          <div class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100 sticky top-28">
            <h3 class="text-lg font-black text-gray-900 mb-6">Récapitulatif</h3>
            <div class="space-y-4 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-500 font-medium">Produit</span>
                <span class="font-bold text-gray-900 text-right max-w-[50%]">{{ produit.nom }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500 font-medium">Quantité</span>
                <span class="font-bold text-gray-900">{{ form.quantite }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500 font-medium">Prix unitaire</span>
                <span class="font-bold text-gray-900">{{ formatPrice(produit.prix) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500 font-medium">Livraison</span>
                <span class="font-bold text-gray-900">{{ form.mode_livraison === 'Retrait' ? 'Gratuite' : (form.adresse_livraison ? 'Incluse' : '—') }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500 font-medium">Paiement</span>
                <span class="font-bold text-gray-900">{{ form.methode_paiement || '—' }}</span>
              </div>
              <div class="border-t border-gray-100 pt-4 flex justify-between">
                <span class="font-black text-gray-900">Total estimé</span>
                <span class="font-black text-[#2D5A27] text-xl">{{ formatPrice(produit.prix * form.quantite) }}</span>
              </div>
            </div>

            <p v-if="formError" class="text-red-500 text-xs font-bold mt-4">{{ formError }}</p>

            <button @click="handleSubmit" :disabled="submitting"
              class="w-full mt-6 py-4 bg-[#2D5A27] text-white rounded-2xl font-black text-base shadow-xl shadow-[#2D5A27]/20 hover:-translate-y-0.5 transition-all active:scale-[0.98] disabled:opacity-50 disabled:translate-y-0 flex items-center justify-center gap-3">
              <span>{{ submitting ? 'Commande en cours...' : 'Confirmer la commande' }}</span>
              <svg v-if="!submitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <svg v-else class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const route = useRoute()

const loading = ref(true)
const error = ref('')
const submitting = ref(false)
const formError = ref('')
const stockError = ref('')

const produit = ref(null)
const form = reactive({
  quantite: 1,
  mode_livraison: 'Domicile',
  adresse_livraison: '',
  methode_paiement: 'Wave',
})

let token = ''
let user = {}

onMounted(async () => {
  token = localStorage.getItem('token') || ''
  const userStr = localStorage.getItem('user')
  if (userStr) user = JSON.parse(userStr)

  if (!token || !user) {
    error.value = 'Veuillez vous connecter pour commander.'
    loading.value = false
    return
  }

  const produitId = route.query.produit_id
  const quantite = parseInt(route.query.quantite) || 1

  if (!produitId) {
    loading.value = false
    return
  }

  try {
    const raw = await $fetch(`http://127.0.0.1:8000/api/produits/${produitId}`)
    produit.value = {
      id: raw.id,
      nom: raw.nom,
      description: raw.description,
      prix: raw.prix,
      photo: raw.photo,
      quantite: raw.quantite,
      sous_categorie: raw.sous_categorie?.nom || '',
      producteur_id: raw.producteur_id,
      proprietaire_type: raw.proprietaire_type,
      proprietaire_id: raw.proprietaire_id,
    }
    if (quantite > 0 && quantite <= raw.quantite) {
      form.quantite = quantite
    }
    if (user.adresse) {
      form.adresse_livraison = user.adresse
    }
  } catch (e) {
    error.value = 'Impossible de charger les informations du produit.'
  } finally {
    loading.value = false
  }
})

function decrementerQuantite() {
  if (form.quantite > 1) form.quantite--
}

function incrementerQuantite() {
  if (produit.value && form.quantite < produit.value.quantite) form.quantite++
}

watch(() => form.quantite, (val) => {
  if (produit.value && val > produit.value.quantite) {
    stockError.value = `Stock insuffisant. Maximum : ${produit.value.quantite}`
  } else {
    stockError.value = ''
  }
})

async function handleSubmit() {
  formError.value = ''

  if (!form.mode_livraison) { formError.value = 'Choisissez un mode de livraison.'; return }
  if (form.mode_livraison === 'Domicile' && !form.adresse_livraison.trim()) { formError.value = 'Veuillez renseigner votre adresse de livraison.'; return }
  if (!form.methode_paiement) { formError.value = 'Choisissez une méthode de paiement.'; return }
  if (form.quantite < 1) { formError.value = 'Quantité invalide.'; return }
  if (produit.value && form.quantite > produit.value.quantite) { formError.value = 'Stock insuffisant.'; return }

  submitting.value = true

  try {
    const response = await $fetch('http://127.0.0.1:8000/api/consommateur/commandes', {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` },
      body: {
        vendeur_id: produit.value.producteur_id ?? (produit.value.proprietaire_type === 'distributeur' ? produit.value.proprietaire_id : null),
        items: [{ produit_id: produit.value.id, quantite: form.quantite }],
        mode_livraison: form.mode_livraison === 'Domicile' ? 'Domicile' : 'Retrait',
        adresse_livraison: form.mode_livraison === 'Domicile' ? form.adresse_livraison : '',
      }
    })

    const commandeId = response.commande?.id || response.commande_id

    navigateTo(`/consommateur/commandes/${commandeId}`)
  } catch (e) {
    if (e.data?.message) {
      formError.value = e.data.message
    } else if (e.data?.errors) {
      formError.value = Object.values(e.data.errors).flat().join(', ')
    } else {
      formError.value = 'Erreur lors de la création de la commande.'
    }
  } finally {
    submitting.value = false
  }
}

function formatPrice(price) {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}
</script>
