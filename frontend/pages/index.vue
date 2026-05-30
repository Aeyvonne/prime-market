<template>
  <div class="overflow-x-hidden">
    <!-- Section 1 — Hero -->
    <section class="relative min-h-screen flex items-center pt-20 pb-12 overflow-hidden">
      <!-- Background Full Image -->
      <div class="absolute inset-0 z-0">
        <img src="/images/hero.jpg" alt="Agriculture Prime Market" class="w-full h-full object-cover">
        <!-- Overlay Vert Sombre -->
        <div class="absolute inset-0 bg-[#1A2E18]/55 backdrop-blur-[2px]"></div>
      </div>
      
      <div class="container mx-auto px-6 md:px-8 relative z-10">
        <div class="max-w-4xl space-y-10 text-center mx-auto lg:text-left lg:mx-0">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 text-white/90 border border-white/20 text-xs font-black uppercase tracking-widest animate-fade-in backdrop-blur-md">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
            </span>
            L'excellence du secteur primaire
          </div>
          
          <h1 class="text-6xl md:text-8xl lg:text-9xl font-black text-white leading-[0.85] font-serif">
            De la Terre <br/>
            <span class="text-[#8BAF85]">à Votre Table</span>
          </h1>
          
          <p class="text-xl md:text-2xl text-white/80 font-medium leading-relaxed max-w-2xl mx-auto lg:mx-0">
            La première plateforme sénégalaise qui connecte directement producteurs locaux, distributeurs et consommateurs pour un commerce plus juste et durable.
          </p>
          
          <div class="flex flex-col sm:flex-row items-center gap-6 pt-4 justify-center lg:justify-start">
            <NuxtLink to="/register" class="group w-full sm:w-auto px-10 py-6 bg-[#2D5A27] text-white rounded-full font-bold text-xl shadow-2xl hover:bg-[#3d7a35] hover:scale-105 transition-all active:scale-95 flex items-center justify-center gap-3">
              Commencer gratuitement
              <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </NuxtLink>
            <NuxtLink to="/catalogue" class="w-full sm:w-auto px-10 py-6 border-2 border-white/30 text-white rounded-full font-bold text-xl hover:bg-white/10 backdrop-blur-sm transition-all active:scale-95 flex items-center justify-center">
              Voir le catalogue
            </NuxtLink>
          </div>

          <!-- Chiffres de confiance -->
          <div class="grid grid-cols-3 gap-8 pt-12 border-t border-white/10 mt-12 max-w-lg mx-auto lg:mx-0">
            <div>
              <p class="text-3xl md:text-4xl font-black text-white leading-none mb-1">500+</p>
              <p class="text-[10px] md:text-xs font-bold text-[#8BAF85] uppercase tracking-widest">Producteurs</p>
            </div>
            <div class="border-x border-white/10 px-4">
              <p class="text-3xl md:text-4xl font-black text-white leading-none mb-1">12</p>
              <p class="text-[10px] md:text-xs font-bold text-[#8BAF85] uppercase tracking-widest">Régions</p>
            </div>
            <div>
              <p class="text-3xl md:text-4xl font-black text-white leading-none mb-1">10k+</p>
              <p class="text-[10px] md:text-xs font-bold text-[#8BAF85] uppercase tracking-widest">Produits</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 2 — Le problème (Secteur Pêche) -->
    <section class="py-24 bg-white overflow-hidden">
      <div class="container mx-auto px-6 md:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-24">
          <div class="w-full lg:w-3/5">
            <div class="max-w-2xl">
              <h2 class="text-[#8B7340] font-black uppercase tracking-[0.3em] text-xs mb-4">Le constat</h2>
              <h3 class="text-4xl md:text-5xl font-black text-[#1A2E18] leading-tight font-serif mb-8">
                La chaîne d'approvisionnement sénégalaise mérite mieux
              </h3>
              <p class="text-gray-600 font-medium mb-12">
                Malgré un potentiel immense, nos secteurs de la pêche et de l'agriculture souffrent de maux structurels qui brident le développement local.
              </p>
            </div>

            <div class="grid grid-cols-1 gap-6">
              <!-- Carte 1 -->
              <div class="group flex gap-8 p-8 rounded-3xl bg-[#F5F0E8]/40 border-2 border-transparent hover:border-[#2D5A27]/20 transition-all">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-[#2D5A27] text-white flex items-center justify-center shadow-lg transform group-hover:rotate-6 transition-transform">
                  <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                  <h4 class="text-lg font-black text-[#1A2E18] mb-2">Producteurs isolés</h4>
                  <p class="text-gray-600 text-sm font-medium leading-relaxed">
                    Manque d'accès direct au marché entraînant des pertes post-récolte estimées entre 30% et 40%.
                  </p>
                </div>
              </div>

              <!-- Carte 2 -->
              <div class="group flex gap-8 p-8 rounded-3xl bg-[#F5F0E8]/40 border-2 border-transparent hover:border-[#2D5A27]/20 transition-all">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-[#8B7340] text-white flex items-center justify-center shadow-lg transform group-hover:rotate-6 transition-transform">
                  <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </div>
                <div>
                  <h4 class="text-lg font-black text-[#1A2E18] mb-2">Intermédiaires opaques</h4>
                  <p class="text-gray-600 text-sm font-medium leading-relaxed">
                    Une multiplication excessive des acteurs rendant les transactions opaques et coûteuses.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Illustration Pêche -->
          <div class="w-full lg:w-2/5 relative">
            <div class="rounded-[3rem] overflow-hidden shadow-3xl transform rotate-2">
              <img src="/images/peche.jpg" alt="Pêche au Sénégal" class="w-full h-[500px] object-cover">
              <div class="absolute inset-0 bg-gradient-to-t from-[#1A2E18]/60 to-transparent"></div>
              <div class="absolute bottom-8 left-8 right-8 text-white">
                <p class="text-xs font-black uppercase tracking-widest mb-2 opacity-80">Secteur Pêche</p>
                <h4 class="text-2xl font-black font-serif">Traçabilité maritime</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 3 — Comment ça marche -->
    <section class="py-24 bg-[#1A2E18] text-white overflow-hidden">
      <div class="container mx-auto px-6 md:px-8">
        <div class="text-center max-w-3xl mx-auto mb-20">
          <h2 class="text-[#8B7340] font-black uppercase tracking-[0.3em] text-xs mb-4">Le fonctionnement</h2>
          <h3 class="text-4xl md:text-5xl font-black text-white leading-tight font-serif">
            Simple, rapide, transparent
          </h3>
          <div class="w-24 h-1 bg-[#8B7340] mx-auto mt-8 rounded-full"></div>
        </div>

        <div class="relative">
          <!-- Ligne de connexion (Desktop) -->
          <div class="absolute top-1/2 left-0 w-full h-0.5 bg-white/10 hidden lg:block -translate-y-1/2 z-0"></div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8 relative z-10">
            <!-- Étape 1 -->
            <div class="text-center space-y-6 group">
              <div class="w-20 h-20 rounded-full bg-[#2D5A27] border-4 border-[#1A2E18] text-white flex items-center justify-center text-3xl font-black mx-auto shadow-2xl group-hover:bg-[#8B7340] transition-colors duration-500 relative">
                1
                <div class="absolute inset-0 rounded-full border-2 border-white/20 scale-125 opacity-0 group-hover:opacity-100 group-hover:scale-100 transition-all duration-500"></div>
              </div>
              <div class="space-y-2">
                <h4 class="text-xl font-bold text-white">Crée ton compte</h4>
                <p class="text-[#8BAF85] text-sm leading-relaxed font-medium">
                  Inscris-toi en choisissant ton rôle : Producteur, Distributeur, Transporteur ou Consommateur.
                </p>
              </div>
            </div>

            <!-- Étape 2 -->
            <div class="text-center space-y-6 group">
              <div class="w-20 h-20 rounded-full bg-[#2D5A27] border-4 border-[#1A2E18] text-white flex items-center justify-center text-3xl font-black mx-auto shadow-2xl group-hover:bg-[#8B7340] transition-colors duration-500 relative">
                2
                <div class="absolute inset-0 rounded-full border-2 border-white/20 scale-125 opacity-0 group-hover:opacity-100 group-hover:scale-100 transition-all duration-500"></div>
              </div>
              <div class="space-y-2">
                <h4 class="text-xl font-bold text-white">Publie tes produits</h4>
                <p class="text-[#8BAF85] text-sm leading-relaxed font-medium">
                  Les producteurs listent leurs récoltes et produits transformés avec prix et quantités.
                </p>
              </div>
            </div>

            <!-- Étape 3 -->
            <div class="text-center space-y-6 group">
              <div class="w-20 h-20 rounded-full bg-[#2D5A27] border-4 border-[#1A2E18] text-white flex items-center justify-center text-3xl font-black mx-auto shadow-2xl group-hover:bg-[#8B7340] transition-colors duration-500 relative">
                3
                <div class="absolute inset-0 rounded-full border-2 border-white/20 scale-125 opacity-0 group-hover:opacity-100 group-hover:scale-100 transition-all duration-500"></div>
              </div>
              <div class="space-y-2">
                <h4 class="text-xl font-bold text-white">Passe commande</h4>
                <p class="text-[#8BAF85] text-sm leading-relaxed font-medium">
                  Parcours le catalogue et commande directement auprès des producteurs en quelques clics.
                </p>
              </div>
            </div>

            <!-- Étape 4 -->
            <div class="text-center space-y-6 group">
              <div class="w-20 h-20 rounded-full bg-[#2D5A27] border-4 border-[#1A2E18] text-white flex items-center justify-center text-3xl font-black mx-auto shadow-2xl group-hover:bg-[#8B7340] transition-colors duration-500 relative">
                4
                <div class="absolute inset-0 rounded-full border-2 border-white/20 scale-125 opacity-0 group-hover:opacity-100 group-hover:scale-100 transition-all duration-500"></div>
              </div>
              <div class="space-y-2">
                <h4 class="text-xl font-bold text-white">Reçois ta livraison</h4>
                <p class="text-[#8BAF85] text-sm leading-relaxed font-medium">
                  Nos transporteurs certifiés acheminent tes produits frais directement à ton adresse.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 4 — Les acteurs -->
    <section class="py-24 bg-white">
      <div class="container mx-auto px-6 md:px-8">
        <div class="text-center max-w-3xl mx-auto mb-20">
          <h2 class="text-[#8B7340] font-black uppercase tracking-[0.3em] text-xs mb-4">Écosystème</h2>
          <h3 class="text-4xl md:text-5xl font-black text-[#1A2E18] leading-tight font-serif">
            Une plateforme pour tous les acteurs
          </h3>
          <div class="w-24 h-1 bg-[#2D5A27] mx-auto mt-8 rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- Producteur -->
          <div class="group p-8 rounded-[2.5rem] bg-emerald-50 border-2 border-emerald-100 hover:border-emerald-500/30 hover:bg-white hover:shadow-2xl transition-all duration-500 flex flex-col items-center text-center">
            <div class="w-20 h-20 rounded-full bg-emerald-500 text-white flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-transform">
              <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h4 class="text-2xl font-black text-emerald-900 mb-3">Producteur</h4>
            <p class="text-emerald-700/70 text-sm font-medium leading-relaxed mb-8 flex-grow">
              Digitalisez votre production, accédez à de nouveaux marchés et vendez au juste prix.
            </p>
            <NuxtLink to="/register?role=producteur" class="w-full py-4 rounded-2xl bg-emerald-500 text-white font-bold hover:bg-emerald-600 transition-colors shadow-lg shadow-emerald-500/20">
              Rejoindre
            </NuxtLink>
          </div>

          <!-- Distributeur -->
          <div class="group p-8 rounded-[2.5rem] bg-amber-50 border-2 border-amber-100 hover:border-amber-500/30 hover:bg-white hover:shadow-2xl transition-all duration-500 flex flex-col items-center text-center">
            <div class="w-20 h-20 rounded-full bg-amber-500 text-white flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-transform">
              <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h4 class="text-2xl font-black text-amber-900 mb-3">Distributeur</h4>
            <p class="text-amber-700/70 text-sm font-medium leading-relaxed mb-8 flex-grow">
              Optimisez votre approvisionnement, géiez vos stocks et trouvez les meilleurs produits locaux.
            </p>
            <NuxtLink to="/register?role=distributeur" class="w-full py-4 rounded-2xl bg-amber-500 text-white font-bold hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/20">
              Rejoindre
            </NuxtLink>
          </div>

          <!-- Consommateur -->
          <div class="group p-8 rounded-[2.5rem] bg-blue-50 border-2 border-blue-100 hover:border-blue-500/30 hover:bg-white hover:shadow-2xl transition-all duration-500 flex flex-col items-center text-center">
            <div class="w-20 h-20 rounded-full bg-blue-500 text-white flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-transform">
              <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h4 class="text-2xl font-black text-blue-900 mb-3">Consommateur</h4>
            <p class="text-blue-700/70 text-sm font-medium leading-relaxed mb-8 flex-grow">
              Achetez des produits frais, locaux et traçables directement depuis votre smartphone.
            </p>
            <NuxtLink to="/register?role=consommateur" class="w-full py-4 rounded-2xl bg-blue-500 text-white font-bold hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/20">
              Rejoindre
            </NuxtLink>
          </div>

          <!-- Transporteur -->
          <div class="group p-8 rounded-[2.5rem] bg-purple-50 border-2 border-purple-100 hover:border-purple-500/30 hover:bg-white hover:shadow-2xl transition-all duration-500 flex flex-col items-center text-center">
            <div class="w-20 h-20 rounded-full bg-purple-500 text-white flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-transform">
              <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
            </div>
            <h4 class="text-2xl font-black text-purple-900 mb-3">Transporteur</h4>
            <p class="text-purple-700/70 text-sm font-medium leading-relaxed mb-8 flex-grow">
              Rentabilisez vos trajets et devenez un maillon clé de la distribution alimentaire.
            </p>
            <NuxtLink to="/register?role=transporteur" class="w-full py-4 rounded-2xl bg-purple-500 text-white font-bold hover:bg-purple-600 transition-colors shadow-lg shadow-purple-500/20">
              Rejoindre
            </NuxtLink>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 5 — Catalogue mis en avant -->
    <section class="py-24 bg-[#F5F0E8]/30">
      <div class="container mx-auto px-6 md:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16">
          <div class="max-w-xl">
            <h2 class="text-[#8B7340] font-black uppercase tracking-[0.3em] text-xs mb-4">Notre Marché</h2>
            <h3 class="text-4xl md:text-5xl font-black text-[#1A2E18] leading-tight font-serif">
              Produits frais disponibles
            </h3>
          </div>
          
          <!-- Filtres Rapides -->
          <div class="flex flex-wrap gap-3">
            <button @click="activeFilter = 'all'" :class="activeFilter === 'all' ? 'bg-[#2D5A27] text-white' : 'bg-white text-[#1A2E18]'" class="px-6 py-3 rounded-full font-bold text-sm shadow-sm transition-all hover:scale-105 active:scale-95">Tous</button>
            <button @click="activeFilter = 'Agriculture'" :class="activeFilter === 'Agriculture' ? 'bg-[#2D5A27] text-white' : 'bg-white text-[#1A2E18]'" class="px-6 py-3 rounded-full font-bold text-sm shadow-sm transition-all hover:scale-105 active:scale-95">Agriculture</button>
            <button @click="activeFilter = 'Elevage'" :class="activeFilter === 'Elevage' ? 'bg-[#2D5A27] text-white' : 'bg-white text-[#1A2E18]'" class="px-6 py-3 rounded-full font-bold text-sm shadow-sm transition-all hover:scale-105 active:scale-95">Élevage</button>
            <button @click="activeFilter = 'Peche'" :class="activeFilter === 'Peche' ? 'bg-[#2D5A27] text-white' : 'bg-white text-[#1A2E18]'" class="px-6 py-3 rounded-full font-bold text-sm shadow-sm transition-all hover:scale-105 active:scale-95">Pêche</button>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <TransitionGroup name="list">
            <div v-for="product in filteredProducts" :key="product.id" class="group bg-white rounded-[2.5rem] overflow-hidden border border-[#2D5A27]/5 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
              <div class="relative overflow-hidden">
                <img v-if="product.photo" :src="`http://127.0.0.1:8000/storage/products/${product.photo}`" :alt="product.nom" class="w-full h-[200px] object-cover bg-[#EFF7EC] group-hover:scale-110 transition-transform duration-700">
                <div v-else class="w-full h-[200px] flex items-center justify-center text-5xl bg-[#EFF7EC] group-hover:scale-110 transition-transform duration-700">📦</div>
                <div class="absolute top-4 right-4 px-3 py-1 bg-white/90 backdrop-blur rounded-full text-[10px] font-black text-[#2D5A27] uppercase tracking-widest shadow-sm">
                  {{ product.sous_categorie?.categorie?.nom || 'Général' }}
                </div>
              </div>
              <div class="p-8">
                <div class="flex justify-between items-start mb-2">
                  <h4 class="text-xl font-black text-[#1A2E18] line-clamp-1">{{ product.nom }}</h4>
                  <p class="text-[#2D5A27] font-black whitespace-nowrap ml-2">{{ formatPrice(product.prix) }}</p>
                </div>
                <p class="text-gray-500 text-xs font-medium mb-6">Récolté le 08 Mai 2026 — Région de Thiès</p>
                <NuxtLink to="/catalogue" class="w-full py-4 rounded-xl border-2 border-[#1A2E18]/5 group-hover:border-[#2D5A27] text-[#1A2E18] group-hover:text-[#2D5A27] font-bold text-sm transition-all flex items-center justify-center gap-2">
                  Détails du produit
                  <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 -translate-x-2 group-hover:translate-x-0 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </NuxtLink>
              </div>
            </div>
          </TransitionGroup>
        </div>

        <div class="mt-20 text-center">
          <NuxtLink to="/catalogue" class="inline-flex items-center gap-4 px-12 py-6 bg-[#1A2E18] text-white rounded-full font-black text-lg hover:bg-[#2D5A27] hover:scale-105 transition-all shadow-2xl active:scale-95">
            Voir tout le catalogue
            <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </div>
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- Section 6 — Pourquoi Prime Market (Secteur Élevage) -->
    <section class="py-24 bg-[#1A2E18] text-white overflow-hidden">
      <div class="container mx-auto px-6 md:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-24">
          <!-- Illustration Élevage -->
          <div class="w-full lg:w-1/2 order-2 lg:order-1">
            <div class="relative">
              <div class="absolute inset-0 bg-[#8B7340]/20 rounded-[4rem] translate-x-4 translate-y-4"></div>
              <img src="/images/elevage.jpg" alt="Élevage Prime Market" class="relative z-10 w-full h-[600px] object-cover rounded-[4rem] shadow-2xl">
              
              <!-- Card Flottante -->
              <div class="absolute -bottom-10 -right-10 z-20 bg-white p-8 rounded-3xl shadow-2xl hidden md:block">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-black">100%</div>
                  <p class="text-[#1A2E18] font-black leading-tight">Santé Animale <br/><span class="text-gray-400 text-xs uppercase tracking-widest">Contrôlée</span></p>
                </div>
              </div>
            </div>
          </div>

          <div class="w-full lg:w-1/2 order-1 lg:order-2 space-y-12">
            <div>
              <h2 class="text-[#8B7340] font-black uppercase tracking-[0.3em] text-xs mb-4">Nos Atouts</h2>
              <h3 class="text-4xl md:text-5xl font-black text-white leading-tight font-serif">
                Pourquoi choisir Prime Market ?
              </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
              <!-- Atout 1 -->
              <div class="space-y-4 group">
                <div class="w-12 h-12 rounded-xl bg-[#2D5A27] flex items-center justify-center text-white group-hover:bg-[#8B7340] transition-colors">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h4 class="text-lg font-black text-white">Traçabilité garantie</h4>
                <p class="text-[#8BAF85] text-sm leading-relaxed">Suivi rigoureux du champ à l'assiette.</p>
              </div>

              <!-- Atout 2 -->
              <div class="space-y-4 group">
                <div class="w-12 h-12 rounded-xl bg-[#2D5A27] flex items-center justify-center text-white group-hover:bg-[#8B7340] transition-colors">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h4 class="text-lg font-black text-white">Paiement Mobile</h4>
                <p class="text-[#8BAF85] text-sm leading-relaxed">Wave & Orange Money intégrés.</p>
              </div>

              <!-- Atout 3 -->
              <div class="space-y-4 group">
                <div class="w-12 h-12 rounded-xl bg-[#2D5A27] flex items-center justify-center text-white group-hover:bg-[#8B7340] transition-colors">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 002-2H4a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h4 class="text-lg font-black text-white">Mobile First</h4>
                <p class="text-[#8BAF85] text-sm leading-relaxed">Une expérience fluide sur smartphone.</p>
              </div>

              <!-- Atout 4 -->
              <div class="space-y-4 group">
                <div class="w-12 h-12 rounded-xl bg-[#2D5A27] flex items-center justify-center text-white group-hover:bg-[#8B7340] transition-colors">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h4 class="text-lg font-black text-white">Circuits courts</h4>
                <p class="text-[#8BAF85] text-sm leading-relaxed">Des prix justes pour tous les acteurs.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 7 — Témoignages -->
    <section class="py-24 bg-white">
      <div class="container mx-auto px-6 md:px-8">
        <div class="text-center max-w-3xl mx-auto mb-20">
          <h2 class="text-[#8B7340] font-black uppercase tracking-[0.3em] text-xs mb-4">Avis clients</h2>
          <h3 class="text-4xl md:text-5xl font-black text-[#1A2E18] leading-tight font-serif">
            Ce que disent nos utilisateurs
          </h3>
          <div class="w-24 h-1 bg-[#2D5A27] mx-auto mt-8 rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Témoignage 1 -->
          <div class="p-10 rounded-[3rem] bg-[#F5F0E8]/40 border-2 border-transparent hover:border-[#2D5A27]/10 transition-all duration-500">
            <div class="flex items-center gap-4 mb-8">
              <div class="w-14 h-14 rounded-full bg-[#2D5A27] text-white flex items-center justify-center font-black text-xl shadow-lg">
                MS
              </div>
              <div>
                <h4 class="font-black text-[#1A2E18]">Moussa Sarr</h4>
                <p class="text-[10px] font-bold text-[#8B7340] uppercase tracking-widest">Producteur — Saint-Louis</p>
              </div>
            </div>
            <div class="flex gap-1 mb-6">
              <svg v-for="i in 5" :key="i" class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            </div>
            <p class="text-gray-600 font-medium italic leading-relaxed">
              "Depuis que j'utilise Prime Market, mes revenus ont augmenté de 25%. Je n'ai plus besoin de chercher des acheteurs, ils viennent directement à moi."
            </p>
          </div>

          <!-- Témoignage 2 -->
          <div class="p-10 rounded-[3rem] bg-[#F5F0E8]/40 border-2 border-transparent hover:border-[#2D5A27]/10 transition-all duration-500">
            <div class="flex items-center gap-4 mb-8">
              <div class="w-14 h-14 rounded-full bg-[#8B7340] text-white flex items-center justify-center font-black text-xl shadow-lg">
                AD
              </div>
              <div>
                <h4 class="font-black text-[#1A2E18]">Aminata Diallo</h4>
                <p class="text-[10px] font-bold text-[#8B7340] uppercase tracking-widest">Distributrice — Dakar</p>
              </div>
            </div>
            <div class="flex gap-1 mb-6">
              <svg v-for="i in 5" :key="i" class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            </div>
            <p class="text-gray-600 font-medium italic leading-relaxed">
              "La qualité des produits est exceptionnelle. La traçabilité me permet de rassurer mes clients sur l'origine des légumes que je vends."
            </p>
          </div>

          <!-- Témoignage 3 -->
          <div class="p-10 rounded-[3rem] bg-[#F5F0E8]/40 border-2 border-transparent hover:border-[#2D5A27]/10 transition-all duration-500">
            <div class="flex items-center gap-4 mb-8">
              <div class="w-14 h-14 rounded-full bg-[#1A2E18] text-white flex items-center justify-center font-black text-xl shadow-lg">
                IK
              </div>
              <div>
                <h4 class="font-black text-[#1A2E18]">Ibrahima Kane</h4>
                <p class="text-[10px] font-bold text-[#8B7340] uppercase tracking-widest">Transporteur — Thiès</p>
              </div>
            </div>
            <div class="flex gap-1 mb-6">
              <svg v-for="i in 5" :key="i" class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            </div>
            <p class="text-gray-600 font-medium italic leading-relaxed">
              "L'application mobile est très simple à utiliser. Je reçois des missions de livraison régulièrement et je suis payé instantanément par Wave."
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 8 — CTA final -->
    <section class="py-24">
      <div class="container mx-auto px-6 md:px-8">
        <div class="relative bg-[#2D5A27] rounded-[4rem] overflow-hidden p-12 md:p-24 text-center shadow-2xl">
          <!-- Motifs de fond -->
          <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
          <div class="absolute bottom-0 right-0 w-96 h-96 bg-[#8B7340]/10 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl"></div>
          
          <div class="relative z-10 space-y-10">
            <h3 class="text-4xl md:text-6xl font-black text-white leading-tight font-serif max-w-4xl mx-auto">
              Rejoignez Prime Market <br/> dès aujourd'hui
            </h3>
            <p class="text-xl md:text-2xl text-white/80 font-medium max-w-2xl mx-auto">
              Gratuit, simple et accessible partout au Sénégal. Propulsez votre commerce vers l'avenir.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 pt-4">
              <NuxtLink to="/register" class="w-full sm:w-auto px-12 py-6 bg-white text-[#2D5A27] rounded-full font-black text-lg hover:scale-105 transition-all shadow-xl active:scale-95">
                Créer mon compte
              </NuxtLink>
              <NuxtLink to="/login" class="w-full sm:w-auto px-12 py-6 border-2 border-white/30 text-white rounded-full font-black text-lg hover:bg-white/10 transition-all active:scale-95">
                Se connecter
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'default'
})

const activeFilter = ref('all')
const products = ref([])
const pending = ref(true)

onMounted(async () => {
  pending.value = true
  try {
    const response = await $fetch('http://127.0.0.1:8000/api/produits')
    products.value = response || []
  } catch (err) {
    console.error('Erreur chargement catalogue public:', err)
    products.value = []
  } finally {
    pending.value = false
  }
})

const filteredProducts = computed(() => {
  if (!products.value || products.value.length === 0) return []
  
  if (activeFilter.value === 'all') return products.value
  
  return products.value.filter(p => {
    // Adapter selon la casse (Peche vs Pêche)
    const categoryName = p.sous_categorie?.categorie?.nom?.replace('ê', 'e') || ''
    return categoryName.includes(activeFilter.value) || activeFilter.value.includes(categoryName)
  })
})

function formatPrice(price) {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}
</script>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 0.8s ease-out forwards;
}

@keyframes bounce-slow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

.animate-bounce-slow {
  animation: bounce-slow 4s ease-in-out infinite;
}

.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: scale(0.9);
}
</style>