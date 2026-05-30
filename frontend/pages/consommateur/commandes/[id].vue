<template>
  <div class="animate-in slide-in-from-bottom duration-700">
    <div v-if="pending" class="flex items-center justify-center h-96">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#2D5A27]"></div>
    </div>
    <div v-else-if="order" class="max-w-5xl mx-auto space-y-8">
      <div class="flex items-center justify-between">
        <NuxtLink to="/consommateur/commandes" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#2D5A27] transition-colors group">
          <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
          Mes commandes
        </NuxtLink>
        <div class="flex gap-3">
          <button v-if="showPayer" @click="modalPaiement.open = true"
            class="px-5 py-2.5 bg-[#2D5A27] text-white rounded-xl text-xs font-black hover:bg-[#236B32] transition-all active:scale-95">Payer</button>
          <button v-if="order.status === 'EnCours'" @click="handleCancel" :disabled="cancelling"
            class="px-5 py-2.5 bg-red-50 text-red-600 rounded-xl text-xs font-black hover:bg-red-100 transition-all active:scale-95 disabled:opacity-50">
            {{ cancelling ? '...' : 'Annuler' }}</button>
          <button v-if="order.status === 'Valider' && order.livraison" @click="handleConfirmReception" :disabled="confirming"
            class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-black hover:bg-emerald-700 transition-all active:scale-95 disabled:opacity-50">
            {{ confirming ? '...' : 'Confirmer réception' }}</button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
          <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-8">
              <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Commande</p>
                <h1 class="text-3xl font-black text-gray-900">#{{ order.id }}</h1>
              </div>
              <div :class="`px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest ${statusStyle(order.status)}`">{{ order.status }}</div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
              <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Date</p>
                <p class="text-sm font-black text-gray-800">{{ formatDate(order.date_commande) }}</p>
              </div>
              <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Articles</p>
                <p class="text-sm font-black text-gray-800">{{ order.lignes_commande?.length || 0 }}</p>
              </div>
              <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Livraison</p>
                <p class="text-sm font-black text-gray-800">{{ order.mode_livraison || 'Standard' }}</p>
              </div>
              <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total</p>
                <p class="text-sm font-black text-[#2D5A27]">{{ formatPrice(order.montant_total) }}</p>
              </div>
            </div>
            <div v-if="order.paiement" class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-2 gap-6">
              <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Méthode de paiement</p>
                <p class="text-sm font-black text-gray-800">{{ order.paiement.methode_paiement || '—' }}</p>
              </div>
              <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Date de paiement</p>
                <p class="text-sm font-black text-gray-800">{{ formatDate(order.paiement.date_transaction) }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50"><h3 class="text-lg font-black text-gray-900">Détail des articles</h3></div>
            <div class="divide-y divide-gray-50">
              <div v-for="ligne in order.lignes_commande" :key="ligne.id" class="p-6 md:p-8 flex items-center justify-between group hover:bg-[#F5F0E8]/30 transition-colors">
                <div class="flex items-center gap-4">
                  <div class="w-14 h-14 rounded-xl bg-[#F5F0E8] flex items-center justify-center text-xl text-[#2D5A27] font-black">{{ ligne.produit?.nom?.[0] || 'P' }}</div>
                  <div>
                    <p class="text-sm font-black text-gray-900">{{ ligne.produit?.nom }}</p>
                    <p class="text-xs font-medium text-gray-400">{{ ligne.quantite }} x {{ formatPrice(ligne.prix_unitaire) }}</p>
                  </div>
                </div>
                <p class="text-sm font-black text-gray-900">{{ formatPrice(ligne.prix_total) }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-6">
          <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-lg font-black text-gray-900 mb-6">Récapitulatif</h3>
            <div class="space-y-3">
              <div class="flex justify-between text-sm"><span class="text-gray-500 font-medium">Sous-total</span><span class="font-bold text-gray-900">{{ formatPrice(order.montant_total) }}</span></div>
              <div class="flex justify-between text-sm"><span class="text-gray-500 font-medium">Livraison</span><span class="font-bold text-gray-900">Gratuite</span></div>
              <div class="border-t border-gray-100 pt-3 flex justify-between"><span class="font-black text-gray-900">Total</span><span class="font-black text-[#2D5A27]">{{ formatPrice(order.montant_total) }}</span></div>
            </div>
          </div>

          <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-lg font-black text-gray-900 mb-6">Adresse de livraison</h3>
            <p class="text-sm font-medium text-gray-700 leading-relaxed">{{ order.adresse_livraison || order.acheteur?.adresse || 'Non spécifiée' }}</p>
          </div>

          <div v-if="order.livraison" class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-lg font-black text-gray-900 mb-6">Livraison associée</h3>
            <div class="flex items-center justify-between mb-4">
              <span class="text-sm font-bold text-gray-700">#{{ order.livraison.id }}</span>
              <span :class="`px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider ${statusStyle(order.livraison.status)}`">{{ order.livraison.status }}</span>
            </div>
            <NuxtLink :to="`/consommateur/livraisons/${order.livraison.id}`" class="block w-full py-3 text-center bg-[#F5F0E8] text-[#2D5A27] rounded-xl text-xs font-black hover:bg-[#2D5A27] hover:text-white transition-all">Voir le suivi</NuxtLink>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Paiement -->
    <Teleport to="body">
      <div v-if="modalPaiement.open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="modalPaiement.open = false">
        <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 w-full max-w-lg mx-4 animate-in fade-in zoom-in-95 duration-200">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-black font-serif text-gray-900">Paiement</h3>
            <button @click="modalPaiement.open = false" class="p-2 bg-gray-50 text-gray-400 hover:text-gray-600 rounded-xl transition-all">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>

          <!-- Récapitulatif de la commande -->
          <div class="bg-[#F5F0E8] rounded-2xl p-5 mb-6 space-y-2">
            <div class="flex justify-between text-sm" v-for="l in order?.lignes_commande" :key="l.id">
              <span class="text-gray-700 font-medium">{{ l.produit?.nom }} <span class="text-gray-400">×{{ l.quantite }}</span></span>
              <span class="font-bold text-gray-900">{{ formatPrice(l.prix_total) }}</span>
            </div>
            <div class="border-t border-gray-300 pt-2 flex justify-between">
              <span class="font-black text-gray-900">Total</span>
              <span class="font-black text-[#2D5A27] text-lg">{{ formatPrice(order?.montant_total) }}</span>
            </div>
          </div>

          <!-- Choix du mode de paiement -->
          <p class="text-xs font-black text-gray-400 uppercase tracking-wider mb-3">Mode de paiement</p>
          <div class="grid grid-cols-2 gap-3 mb-6">
            <button
              @click="modalPaiement.methode = 'Wave'"
              :class="`p-4 rounded-2xl border-2 text-center transition-all ${modalPaiement.methode === 'Wave' ? 'border-[#2D5A27] bg-[#2D5A27]/5' : 'border-gray-100 bg-gray-50 hover:border-gray-200'}`">
              <div class="w-10 h-10 rounded-xl bg-[#2D5A27]/10 flex items-center justify-center mx-auto mb-2 text-lg">🌊</div>
              <p class="text-sm font-black text-gray-900">Wave</p>
            </button>
            <button
              @click="modalPaiement.methode = 'OrangeMoney'"
              :class="`p-4 rounded-2xl border-2 text-center transition-all ${modalPaiement.methode === 'OrangeMoney' ? 'border-[#2D5A27] bg-[#2D5A27]/5' : 'border-gray-100 bg-gray-50 hover:border-gray-200'}`">
              <div class="w-10 h-10 rounded-xl bg-[#2D5A27]/10 flex items-center justify-center mx-auto mb-2 text-lg">📱</div>
              <p class="text-sm font-black text-gray-900">Orange Money</p>
            </button>
          </div>

          <div v-if="modalPaiement.error" class="text-red-500 text-xs font-bold mb-4 text-center">{{ modalPaiement.error }}</div>

          <div class="flex gap-3">
            <button @click="confirmerPaiement"
              :disabled="modalPaiement.loading"
              class="flex-1 px-6 py-3.5 bg-[#2D5A27] text-white rounded-2xl font-black text-sm hover:bg-[#1E3F1A] transition-all shadow-lg disabled:opacity-50 flex items-center justify-center gap-2">
              <svg v-if="modalPaiement.loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ modalPaiement.loading ? 'Traitement...' : `Payer ${formatPrice(order?.montant_total)}` }}
            </button>
            <button @click="modalPaiement.open = false"
              class="px-6 py-3.5 bg-gray-100 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-200 transition-all">
              Annuler
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

import { ref, reactive, computed } from 'vue'

const route = useRoute()
const cancelling = ref(false)
const confirming = ref(false)

const { data: order, pending, refresh } = await useFetch(`http://127.0.0.1:8000/api/consommateur/commandes/${route.params.id}`, {
  headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
})

const modalPaiement = reactive({
  open: false,
  methode: 'Wave',
  loading: false,
  error: ''
})

const showPayer = computed(() => {
  if (!order.value) return false
  if (order.value.paiement && order.value.paiement.status === 'Valider') return false
  return order.value.status === 'Valider'
})

async function handleCancel() {
  if (!confirm('Annuler cette commande ?')) return
  cancelling.value = true
  try {
    await $fetch(`http://127.0.0.1:8000/api/consommateur/commandes/${route.params.id}/annuler`, { method: 'PUT', headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } })
    refresh()
  } catch (e) { alert("Erreur lors de l'annulation") }
  finally { cancelling.value = false }
}

async function handleConfirmReception() {
  if (!confirm('Confirmer la réception de cette commande ?')) return
  confirming.value = true
  try {
    await $fetch(`http://127.0.0.1:8000/api/consommateur/commandes/${route.params.id}/confirmer-reception`, { method: 'PUT', headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } })
    refresh()
  } catch (e) { alert("Erreur lors de la confirmation") }
  finally { confirming.value = false }
}

async function confirmerPaiement() {
  modalPaiement.error = ''
  modalPaiement.loading = true
  try {
    const res = await $fetch('http://127.0.0.1:8000/api/consommateur/paiements', {
      method: 'POST',
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
      body: { commande_id: route.params.id, methode_paiement: modalPaiement.methode }
    })
    if (res.redirect_url) {
      modalPaiement.open = false
      if (res.redirect_url.startsWith('/')) {
        await navigateTo(res.redirect_url)
      } else {
        window.location.href = res.redirect_url
      }
    } else {
      modalPaiement.open = false
      refresh()
    }
  } catch (e) {
    modalPaiement.error = e.data?.message || 'Erreur lors du paiement'
  } finally {
    modalPaiement.loading = false
  }
}

function statusStyle(status) {
  switch (status) {
    case 'Valider': return 'bg-emerald-100 text-emerald-700'
    case 'Annuler': return 'bg-red-100 text-red-700'
    case 'EnCours': return 'bg-amber-100 text-amber-700'
    case 'Livrer': return 'bg-blue-100 text-blue-700'
    case 'Livrée': return 'bg-emerald-100 text-emerald-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

function formatDate(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
}

function formatPrice(price) {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}
</script>
