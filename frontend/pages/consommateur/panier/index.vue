<template>
  <div class="max-w-6xl mx-auto pb-24 lg:pb-8 animate-in fade-in duration-500">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2.5 text-sm mb-6">
      <NuxtLink to="/catalogue" class="text-gray-400 hover:text-[#2D5A27] font-medium transition-colors">Catalogue</NuxtLink>
      <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      <span class="text-[#2D5A27] font-bold">Panier</span>
      <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      <span class="text-gray-400 font-medium">Commande</span>
    </div>

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl md:text-4xl font-black text-gray-900 font-serif">Mon Panier</h1>
        <p class="text-gray-500 font-medium mt-2 flex items-center gap-2">
          <span class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5 rounded-full bg-[#2D5A27] text-white text-[11px] font-black">{{ store.count }}</span>
          <span>{{ store.count }} article{{ store.count > 1 ? 's' : '' }} dans votre panier</span>
        </p>
      </div>
      <NuxtLink to="/catalogue"
        class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 bg-[#F5F0E8] text-[#2D5A27] rounded-xl text-xs font-bold hover:bg-[#2D5A27] hover:text-white transition-all duration-300">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Continuer mes achats
      </NuxtLink>
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 space-y-4">
        <div v-for="i in 3" :key="i" class="h-28 bg-white rounded-2xl animate-pulse shadow-sm border border-gray-100"/>
      </div>
      <div class="hidden lg:block">
        <div class="h-96 bg-white rounded-2xl animate-pulse shadow-sm border border-gray-100"/>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else-if="store.items.length === 0 && !commandeSuccess" class="text-center py-20 max-w-md mx-auto">
      <svg class="w-28 h-28 mx-auto text-gray-300 mb-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
      </svg>
      <h3 class="text-2xl font-black text-gray-900 font-serif mb-3">Votre panier est vide</h3>
      <p class="text-gray-500 font-medium leading-relaxed mb-8">Découvrez nos produits frais du secteur primaire sénégalais et ajoutez vos articles préférés.</p>
      <NuxtLink to="/catalogue"
        class="inline-flex items-center gap-2 px-8 py-3.5 bg-[#2D5A27] text-white rounded-2xl font-bold text-sm shadow-xl shadow-[#2D5A27]/20 hover:bg-[#1e3d1a] hover:-translate-y-0.5 transition-all duration-300">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        Voir le catalogue
      </NuxtLink>
    </div>

    <!-- Main content (items present) -->
    <div v-else-if="store.items.length > 0" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

      <!-- ====== LEFT COLUMN: Items list ====== -->
      <div class="lg:col-span-2 space-y-6">

        <!-- Success banner -->
        <transition name="banner">
          <div v-if="commandeSuccess"
            class="bg-emerald-50 border border-emerald-200 rounded-[2rem] p-6 text-center animate-in slide-in-from-top-2 duration-500">
            <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
              <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 class="text-lg font-black text-gray-900 mb-1">Commande{{ commandesCrees.length > 1 ? 's' : '' }} passée{{ commandesCrees.length > 1 ? 's' : '' }} avec succès !</h3>
            <p class="text-sm text-gray-500 mb-4">{{ commandesCrees.length }} commande{{ commandesCrees.length > 1 ? 's' : '' }} créée{{ commandesCrees.length > 1 ? 's' : '' }}</p>
            <div class="flex flex-wrap gap-3 justify-center">
              <NuxtLink v-for="c in commandesCrees" :key="c.id"
                :to="`/${userRole}/commandes/${c.id}`"
                class="px-5 py-2.5 bg-[#2D5A27] text-white rounded-xl text-xs font-bold hover:bg-[#1e3d1a] transition-all">
                Voir commande #{{ c.id }}
              </NuxtLink>
              <button @click="resetCommande"
                class="px-5 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-xs font-bold hover:bg-gray-200 transition-all">
                Nouvelle commande
              </button>
            </div>
          </div>
        </transition>

        <!-- Items grouped by seller -->
        <div v-for="(group, vendeurId) in itemsBySeller" :key="vendeurId">
          <!-- Seller header -->
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-[#2D5A27]/10 flex items-center justify-center text-sm font-bold text-[#2D5A27]">
              {{ group.vendeur_nom?.[0] || 'V' }}
            </div>
            <div>
              <p class="text-sm font-bold text-gray-900">{{ group.vendeur_nom || 'Vendeur' }}</p>
              <p class="text-[11px] text-gray-400 font-medium">{{ group.items.length }} article{{ group.items.length > 1 ? 's' : '' }}</p>
            </div>
          </div>

          <!-- Product cards -->
          <div class="space-y-4">
            <div v-for="item in group.items" :key="item.id"
              :class="[
                'relative bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-4 transition-all duration-500',
                removingId === item.id ? 'opacity-0 translate-y-3 scale-[0.98]' : 'hover:shadow-md hover:-translate-y-0.5'
              ]">

              <!-- Delete button -->
              <button @click="supprimer(item.id)"
                class="absolute -top-2 -right-2 w-7 h-7 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-300 hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-all duration-300 shadow-sm z-10">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>

              <!-- Photo -->
              <div class="w-[88px] h-[88px] rounded-xl bg-[#EFF7EC] overflow-hidden flex-shrink-0">
                <img v-if="item.photo" :src="`http://127.0.0.1:8000/storage/products/${item.photo}`" :alt="item.nom"
                  class="w-full h-full object-cover">
                <div v-else class="w-full h-full flex items-center justify-center">
                  <svg class="w-8 h-8 text-[#2D5A27]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L12 12m0 0l2.586-2.586a2 2 0 012.828 0L20 14m0 0V6a2 2 0 00-2-2H6a2 2 0 00-2 2v8m16 0v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2"/>
                  </svg>
                </div>
              </div>

              <!-- Info -->
              <div class="flex-1 min-w-0">
                <h4 class="text-[15px] font-bold text-gray-900 font-serif leading-tight truncate">{{ item.nom }}</h4>
                <p class="text-xs text-gray-400 mt-1 font-medium">{{ item.vendeur_nom }}</p>
                <p class="text-sm font-black text-[#2D5A27] mt-2">{{ formatPrice(item.prix) }} <span class="text-[10px] font-medium text-gray-400">/ u</span></p>
              </div>

              <!-- Quantity controls -->
              <div class="flex flex-col items-end gap-3 flex-shrink-0">
                <div class="flex items-center gap-0">
                  <button @click="decrementer(item)"
                    class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:border-[#2D5A27] hover:text-[#2D5A27] hover:bg-[#2D5A27]/5 transition-all text-lg font-medium">
                    −
                  </button>
                  <span class="w-10 text-center font-black text-gray-900 text-sm select-none">{{ item.quantite }}</span>
                  <button @click="incrementer(item)"
                    :disabled="item.quantite >= item.quantite_stock"
                    class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:border-[#2D5A27] hover:text-[#2D5A27] hover:bg-[#2D5A27]/5 transition-all text-lg font-medium disabled:opacity-30 disabled:cursor-not-allowed">
                    +
                  </button>
                </div>
                <p class="text-sm font-bold text-[#8B7340]">{{ formatPrice(item.prix * item.quantite) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile: Continue shopping link -->
        <NuxtLink to="/catalogue"
          class="flex sm:hidden items-center justify-center gap-2 py-3 text-sm font-bold text-[#2D5A27] hover:underline">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Continuer mes achats
        </NuxtLink>
      </div>

      <!-- ====== RIGHT COLUMN: Summary (Desktop) ====== -->
      <div class="hidden lg:block lg:col-span-1">
        <div class="sticky top-24 bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 transition-all duration-300 hover:shadow-md">
          <h3 class="text-lg font-bold text-gray-900 font-serif mb-6">Récapitulatif</h3>

          <!-- Order summary -->
          <div class="space-y-3 pb-4 border-b border-gray-100">
            <div class="flex justify-between text-sm">
              <span class="text-gray-500">{{ store.count }} article{{ store.count > 1 ? 's' : '' }}</span>
              <span class="font-bold text-gray-900">{{ formatPrice(store.total) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-500">Livraison</span>
              <span class="text-sm font-medium text-gray-400">{{ fraisLivraison }}</span>
            </div>
          </div>

          <div class="pt-4 mb-6">
            <div class="flex justify-between items-end">
              <span class="text-sm font-bold text-gray-700">Total</span>
              <span class="text-2xl font-black text-[#2D5A27] font-serif">{{ formatPrice(store.total) }}</span>
            </div>
          </div>

          <!-- Delivery mode -->
          <div class="mb-5">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Mode de livraison</p>
            <div class="space-y-2.5">
              <button @click="modeLivraison = 'Domicile'"
                :class="[
                  'w-full flex items-center gap-3 p-3.5 rounded-2xl border-2 transition-all duration-300 text-sm font-bold text-left',
                  modeLivraison === 'Domicile'
                    ? 'border-[#2D5A27] bg-[#2D5A27]/5 text-[#2D5A27]'
                    : 'border-gray-100 text-gray-500 hover:border-gray-200 hover:bg-gray-50'
                ]">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Livraison à domicile
              </button>
              <button @click="modeLivraison = 'Retrait'"
                :class="[
                  'w-full flex items-center gap-3 p-3.5 rounded-2xl border-2 transition-all duration-300 text-sm font-bold text-left',
                  modeLivraison === 'Retrait'
                    ? 'border-[#2D5A27] bg-[#2D5A27]/5 text-[#2D5A27]'
                    : 'border-gray-100 text-gray-500 hover:border-gray-200 hover:bg-gray-50'
                ]">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Retrait sur place
              </button>
            </div>
          </div>

          <!-- Address (slide transition) -->
          <transition name="address">
            <div v-if="modeLivraison === 'Domicile'" class="mb-5">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Adresse de livraison</label>
              <textarea v-model="adresseLivraison" rows="2" required
                class="mt-2 block w-full px-4 py-3 border border-gray-200 rounded-2xl text-sm font-medium text-gray-700 focus:border-[#2D5A27] focus:ring-2 focus:ring-[#2D5A27]/10 outline-none resize-none transition-all"
                placeholder="Entrez votre adresse de livraison..."/>
              <p v-if="erreurAdresse" class="text-xs text-red-500 mt-1.5 font-medium">{{ erreurAdresse }}</p>
            </div>
          </transition>

          <!-- Payment method (distributeur only) -->
          <div v-if="userRole === 'distributeur'" class="mb-5">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Méthode de paiement</p>
            <div class="space-y-2.5">
              <button @click="methodePaiement = 'Wave'"
                :class="[
                  'w-full flex items-center gap-3 p-3.5 rounded-2xl border-2 transition-all duration-300 text-sm font-bold text-left',
                  methodePaiement === 'Wave'
                    ? 'border-[#00AEEF] bg-[#00AEEF]/5 text-[#00AEEF]'
                    : 'border-gray-100 text-gray-500 hover:border-gray-200 hover:bg-gray-50'
                ]">
                <div class="w-7 h-7 rounded-lg bg-[#00AEEF] flex items-center justify-center flex-shrink-0">
                  <span class="text-white text-[10px] font-black">W</span>
                </div>
                Wave
              </button>
              <button @click="methodePaiement = 'OrangeMoney'"
                :class="[
                  'w-full flex items-center gap-3 p-3.5 rounded-2xl border-2 transition-all duration-300 text-sm font-bold text-left',
                  methodePaiement === 'OrangeMoney'
                    ? 'border-[#FF7900] bg-[#FF7900]/5 text-[#FF7900]'
                    : 'border-gray-100 text-gray-500 hover:border-gray-200 hover:bg-gray-50'
                ]">
                <div class="w-7 h-7 rounded-lg bg-[#FF7900] flex items-center justify-center flex-shrink-0">
                  <span class="text-white text-[8px] font-black">OM</span>
                </div>
                Orange Money
              </button>
            </div>
          </div>

          <!-- Confirm button -->
          <button @click="passerCommande" :disabled="submitting || boutonDisabled"
            class="w-full py-4 bg-[#2D5A27] text-white rounded-2xl font-bold text-sm shadow-lg shadow-[#2D5A27]/20 hover:bg-[#1e3d1a] hover:-translate-y-0.5 active:scale-[0.98] transition-all duration-300 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:bg-[#2D5A27] flex items-center justify-center gap-3">
            <svg v-if="submitting" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
            </svg>
            <span>{{ submitting ? 'Traitement...' : 'Confirmer la commande' }}</span>
          </button>

          <p v-if="userRole !== 'distributeur'" class="text-[10px] text-gray-400 text-center mt-3 font-medium">
            Paiement à la livraison ou via votre espace paiement
          </p>
        </div>
      </div>
    </div>

    <!-- ====== MOBILE STICKY BOTTOM ====== -->
    <transition name="mobile-summary">
      <div v-if="store.items.length > 0 && !commandeSuccess"
        class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 px-4 py-3 lg:hidden shadow-2xl">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-bold text-gray-700">Total</span>
          <span class="text-xl font-black text-[#2D5A27] font-serif">{{ formatPrice(store.total) }}</span>
        </div>
        <button @click="passerCommande" :disabled="submitting || boutonDisabled"
          class="w-full py-3.5 bg-[#2D5A27] text-white rounded-2xl font-bold text-sm shadow-lg flex items-center justify-center gap-2 transition-all duration-300 disabled:opacity-40 disabled:cursor-not-allowed active:scale-[0.98]">
          <svg v-if="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
          </svg>
          <span>{{ submitting ? 'Traitement...' : `Confirmer (${formatPrice(store.total)})` }}</span>
        </button>
      </div>
    </transition>

    <!-- Toast notification -->
    <transition name="toast">
      <div v-if="toast.show"
        :class="[
          'fixed bottom-6 right-6 z-50 px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold flex items-center gap-3 min-w-[280px]',
          toast.type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'
        ]">
        <svg v-if="toast.type === 'success'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        <svg v-else class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <span>{{ toast.message }}</span>
      </div>
    </transition>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const store = useCartStore()
const loading = ref(true)
const submitting = ref(false)
const commandeSuccess = ref(false)
const commandesCrees = ref([])
const userRole = ref(null)
const modeLivraison = ref('Domicile')
const adresseLivraison = ref('')
const methodePaiement = ref('Wave')
const erreurAdresse = ref('')
const removingId = ref(null)

const toast = reactive({ show: false, message: '', type: 'success' })
let toastTimer = null

const itemsBySeller = computed(() => {
  const groups = {}
  for (const item of store.items) {
    const key = item.vendeur_id
    if (!groups[key]) {
      groups[key] = { vendeur_id: key, vendeur_nom: item.vendeur_nom || '', items: [] }
    }
    groups[key].items.push(item)
  }
  return groups
})

const fraisLivraison = computed(() => {
  return modeLivraison.value === 'Domicile' ? 'Gratuit' : '—'
})

const boutonDisabled = computed(() => {
  return store.items.length === 0 ||
    (userRole.value === 'distributeur' && !methodePaiement.value)
})

function showToast(message, type = 'success') {
  toast.show = true
  toast.message = message
  toast.type = type
  if (toastTimer) clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toast.show = false }, 3000)
}

function decrementer(item) {
  if (item.quantite > 1) {
    store.modifierQuantite(item.id, item.quantite - 1)
  }
}

function incrementer(item) {
  if (item.quantite < item.quantite_stock) {
    store.modifierQuantite(item.id, item.quantite + 1)
  }
}

function supprimer(id) {
  removingId.value = id
  setTimeout(() => {
    store.retirer(id)
    removingId.value = null
    showToast('Produit retiré du panier')
  }, 400)
}

function resetCommande() {
  commandeSuccess.value = false
  commandesCrees.value = []
  store.vider()
}

async function passerCommande() {
  if (store.items.length === 0) return

  if (modeLivraison.value === 'Domicile' && !adresseLivraison.value.trim()) {
    erreurAdresse.value = "Veuillez saisir votre adresse de livraison."
    return
  }
  erreurAdresse.value = ''

  submitting.value = true
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      showToast('Veuillez vous connecter', 'error')
      return navigateTo('/login')
    }

    const body = {
      items: store.items.map(i => ({ produit_id: i.id, quantite: i.quantite })),
      mode_livraison: modeLivraison.value,
      adresse_livraison: modeLivraison.value === 'Domicile' ? adresseLivraison.value : null,
    }
    if (userRole.value === 'distributeur') {
      body.methode_paiement = methodePaiement.value
    }

    const res = await $fetch('http://127.0.0.1:8000/api/panier/checkout', {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` },
      body,
    })
    commandesCrees.value = res.commandes
    commandeSuccess.value = true
    showToast('Commande passée avec succès !')
    store.vider()
  } catch (err) {
    const msg = err.data?.message || 'Erreur lors de la commande.'
    showToast(msg, 'error')
  } finally {
    submitting.value = false
  }
}

function formatPrice(price) {
  if (!price && price !== 0) return '0 FCFA'
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' })
    .format(price).replace('XOF', 'FCFA')
}

onMounted(() => {
  const userStr = localStorage.getItem('user')
  if (userStr) {
    const user = JSON.parse(userStr)
    userRole.value = user.role
    if (user.adresse) {
      adresseLivraison.value = user.adresse
    }
  }
  loading.value = false
})
</script>

<style scoped>
.banner-enter-active, .banner-leave-active {
  transition: all 0.5s ease;
}
.banner-enter-from, .banner-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}

.address-enter-active, .address-leave-active {
  transition: all 0.3s ease;
}
.address-enter-from {
  opacity: 0;
  transform: translateY(-10px);
  max-height: 0;
}
.address-enter-to {
  opacity: 1;
  transform: translateY(0);
  max-height: 200px;
}
.address-leave-from {
  opacity: 1;
  transform: translateY(0);
  max-height: 200px;
}
.address-leave-to {
  opacity: 0;
  transform: translateY(-10px);
  max-height: 0;
}

.toast-enter-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from { opacity: 0; transform: translateX(40px) scale(0.95); }
.toast-leave-to { opacity: 0; transform: translateX(40px) scale(0.95); }

.mobile-summary-enter-active, .mobile-summary-leave-active {
  transition: all 0.3s ease;
}
.mobile-summary-enter-from, .mobile-summary-leave-to {
  transform: translateY(100%);
}
</style>
