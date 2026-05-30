<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- Header/Welcome Banner -->
    <div class="bg-gradient-to-br from-[#2D5A27] to-[#1E3F1A] rounded-[2.5rem] p-8 md:p-12 text-white relative overflow-hidden shadow-xl shadow-[#2D5A27]/10">
      <div class="absolute -right-24 -bottom-24 w-96 h-96 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
      <div class="absolute -left-12 -top-12 w-64 h-64 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
      
      <div class="relative z-10 max-w-3xl">
        <!-- Badge dynamique indiquant le secteur avec couleur correspondante -->
        <span 
          class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-4 inline-block border"
          :class="sectorBadgeStyle"
        >
          Secteur {{ currentSector }}
        </span>
        <h1 class="text-4xl md:text-5xl font-black font-serif leading-tight mb-4">
          Bonjour, <span class="text-[#F5F0E8]">{{ adminName }}</span> 👋
        </h1>
        <p class="text-white/80 text-base md:text-lg font-medium leading-relaxed">
          Bienvenue dans votre console de gestion sectorielle. Suivez les comptes producteurs, organisez les sous-catégories de votre secteur et supervisez les transactions en toute simplicité.
        </p>
      </div>
    </div>

    <!-- Quick Stats Cards (3 cartes stats demandées) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Card 1: Comptes actifs dans le secteur -->
      <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-500 group relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#F5F0E8] rounded-full opacity-0 group-hover:opacity-100 transition-all duration-700 scale-0 group-hover:scale-100"></div>
        <div class="relative z-10">
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-6 shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          </div>
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Comptes Actifs (Secteur)</p>
          <h3 class="text-3xl font-black text-gray-900 mb-4">{{ totalComptesActifs }}</h3>
          <NuxtLink to="/admin/sectoriel/comptes" class="text-xs font-bold text-[#2D5A27] hover:underline flex items-center gap-1 group-hover:translate-x-1 transition-transform">
            Gérer les comptes →
          </NuxtLink>
        </div>
      </div>

      <!-- Card 2: Transactions du mois -->
      <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-500 group relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#F5F0E8] rounded-full opacity-0 group-hover:opacity-100 transition-all duration-700 scale-0 group-hover:scale-100"></div>
        <div class="relative z-10">
          <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-6 shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Transactions du Mois (Sectoriel)</p>
          <h3 class="text-3xl font-black text-gray-900 mb-4">{{ formatPrice(volumeTransactionsMois) }}</h3>
          <NuxtLink to="/admin/sectoriel/transactions" class="text-xs font-bold text-[#2D5A27] hover:underline flex items-center gap-1 group-hover:translate-x-1 transition-transform">
            Superviser les ventes →
          </NuxtLink>
        </div>
      </div>

      <!-- Card 3: Sous-catégories gérées -->
      <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-500 group relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#F5F0E8] rounded-full opacity-0 group-hover:opacity-100 transition-all duration-700 scale-0 group-hover:scale-100"></div>
        <div class="relative z-10">
          <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-6 shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
          </div>
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Sous-Catégories Gérées</p>
          <h3 class="text-3xl font-black text-gray-900 mb-4">{{ totalCategories }}</h3>
          <NuxtLink to="/admin/sectoriel/sous-categories" class="text-xs font-bold text-[#2D5A27] hover:underline flex items-center gap-1 group-hover:translate-x-1 transition-transform">
            Gérer le catalogue →
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Tableau des dernières transactions du secteur -->
    <div class="bg-white rounded-[2.5rem] border border-gray-100 overflow-hidden flex flex-col justify-between shadow-sm">
      <div>
        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
          <h3 class="text-xl font-black text-gray-900 font-serif">Dernières Transactions du Secteur</h3>
          <NuxtLink to="/admin/sectoriel/transactions" class="text-xs font-bold text-[#2D5A27] hover:underline">Voir tout →</NuxtLink>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-55/50">
                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Acheteur</th>
                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Vendeur (Producteur)</th>
                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Montant</th>
                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Méthode</th>
                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="tx in recentTransactions" :key="tx.id" class="hover:bg-[#F5F0E8]/30 transition-colors">
                <td class="px-8 py-4">
                  <span class="text-sm font-bold text-gray-800">{{ tx.commande?.acheteur?.prenom }} {{ tx.commande?.acheteur?.nom }}</span>
                </td>
                <td class="px-8 py-4">
                  <span class="text-sm font-medium text-gray-600">{{ tx.commande?.vendeur?.prenom }} {{ tx.commande?.vendeur?.nom }}</span>
                </td>
                <td class="px-8 py-4">
                  <span class="text-sm font-black text-gray-900">{{ formatPrice(tx.montant) }}</span>
                </td>
                <td class="px-8 py-4 text-center">
                  <span class="text-xs font-bold bg-[#F5F0E8] text-[#8B7340] px-3 py-1 rounded-full">{{ tx.methode_paiement }}</span>
                </td>
                <td class="px-8 py-4 text-center">
                  <span :class="`inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider ${statusStyle(tx.status)}`">
                    {{ tx.status }}
                  </span>
                </td>
              </tr>
              <tr v-if="recentTransactions.length === 0">
                <td colspan="5" class="px-8 py-10 text-center text-gray-400 italic">
                  Aucune transaction récente dans votre secteur.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard'
})

import { ref, onMounted, computed } from 'vue'

const adminName = ref('Administrateur')
const currentSector = ref('Agriculture')
const totalComptesActifs = ref(0)
const totalCategories = ref(0)
const volumeTransactionsMois = ref(0)
const recentTransactions = ref([])

// Style de badge selon le secteur connecté
const sectorBadgeStyle = computed(() => {
  const sector = currentSector.value ? currentSector.value.toLowerCase() : ''
  if (sector === 'agriculture') {
    return 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30'
  } else if (sector === 'elevage' || sector === 'élevage') {
    return 'bg-amber-500/20 text-amber-300 border-amber-500/30'
  } else if (sector === 'peche' || sector === 'pêche') {
    return 'bg-blue-500/20 text-blue-300 border-blue-500/30'
  }
  return 'bg-[#8B7340]/20 text-[#8B7340] border-[#8B7340]/30'
})

function statusStyle(status) {
  switch (status) {
    case 'Valider': return 'bg-emerald-100 text-emerald-700'
    case 'Annuler': return 'bg-red-100 text-red-700'
    case 'EnCours': return 'bg-amber-100 text-amber-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

function formatPrice(price) {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}

onMounted(async () => {
  const token = localStorage.getItem('token')
  const userStr = localStorage.getItem('user')
  
  if (userStr) {
    const user = JSON.parse(userStr)
    adminName.value = `${user.prenom || ''} ${user.nom || ''}`.trim() || 'Administrateur'
    if (user.admin_sectorielle) {
      currentSector.value = user.admin_sectorielle.secteur || 'Agriculture'
    }
  }

  try {
    const config = useRuntimeConfig()
    const apiUrl = config.public.apiUrl || 'http://127.0.0.1:8000/api'

    // Requêtes parallèles pour charger les données
    const [comptesRes, categoriesRes, txRes] = await Promise.all([
      $fetch(`${apiUrl}/admin-sectoriel/comptes`, { headers: { Authorization: `Bearer ${token}` } }),
      $fetch(`${apiUrl}/admin-sectoriel/sous-categories`, { headers: { Authorization: `Bearer ${token}` } }),
      $fetch(`${apiUrl}/admin-sectoriel/transactions`, { headers: { Authorization: `Bearer ${token}` } })
    ])

    if (comptesRes?.comptes) {
      totalComptesActifs.value = comptesRes.comptes.filter(u => u.statut === 'actif').length
    }
    if (categoriesRes) {
      totalCategories.value = categoriesRes.length
    }
    if (txRes?.transactions) {
      const currentMonth = new Date().getMonth()
      const currentYear = new Date().getFullYear()
      const txCeMois = txRes.transactions.filter(t => {
        const d = new Date(t.created_at)
        return d.getMonth() === currentMonth && d.getFullYear() === currentYear && t.status === 'Valider'
      })
      volumeTransactionsMois.value = txCeMois.reduce((sum, curr) => sum + parseFloat(curr.montant || 0), 0)
      recentTransactions.value = txRes.transactions.slice(0, 5)
    }
  } catch (err) {
    console.error('Erreur chargement des données sectorielles:', err)
  }
})
</script>
