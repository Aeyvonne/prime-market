<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- Header -->
    <section class="bg-gradient-to-br from-[#2D5A27] to-[#1E3F1A] rounded-[2.5rem] p-8 md:p-16 text-white relative overflow-hidden shadow-xl shadow-[#2D5A27]/10">
      <div class="absolute -right-24 -bottom-24 w-96 h-96 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
      <div class="absolute -left-12 -top-12 w-64 h-64 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
      <div class="relative z-10 max-w-3xl">
        <span class="px-4 py-1.5 rounded-full bg-[#8B7340] text-white text-[10px] font-black uppercase tracking-[0.2em] mb-4 inline-block">
          Aide
        </span>
        <h1 class="text-4xl md:text-5xl font-black font-serif leading-tight mb-4">
          Centre d'Aide
        </h1>
        <p class="text-white/80 text-lg font-medium max-w-2xl">
          Retrouvez les réponses aux questions les plus fréquentes et les ressources pour bien utiliser Prime Market.
        </p>
      </div>
    </section>

    <!-- Liens rapides -->
    <section class="max-w-5xl mx-auto">
      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <NuxtLink to="/catalogue" class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 text-center hover:shadow-md hover:-translate-y-0.5 transition-all">
          <div class="w-12 h-12 bg-[#F5F0E8] rounded-xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-[#8B7340]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
          </div>
          <h4 class="text-sm font-black text-gray-900">Catalogue</h4>
          <p class="text-[10px] text-gray-500 font-medium mt-1">Parcourir les produits</p>
        </NuxtLink>
        <NuxtLink to="/register" class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 text-center hover:shadow-md hover:-translate-y-0.5 transition-all">
          <div class="w-12 h-12 bg-[#F5F0E8] rounded-xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-[#8B7340]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
          </div>
          <h4 class="text-sm font-black text-gray-900">Inscription</h4>
          <p class="text-[10px] text-gray-500 font-medium mt-1">Créer un compte</p>
        </NuxtLink>
        <NuxtLink to="/login" class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 text-center hover:shadow-md hover:-translate-y-0.5 transition-all">
          <div class="w-12 h-12 bg-[#F5F0E8] rounded-xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-[#8B7340]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
          </div>
          <h4 class="text-sm font-black text-gray-900">Connexion</h4>
          <p class="text-[10px] text-gray-500 font-medium mt-1">Accéder à mon compte</p>
        </NuxtLink>
        <NuxtLink to="/contact" class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 text-center hover:shadow-md hover:-translate-y-0.5 transition-all">
          <div class="w-12 h-12 bg-[#F5F0E8] rounded-xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-[#8B7340]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </div>
          <h4 class="text-sm font-black text-gray-900">Contact</h4>
          <p class="text-[10px] text-gray-500 font-medium mt-1">Nous écrire</p>
        </NuxtLink>
      </div>
    </section>

    <!-- FAQ -->
    <section class="max-w-5xl mx-auto">
      <div class="text-center mb-10">
        <h2 class="text-3xl md:text-4xl font-black font-serif text-gray-900">Foire Aux Questions</h2>
        <div class="w-16 h-1 bg-[#8B7340] rounded-full mx-auto mt-4"></div>
      </div>
      <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm divide-y divide-gray-100">
        <div v-for="(faq, idx) in faqs" :key="idx" class="overflow-hidden">
          <button @click="toggleFaq(idx)" class="w-full flex items-center justify-between p-6 md:p-8 text-left hover:bg-[#F5F0E8]/20 transition-colors">
            <span class="text-sm md:text-base font-bold text-gray-900 pr-4">{{ faq.q }}</span>
            <svg
              class="w-5 h-5 shrink-0 text-[#8B7340] transition-transform duration-300"
              :class="{ 'rotate-180': openIndex === idx }"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div
            class="transition-all duration-300 overflow-hidden"
            :style="{ maxHeight: openIndex === idx ? faq.a.length * 2 + 'rem' : '0' }"
          >
            <div class="px-6 md:px-8 pb-6 md:pb-8 text-sm text-gray-600 font-medium leading-relaxed" v-html="faq.a"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact CTA -->
    <section class="max-w-5xl mx-auto">
      <div class="bg-[#F5F0E8] rounded-[2.5rem] p-8 md:p-12 text-center">
        <h2 class="text-2xl md:text-3xl font-black font-serif text-gray-900 mb-4">Vous n'avez pas trouvé votre réponse ?</h2>
        <p class="text-gray-600 font-medium max-w-lg mx-auto mb-8">
          Notre équipe est à votre disposition pour répondre à toutes vos questions.
        </p>
        <NuxtLink to="/contact" class="inline-flex px-8 py-4 bg-[#2D5A27] text-white rounded-2xl font-black text-sm shadow-lg hover:shadow-[#2D5A27]/25 transition-all">
          Nous Contacter
        </NuxtLink>
      </div>
    </section>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

import { ref } from 'vue'

const openIndex = ref(null)

function toggleFaq(idx) {
  openIndex.value = openIndex.value === idx ? null : idx
}

const faqs = [
  {
    q: 'Comment créer un compte sur Prime Market ?',
    a: 'Rendez-vous sur la page <a href="/register" class="text-[#2D5A27] font-bold hover:underline">Inscription</a>. Remplissez vos informations, choisissez votre rôle (Producteur, Distributeur, Transporteur ou Consommateur) et validez. Vous recevrez un email de confirmation.'
  },
  {
    q: 'Comment passer une commande ?',
    a: 'Parcourez le <a href="/catalogue" class="text-[#2D5A27] font-bold hover:underline">Catalogue</a>, ajoutez des produits à votre panier, puis validez la commande. Vous pourrez choisir un moyen de paiement (Wave, Orange Money, Carte bancaire) et suivre la livraison en temps réel.'
  },
  {
    q: 'Quels sont les moyens de paiement acceptés ?',
    a: 'Prime Market accepte Wave, Orange Money, Free Money et les cartes bancaires. Les paiements sont sécurisés et traités instantanément.'
  },
  {
    q: 'Comment suivre ma livraison ?',
    a: 'Une fois votre commande confirmée par le producteur, un transporteur est assigné. Vous pouvez suivre l\'avancement de votre livraison depuis votre tableau de bord, rubrique "Mes Commandes".'
  },
  {
    q: 'Comment devenir producteur sur Prime Market ?',
    a: 'Lors de l\'inscription, sélectionnez le rôle "Producteur" et indiquez votre secteur d\'activité (Agriculture, Élevage ou Pêche). Après validation de votre compte, vous pourrez publier vos produits, gérer vos ventes et recevoir des paiements directement.'
  }
]
</script>
