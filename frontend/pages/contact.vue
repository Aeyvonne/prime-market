<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- Header -->
    <section class="bg-gradient-to-br from-[#2D5A27] to-[#1E3F1A] rounded-[2.5rem] p-8 md:p-16 text-white relative overflow-hidden shadow-xl shadow-[#2D5A27]/10">
      <div class="absolute -right-24 -bottom-24 w-96 h-96 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
      <div class="absolute -left-12 -top-12 w-64 h-64 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
      <div class="relative z-10 max-w-3xl">
        <span class="px-4 py-1.5 rounded-full bg-[#8B7340] text-white text-[10px] font-black uppercase tracking-[0.2em] mb-4 inline-block">
          Contact
        </span>
        <h1 class="text-4xl md:text-5xl font-black font-serif leading-tight mb-4">
          Nous Contacter
        </h1>
        <p class="text-white/80 text-lg font-medium max-w-2xl">
          Une question, une suggestion ou un problème ? Notre équipe est là pour vous répondre.
        </p>
      </div>
    </section>

    <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-8">
      <!-- Formulaire -->
      <div class="md:col-span-2 bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8 md:p-10">
        <h2 class="text-2xl font-black font-serif text-gray-900 mb-8">Envoyez-nous un message</h2>
        <form @submit.prevent="submitContact" class="space-y-6">
          <div class="grid sm:grid-cols-2 gap-6">
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Nom complet *</label>
              <input v-model="form.nom" required class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm transition-all font-medium">
            </div>
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Email *</label>
              <input v-model="form.email" type="email" required class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm transition-all font-medium">
            </div>
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Sujet *</label>
            <select v-model="form.sujet" required class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm font-bold text-gray-700 transition-all">
              <option value="">Sélectionnez un sujet</option>
              <option value="inscription">Inscription &amp; Connexion</option>
              <option value="commande">Problème de Commande</option>
              <option value="paiement">Problème de Paiement</option>
              <option value="livraison">Problème de Livraison</option>
              <option value="partenariat">Partenariat</option>
              <option value="reclamation">Réclamation</option>
              <option value="autre">Autre</option>
            </select>
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Message *</label>
            <textarea v-model="form.message" required rows="5" class="w-full px-4 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-xl focus:border-[#2D5A27] focus:bg-white outline-none text-sm transition-all font-medium resize-none"></textarea>
          </div>
          <div class="flex items-center justify-end">
            <button type="submit" class="px-8 py-4 bg-[#2D5A27] text-white rounded-2xl font-black text-sm shadow-lg hover:shadow-[#2D5A27]/25 transition-all flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              Envoyer le message
            </button>
          </div>
        </form>
        <div v-if="submitted" class="mt-6 p-4 bg-green-50 border border-green-200 rounded-2xl text-center">
          <p class="text-green-700 font-bold text-sm">Message envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.</p>
        </div>
      </div>

      <!-- Infos de contact -->
      <div class="space-y-6">
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8 space-y-6">
          <h3 class="text-lg font-black font-serif text-gray-900">Nos Coordonnées</h3>
          <div class="space-y-5">
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-xl bg-[#F5F0E8] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-[#8B7340]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              </div>
              <div>
                <p class="text-xs font-black text-gray-400 uppercase tracking-wider">Email</p>
                <a href="mailto:contact@primemarket.sn" class="text-sm font-bold text-[#2D5A27] hover:underline">contact@primemarket.sn</a>
              </div>
            </div>
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-xl bg-[#F5F0E8] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-[#8B7340]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
              </div>
              <div>
                <p class="text-xs font-black text-gray-400 uppercase tracking-wider">Téléphone</p>
                <a href="tel:+221338889900" class="text-sm font-bold text-[#2D5A27] hover:underline">+221 33 888 99 00</a>
              </div>
            </div>
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-xl bg-[#F5F0E8] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-[#8B7340]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              </div>
              <div>
                <p class="text-xs font-black text-gray-400 uppercase tracking-wider">Adresse</p>
                <p class="text-sm font-bold text-gray-700">Dakar, Sénégal</p>
                <p class="text-xs text-gray-500">UCAD ESP, BP 5005</p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-[#F5F0E8] rounded-[2.5rem] p-8">
          <h3 class="text-lg font-black font-serif text-gray-900 mb-4">Horaires</h3>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600 font-medium">Lun - Ven</span>
              <span class="font-bold text-gray-900">08h - 18h</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 font-medium">Samedi</span>
              <span class="font-bold text-gray-900">09h - 13h</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 font-medium">Dimanche</span>
              <span class="font-bold text-red-500">Fermé</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Localisation -->
    <section class="max-w-6xl mx-auto">
      <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8 md:p-10">
        <h2 class="text-2xl font-black font-serif text-gray-900 mb-6">Notre Localisation</h2>
        <div class="bg-[#F5F0E8] rounded-[2rem] p-8 text-center">
          <div class="w-16 h-16 bg-[#2D5A27] rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          </div>
          <h3 class="text-xl font-black font-serif text-gray-900">Dakar, Sénégal</h3>
          <p class="text-sm text-gray-600 font-medium mt-2">
            École Supérieure Polytechnique (ESP) — Université Cheikh Anta Diop (UCAD)
          </p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

import { reactive, ref } from 'vue'

const form = reactive({ nom: '', email: '', sujet: '', message: '' })
const submitted = ref(false)

function submitContact() {
  submitted.value = true
}
</script>
