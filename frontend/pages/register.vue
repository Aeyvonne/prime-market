<template>
  <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 flex items-center justify-center relative overflow-hidden" style="background-color: #F5F0E8;">
    
    <!-- Décorations d'arrière-plan artistiques -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] rounded-full blur-[120px] opacity-10" style="background-color: #8B7340;"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full blur-3xl opacity-20" style="background-color: #2D5A27;"></div>

    <div class="max-w-5xl w-full bg-white/90 backdrop-blur-md rounded-[3rem] shadow-[0_32px_64px_-12px_rgba(45,90,39,0.2)] border border-white relative z-10 overflow-hidden">
      
      <div class="flex flex-col lg:flex-row">
        <!-- Panneau Gauche (Identité & Réassurance) -->
        <div class="lg:w-1/3 bg-[#2D5A27] p-12 text-white flex flex-col justify-between relative">
          <div class="absolute top-0 left-0 w-full h-full opacity-5 pointer-events-none">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,100 M100,0 L0,100" stroke="white" stroke-width="0.1"/></svg>
          </div>

          <div class="relative z-10">
            <h2 class="text-4xl font-black mb-6 leading-tight mt-12" style="font-family: 'Playfair Display', serif;">
              L'avenir du secteur primaire commence ici.
            </h2>
            <div class="space-y-6 mt-12">
              <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 border border-white/20">
                  <svg class="w-5 h-5 text-[#8B7340]" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"/></svg>
                </div>
                <p class="text-white/80 font-medium">Gestion simplifiée de vos produits et stocks.</p>
              </div>
              <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 border border-white/20">
                  <svg class="w-5 h-5 text-[#8B7340]" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>
                </div>
                <p class="text-white/80 font-medium">Accès direct à un réseau de partenaires certifiés.</p>
              </div>
            </div>
          </div>

          <div class="mt-12 p-6 bg-white/10 rounded-2xl border border-white/10 text-center">
            <p class="text-sm font-bold opacity-75">Plus de 500 professionnels nous font confiance</p>
          </div>
        </div>

        <!-- Section Formulaire -->
        <div class="lg:w-2/3 p-8 md:p-12 lg:p-16 flex flex-col">
          
          <div class="mb-10 text-center">
            <!-- Branding Premium -->
            <NuxtLink to="/" class="inline-block mb-8 group text-center">
              <img src="/images/logo.png" alt="Prime Market" class="h-24 w-auto mx-auto mb-4 transition-transform duration-500 group-hover:scale-105">
              <div class="flex flex-col items-center">
                <h1 class="text-3xl font-black tracking-tighter leading-none" style="font-family: 'Playfair Display', serif;">
                  <span style="color: #2D5A27;">PRIME</span>
                  <span style="color: #8B7340;" class="ml-1">MARKET</span>
                </h1>
                <span class="text-[10px] font-bold uppercase tracking-[0.3em] mt-2" style="color: #2D5A27;">De la Terre à Votre Table</span>
              </div>
            </NuxtLink>
            
            <h3 class="text-2xl font-bold text-gray-800">Créer un compte</h3>
            <p class="text-gray-500 mt-2 font-medium">Rejoignez la communauté Prime Market</p>
          </div>

          <form @submit.prevent="handleRegister" class="space-y-8">
            
            <!-- Sélection du Rôle stylisée -->
            <div class="text-center">
              <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Quel est votre rôle ?</p>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button 
                  v-for="r in roles" 
                  :key="r.id"
                  type="button"
                  @click="form.role = r.id"
                  class="flex flex-col items-center gap-3 p-4 rounded-2xl border-2 transition-all duration-300 group"
                  :class="[form.role === r.id ? 'border-[#2D5A27] bg-[#2D5A27]/5 shadow-lg shadow-[#2D5A27]/10' : 'border-gray-100 bg-white hover:border-[#8B7340]/30']"
                >
                  <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110"
                    :class="[form.role === r.id ? 'bg-[#2D5A27] text-white' : 'bg-[#F5F0E8] text-[#2D5A27]']"
                  >
                    <span class="text-2xl" v-html="r.icon"></span>
                  </div>
                  <span class="text-[10px] font-bold uppercase tracking-widest" :class="[form.role === r.id ? 'text-[#2D5A27]' : 'text-gray-500']">
                    {{ r.label }}
                  </span>
                </button>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
              <!-- Champs Communs -->
              <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                  <svg class="w-4 h-4 text-gray-400 group-focus-within:text-[#2D5A27]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <input v-model="form.nom" required placeholder="Nom" class="w-full pl-12 pr-4 py-4 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-medium">
              </div>

              <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                  <svg class="w-4 h-4 text-gray-400 group-focus-within:text-[#2D5A27]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <input v-model="form.prenom" required placeholder="Prénom" class="w-full pl-12 pr-4 py-4 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-medium">
              </div>

              <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                  <svg class="w-4 h-4 text-gray-400 group-focus-within:text-[#2D5A27]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <input v-model="form.email" type="email" required placeholder="Email" class="w-full pl-12 pr-4 py-4 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-medium">
              </div>

              <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                  <svg class="w-4 h-4 text-gray-400 group-focus-within:text-[#2D5A27]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <input v-model="form.telephone" required placeholder="Téléphone" class="w-full pl-12 pr-4 py-4 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-medium">
              </div>

              <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                  <svg class="w-4 h-4 text-gray-400 group-focus-within:text-[#2D5A27]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <input v-model="form.password" type="password" required placeholder="Mot de passe (8+ caractères)" class="w-full pl-12 pr-4 py-4 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-medium">
              </div>

              <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                  <svg class="w-4 h-4 text-gray-400 group-focus-within:text-[#2D5A27]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <input v-model="form.password_confirmation" type="password" required placeholder="Confirmer le mot de passe" class="w-full pl-12 pr-4 py-4 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-medium">
              </div>
            </div>

            <!-- Champs dynamiques selon rôle -->
            <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-4" enter-to-class="opacity-100 translate-y-0">
              <div v-if="form.role" class="bg-[#F5F0E8]/50 p-6 rounded-3xl border border-dashed border-[#2D5A27]/20 space-y-4">
                <p class="text-xs font-bold text-[#2D5A27] uppercase tracking-wider mb-2">Informations {{ form.role }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <input v-if="['producteur', 'distributeur', 'consommateur', 'transporteur'].includes(form.role)" v-model="form.adresse" placeholder="Adresse complète" class="w-full px-4 py-3 bg-white border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium">
                  
                  <select v-if="form.role === 'producteur'" v-model="form.secteur_travail" class="w-full px-4 py-3 bg-white border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium">
                    <option value="">Secteur d'activité</option>
                    <option value="Agriculture">Agriculture</option>
                    <option value="Elevage">Élevage</option>
                    <option value="Peche">Pêche</option>
                  </select>

                  <select v-if="form.role === 'distributeur'" v-model="form.type_distributeur" class="w-full px-4 py-3 bg-white border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium">
                    <option value="">Type de distributeur</option>
                    <option value="Grossiste">Grossiste</option>
                    <option value="Detaillant">Détaillant</option>
                  </select>

                  <select v-if="form.role === 'transporteur'" v-model="form.type_vehicule" class="w-full px-4 py-3 bg-white border-2 border-transparent rounded-xl focus:border-[#2D5A27] outline-none font-medium">
                    <option value="">Type de véhicule</option>
                    <option value="Moto">Moto</option>
                    <option value="Voiture">Voiture</option>
                    <option value="Camion">Camion</option>
                  </select>
                </div>
              </div>
            </Transition>

            <!-- CGU Validation -->
            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl">
              <input type="checkbox" v-model="form.acceptTerms" required class="mt-1 w-4 h-4 rounded text-[#2D5A27] focus:ring-[#2D5A27]">
              <p class="text-xs text-gray-500 leading-relaxed font-medium">
                En créant un compte, j'accepte les <NuxtLink to="/terms" class="text-[#2D5A27] font-bold hover:underline">Conditions d'Utilisation</NuxtLink> et la <NuxtLink to="/privacy" class="text-[#2D5A27] font-bold hover:underline">Politique de Confidentialité</NuxtLink> de Prime Market.
              </p>
            </div>

            <div class="pt-4">
              <button type="submit" :disabled="loading || !form.acceptTerms"
                class="w-full py-5 rounded-full text-white font-bold text-lg shadow-xl hover:shadow-[#2D5A27]/20 transition-all hover:-translate-y-1 active:scale-95 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed flex items-center justify-center gap-3"
                style="background-color: #2D5A27;"
              >
                <span>{{ loading ? 'Création en cours...' : 'Créer mon compte' }}</span>
                <svg v-if="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
              </button>
              
              <div class="text-center mt-6">
                <p class="text-gray-500 font-medium text-sm">Vous avez déjà un compte ? <NuxtLink to="/login" class="text-[#2D5A27] font-bold hover:underline">Se connecter</NuxtLink></p>
              </div>
            </div>
          </form>

          <!-- Erreurs -->
          <TransitionGroup enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 translate-y-4" enter-to-class="opacity-100 translate-y-0">
            <div v-if="Object.keys(errors).length > 0" class="mt-8 p-6 bg-red-50 border border-red-100 rounded-[2rem]">
              <div class="flex items-center gap-2 text-red-600 font-bold mb-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Erreurs détectées
              </div>
              <ul class="space-y-2">
                <li v-for="(errs, field) in errors" :key="field" class="text-sm text-red-500 font-medium">• {{ errs[0] }}</li>
              </ul>
            </div>
          </TransitionGroup>
        </div>
      </div>
    </div>

    <!-- Décorations (Feuilles) -->
    <div class="fixed -bottom-10 -left-10 opacity-20 pointer-events-none transform -rotate-12 hidden lg:block">
      <svg width="300" height="300" viewBox="0 0 200 200"><path fill="#8B7340" d="M100,20 C100,20 140,60 100,140 C60,60 100,20 100,20 Z"/></svg>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'default'
})

const roles = [
  { id: 'producteur', label: 'Producteur', icon: '🌱' },
  { id: 'distributeur', label: 'Distributeur', icon: '🏪' },
  { id: 'consommateur', label: 'Consommateur', icon: '🛒' },
  { id: 'transporteur', label: 'Transporteur', icon: '🚛' }
]

const form = reactive({
  role: 'producteur',
  nom: '',
  prenom: '',
  email: '',
  telephone: '',
  password: '',
  password_confirmation: '',
  adresse: '',
  secteur_travail: '',
  type_distributeur: '',
  type_vehicule: '',
  acceptTerms: false
})

const loading = ref(false)
const errors = ref({})

async function handleRegister() {
  if (!form.acceptTerms) return
  
  loading.value = true
  errors.value = {}

  // Validation frontend des mots de passe
  if (form.password !== form.password_confirmation) {
    errors.value = { password: ['Les mots de passe ne correspondent pas.'] }
    loading.value = false
    return
  }

  
  const payload = { ...form }
  
  // Nettoyage des champs inutiles selon le rôle pour éviter les conflits de validation
  if (payload.role !== 'producteur') delete payload.secteur_travail
  if (payload.role !== 'distributeur') delete payload.type_distributeur
  if (payload.role !== 'transporteur') {
    delete payload.type_vehicule
    delete payload.zone_intervention
  }

  try {
    const response = await $fetch('http://127.0.0.1:8000/api/register', {
      method: 'POST',
      body: payload
    })

    if (response.token) {
      localStorage.setItem('token', response.token)
      localStorage.setItem('user', JSON.stringify(response.user))
      localStorage.setItem('role', response.user.role)
      
      // Redirection dynamique
      navigateTo(`/${response.user.role}`)
    }
  } catch (err) {
    if (err.data && err.data.errors) {
      errors.value = err.data.errors
    } else {
      console.error('Erreur inscription:', err)
      errors.value = { server: ['Une erreur serveur est survenue. Veuillez réessayer.'] }
    }
  } finally {
    loading.value = false
  }
}
</script>