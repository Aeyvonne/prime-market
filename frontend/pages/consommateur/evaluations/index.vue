<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <div>
      <h1 class="text-3xl font-black text-gray-900 font-serif">Mes <span class="text-[#2D5A27]">Évaluations</span></h1>
      <p class="text-gray-500 font-medium mt-1">Notez vos commandes reçues et partagez votre expérience.</p>
    </div>

    <!-- Commandes à évaluer -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
      <div class="px-6 md:px-8 pt-6 pb-3 border-b border-gray-50">
        <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider">Commandes à évaluer</h2>
      </div>

      <div v-if="commandesPending" class="p-12 space-y-6">
        <div v-for="i in 2" :key="i" class="h-24 bg-gray-50 animate-pulse rounded-2xl"></div>
      </div>

      <div v-else-if="commandes.length > 0">
        <div v-for="cmd in commandes" :key="cmd.id" class="p-6 md:p-8 border-b border-gray-50 last:border-b-0 hover:bg-[#F5F0E8]/30 transition-colors">
          <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-xl bg-[#2D5A27]/10 flex items-center justify-center text-[#2D5A27] text-sm font-black flex-shrink-0">#{{ cmd.id }}</div>
              <div>
                <p class="text-sm font-bold text-gray-900">{{ cmd.vendeur_prenom }} {{ cmd.vendeur_nom }}</p>
                <p class="text-xs text-gray-500">{{ cmd.produits.map(p => p.nom).join(', ') }}</p>
                <p class="text-[10px] text-gray-400 font-medium mt-1">{{ formatPrice(cmd.montant_total) }} · {{ formatDate(cmd.date_commande) }}</p>
              </div>
            </div>
            <div v-if="cmd.evaluation" class="flex flex-col items-end gap-1.5 flex-shrink-0">
              <div class="flex gap-0.5">
                <span v-for="s in 5" :key="s" :class="s <= cmd.evaluation.note ? 'text-amber-400' : 'text-gray-200'">★</span>
              </div>
              <span class="text-[10px] font-bold text-gray-400">Déjà évalué</span>
            </div>
            <button v-else @click="ouvrirModal(cmd)"
              class="px-5 py-2.5 bg-[#2D5A27] text-white rounded-xl text-xs font-bold hover:bg-[#236B32] transition-all active:scale-95 flex-shrink-0">
              Laisser un avis
            </button>
          </div>
          <p v-if="cmd.evaluation?.commentaire" class="mt-3 text-sm text-gray-600 italic bg-gray-50 rounded-2xl p-4">« {{ cmd.evaluation.commentaire }} »</p>
        </div>
      </div>

      <div v-else class="py-16 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <h3 class="text-lg font-black text-gray-900">Aucune commande à évaluer</h3>
        <p class="text-sm text-gray-500 mt-1">Vous n'avez pas encore reçu de commande.</p>
      </div>
    </div>

    <!-- Historique des évaluations -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
      <div class="px-6 md:px-8 pt-6 pb-3 border-b border-gray-50">
        <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider">Historique</h2>
      </div>

      <div v-if="pendingEvaluations" class="p-12 space-y-6">
        <div v-for="i in 2" :key="i" class="h-16 bg-gray-50 animate-pulse rounded-2xl"></div>
      </div>

      <div v-else-if="evaluations.length > 0">
        <div v-for="ev in evaluations" :key="ev.id" class="p-6 md:p-8 border-b border-gray-50 last:border-b-0 hover:bg-[#F5F0E8]/30 transition-colors">
          <div class="flex items-start justify-between mb-2">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-[#2D5A27] flex items-center justify-center text-white text-xs font-black">{{ ev.evalue?.nom?.[0] || 'D' }}</div>
              <div>
                <p class="text-sm font-bold text-gray-900">{{ ev.evalue?.nom || 'Distributeur' }}</p>
                <p class="text-[10px] text-gray-400 font-medium">{{ formatDate(ev.date || ev.created_at) }}</p>
              </div>
            </div>
            <div class="flex gap-0.5">
              <span v-for="s in 5" :key="s" :class="s <= ev.note ? 'text-amber-400' : 'text-gray-200'">★</span>
            </div>
          </div>
          <p v-if="ev.commentaire" class="text-sm text-gray-600 leading-relaxed">{{ ev.commentaire }}</p>
        </div>
      </div>

      <div v-else class="py-16 text-center">
        <div class="text-5xl mb-3 opacity-20">⭐</div>
        <h3 class="text-lg font-black text-gray-900">Aucune évaluation</h3>
        <p class="text-sm text-gray-500 mt-1">Vous n'avez encore évalué aucun distributeur.</p>
      </div>
    </div>

    <!-- Modal Laisser un avis -->
    <Teleport to="body">
      <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="showModal = false">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
          <div class="relative bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl p-8 transform transition-all">
            <button @click="showModal = false" class="absolute top-5 right-5 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-2xl font-black text-gray-900 mb-1">Laisser un avis</h3>
            <p class="text-sm text-gray-500 font-medium mb-6">
              Commande #{{ form.commande_id }} · {{ selectedVendeur }}
            </p>

            <form @submit.prevent="submitEvaluation" class="space-y-6">
              <div>
                <label class="text-xs font-black text-gray-400 uppercase tracking-widest block mb-3">Note</label>
                <div class="flex gap-1.5">
                  <button v-for="n in 5" :key="n" type="button" @click="form.note = n"
                    :class="`w-11 h-11 rounded-xl text-xl transition-all duration-200 ${
                      form.note >= n ? 'bg-amber-100 text-amber-500 scale-110' : 'bg-gray-100 text-gray-300 hover:bg-amber-50 hover:text-amber-300'
                    }`">★</button>
                </div>
              </div>
              <div>
                <label class="text-xs font-black text-gray-400 uppercase tracking-widest block mb-2">Commentaire <span class="font-normal lowercase opacity-60">(facultatif)</span></label>
                <textarea v-model="form.commentaire" rows="3" placeholder="Partagez votre expérience..."
                  class="w-full px-5 py-3 bg-[#F5F0E8]/50 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-medium text-gray-700 resize-none"></textarea>
              </div>
              <p v-if="formError" class="text-red-500 text-sm font-medium">{{ formError }}</p>
              <div class="flex gap-3 pt-2">
                <button type="submit" :disabled="submitting"
                  class="flex-1 py-3.5 bg-[#2D5A27] text-white rounded-xl font-bold text-sm hover:bg-[#236B32] transition-all active:scale-[0.98] disabled:opacity-50">{{ submitting ? 'Envoi...' : 'Publier l\'avis' }}</button>
                <button type="button" @click="showModal = false"
                  class="px-6 py-3.5 bg-gray-100 text-gray-500 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">Annuler</button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const showModal = ref(false)
const submitting = ref(false)
const formError = ref('')
const form = reactive({ evalue_id: null, commande_id: null, note: 0, commentaire: '' })
const selectedVendeur = ref('')

const token = process.client ? localStorage.getItem('token') : null

const { data: commandes, pending: commandesPending, refresh: refreshCommandes } = await useFetch(
  'http://127.0.0.1:8000/api/consommateur/commandes-evaluables',
  { headers: token ? { Authorization: `Bearer ${token}` } : {}, server: false }
)

const { data: evaluations, pending: pendingEvaluations, refresh: refreshEvaluations } = await useFetch(
  'http://127.0.0.1:8000/api/consommateur/evaluations',
  { headers: token ? { Authorization: `Bearer ${token}` } : {}, server: false }
)

function ouvrirModal(cmd) {
  form.evalue_id = cmd.vendeur_id
  form.commande_id = cmd.id
  form.note = 0
  form.commentaire = ''
  formError.value = ''
  selectedVendeur.value = `${cmd.vendeur_prenom} ${cmd.vendeur_nom}`
  showModal.value = true
}

async function submitEvaluation() {
  formError.value = ''
  if (!form.note) { formError.value = 'Veuillez sélectionner une note'; return }
  submitting.value = true
  try {
    await $fetch('http://127.0.0.1:8000/api/consommateur/evaluations', {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` },
      body: {
        evalue_id: form.evalue_id,
        commande_id: form.commande_id,
        note: form.note,
        commentaire: form.commentaire,
      },
    })
    form.evalue_id = null
    form.commande_id = null
    form.note = 0
    form.commentaire = ''
    showModal.value = false
    refreshCommandes()
    refreshEvaluations()
  } catch (e) {
    formError.value = e.data?.message || "Erreur lors de l'envoi"
  } finally {
    submitting.value = false
  }
}

function formatPrice(price) {
  if (!price) return '0 FCFA'
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}

function formatDate(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>
