<template>
  <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 flex items-center justify-center relative overflow-hidden" style="background-color: #F5F0E8;">
    
    <!-- Décorations d'arrière-plan -->
    <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full blur-3xl opacity-20" style="background-color: #2D5A27;"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full blur-3xl opacity-20" style="background-color: #8B7340;"></div>

    <div class="max-w-5xl w-full bg-white/90 backdrop-blur-md rounded-[3rem] shadow-[0_32px_64px_-12px_rgba(45,90,39,0.2)] border border-white relative z-10 overflow-hidden">
      
      <div class="flex flex-col lg:flex-row">
        <!-- Panneau Gauche (Identité) -->
        <div class="lg:w-1/3 bg-[#2D5A27] p-12 text-white flex flex-col justify-between relative overflow-hidden">
          <!-- Motif de feuilles décoratif -->
          <div class="absolute top-0 right-0 opacity-10 transform translate-x-1/4 -translate-y-1/4">
            <svg width="400" height="400" viewBox="0 0 200 200"><path fill="currentColor" d="M40,60 C40,60 80,20 120,60 C160,100 120,140 120,140 C120,140 80,180 40,140 C0,100 40,60 40,60 Z"/></svg>
          </div>

          <div class="relative z-10 text-center lg:text-left">
            <h2 class="text-4xl font-black mb-6 leading-tight mt-12" style="font-family: 'Playfair Display', serif;">
              Ravi de vous revoir !
            </h2>
            <p class="text-white/80 text-lg leading-relaxed font-medium">
              Connectez-vous pour accéder à vos commandes, gérer vos produits et suivre vos livraisons en temps réel.
            </p>
          </div>

          <div class="mt-12 p-6 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/10">
            <p class="text-sm font-bold italic opacity-90">
              "La plateforme qui valorise le travail de nos producteurs locaux."
            </p>
          </div>
        </div>

        <!-- Section Formulaire -->
        <div class="lg:w-2/3 p-8 md:p-16 flex flex-col justify-center relative">
          
          <div class="mb-12 text-center">
            <!-- Branding Premium -->
            <NuxtLink to="/" class="inline-block mb-8 group">
              <img src="/images/logo.png" alt="Prime Market" class="h-24 w-auto mx-auto mb-4 transition-transform duration-500 group-hover:scale-105">
              <div class="flex flex-col items-center">
                <h1 class="text-3xl font-black tracking-tighter leading-none" style="font-family: 'Playfair Display', serif;">
                  <span style="color: #2D5A27;">PRIME</span>
                  <span style="color: #8B7340;" class="ml-1">MARKET</span>
                </h1>
                <span class="text-[10px] font-bold uppercase tracking-[0.3em] mt-2" style="color: #2D5A27;">De la Terre à Votre Table</span>
              </div>
            </NuxtLink>
            
            <h3 class="text-2xl font-bold text-gray-800">Bienvenue</h3>
            <p class="text-gray-500 mt-2 font-medium">Entrez vos identifiants pour continuer</p>
          </div>

          <form @submit.prevent="handleLogin" class="space-y-6">
            
            <div class="space-y-4">
              <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-gray-400 group-focus-within:text-[#2D5A27] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <input 
                  v-model="form.email" 
                  type="email" 
                  required 
                  placeholder="Votre adresse email"
                  class="w-full pl-12 pr-4 py-4 bg-[#F5F0E8]/50 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-medium text-gray-700"
                >
              </div>

              <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-gray-400 group-focus-within:text-[#2D5A27] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <input 
                  v-model="form.password" 
                  type="password" 
                  required 
                  placeholder="Votre mot de passe"
                  class="w-full pl-12 pr-4 py-4 bg-[#F5F0E8]/50 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-medium text-gray-700"
                >
              </div>
            </div>

            <div class="flex items-center justify-between text-sm font-bold">
              <label class="flex items-center gap-2 text-gray-500 cursor-pointer group">
                <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#2D5A27] focus:ring-[#2D5A27]">
                <span class="group-hover:text-[#2D5A27] transition-colors">Se souvenir de moi</span>
              </label>
              <a href="#" class="text-[#8B7340] hover:text-[#2D5A27] transition-colors">Mot de passe oublié ?</a>
            </div>

            <div class="pt-4">
              <button type="submit" :disabled="loading"
                class="w-full py-4 rounded-full text-white font-bold text-lg shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 active:scale-95 disabled:opacity-50 flex items-center justify-center gap-3"
                style="background-color: #2D5A27;"
              >
                <span>{{ loading ? 'Connexion...' : 'Se connecter' }}</span>
                <svg v-if="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
              </button>
            </div>

            <div class="text-center mt-8">
              <p class="text-gray-500 font-medium">Vous n'avez pas de compte ? 
                <NuxtLink to="/register" class="text-[#2D5A27] font-bold hover:underline">Créer un compte</NuxtLink>
              </p>
            </div>
          </form>

          <!-- Erreur -->
          <Transition enter-active-class="transform transition ease-out duration-300" enter-from-class="translate-y-2 opacity-0" enter-to-class="translate-y-0 opacity-100">
            <div v-if="error" class="mt-6 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-3 text-red-600 text-sm font-bold">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
              {{ error }}
            </div>
          </Transition>
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

const form = reactive({
  email: '',
  password: ''
})

const loading = ref(false)
const error = ref('')

async function handleLogin() {
  loading.value = true
  error.value = ''
  
  try {
    const config = useRuntimeConfig()
    const response = await $fetch('http://127.0.0.1:8000/api/login', {
      method: 'POST',
      body: form
    })

    if (response.token) {
      localStorage.setItem('token', response.token)
      localStorage.setItem('user', JSON.stringify(response.user))
      localStorage.setItem('role', response.user.role)
      
      // Redirection dynamique selon le rôle
      const routes = {
        producteur: '/producteur',
        distributeur: '/distributeur',
        consommateur: '/consommateur',
        transporteur: '/transporteur',
        super_administrateur: '/admin/super',
        admin_sectoriel: '/admin'
      }
      const targetRoute = routes[response.user.role] || '/'
      navigateTo(targetRoute)
    }
  } catch (err) {
    error.value = 'Email ou mot de passe incorrect.'
    console.error('Erreur login:', err)
  } finally {
    loading.value = false
  }
}
</script>
