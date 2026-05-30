<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- Hero -->
    <section class="bg-gradient-to-br from-[#2D5A27] to-[#1E3F1A] rounded-[2.5rem] p-8 md:p-16 text-white relative overflow-hidden shadow-xl shadow-[#2D5A27]/10">
      <div class="absolute -right-24 -bottom-24 w-96 h-96 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
      <div class="absolute -left-12 -top-12 w-64 h-64 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
      <div class="relative z-10 max-w-3xl">
        <span class="px-4 py-1.5 rounded-full bg-[#8B7340] text-white text-[10px] font-black uppercase tracking-[0.2em] mb-4 inline-block">
          À Propos
        </span>
        <h1 class="text-4xl md:text-5xl font-black font-serif leading-tight mb-4">
          De la Terre à Votre Table
        </h1>
        <p class="text-white/80 text-lg md:text-xl font-medium max-w-2xl">
          Prime Market connecte les producteurs sénégalais aux consommateurs, distributeurs et transporteurs pour une filière agroalimentaire plus juste et transparente.
        </p>
      </div>
    </section>

    <!-- Histoire -->
    <section class="max-w-5xl mx-auto">
      <div class="text-center mb-10">
        <h2 class="text-3xl md:text-4xl font-black font-serif text-gray-900">Notre Histoire</h2>
        <div class="w-16 h-1 bg-[#8B7340] rounded-full mx-auto mt-4"></div>
      </div>
      <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8 md:p-12 space-y-6">
        <p class="text-gray-700 font-medium leading-relaxed">
          Prime Market est né d'un constat simple : au Sénégal, les producteurs agricoles, éleveurs et pêcheurs peinent à écouler leurs produits à un prix juste, tandis que les consommateurs ont du mal à trouver des produits frais et de qualité à des prix abordables. Les intermédiaires multiples et l'absence de transparence fragilisent toute la chaîne de valeur.
        </p>
        <p class="text-gray-700 font-medium leading-relaxed">
          C'est dans ce contexte que notre équipe d'étudiants de l'UCAD ESP (Groupe 8) a imaginé Prime Market : une plateforme numérique qui met en relation directe les acteurs du secteur primaire — producteurs, distributeurs, transporteurs et consommateurs — pour créer un écosystème plus équitable et plus efficace.
        </p>
        <p class="text-gray-700 font-medium leading-relaxed">
          Développé dans le cadre de notre Licence 3 GLSI, ce projet incarne notre vision d'un numérique au service du développement économique local. Prime Market n'est pas seulement une plateforme de vente : c'est un outil de structuration de la filière agroalimentaire sénégalaise.
        </p>
      </div>
    </section>

    <!-- Mission & Valeurs -->
    <section class="max-w-5xl mx-auto">
      <div class="text-center mb-10">
        <h2 class="text-3xl md:text-4xl font-black font-serif text-gray-900">Mission &amp; Valeurs</h2>
        <div class="w-16 h-1 bg-[#8B7340] rounded-full mx-auto mt-4"></div>
      </div>
      <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8 text-center">
          <div class="w-16 h-16 bg-[#F5F0E8] rounded-2xl flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8 text-[#8B7340]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <h3 class="text-lg font-black font-serif text-gray-900 mb-3">Transparence</h3>
          <p class="text-sm text-gray-600 font-medium leading-relaxed">
            Chaque transaction est tracée, du producteur au consommateur. Plus d'intermédiaires opaques, une visibilité totale sur la provenance et le prix des produits.
          </p>
        </div>
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8 text-center">
          <div class="w-16 h-16 bg-[#F5F0E8] rounded-2xl flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8 text-[#8B7340]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
          </div>
          <h3 class="text-lg font-black font-serif text-gray-900 mb-3">Équité</h3>
          <p class="text-sm text-gray-600 font-medium leading-relaxed">
            Nous œuvrons pour une rémunération juste des producteurs et des prix accessibles aux consommateurs, en supprimant les marges excessives des intermédiaires.
          </p>
        </div>
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8 text-center">
          <div class="w-16 h-16 bg-[#F5F0E8] rounded-2xl flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8 text-[#8B7340]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <h3 class="text-lg font-black font-serif text-gray-900 mb-3">Impact Local</h3>
          <p class="text-sm text-gray-600 font-medium leading-relaxed">
            Chaque achat sur Prime Market soutient l'économie sénégalaise et contribue à la souveraineté alimentaire du pays. Nous privilégions les circuits courts.
          </p>
        </div>
      </div>
    </section>

    <!-- Contexte Sénégalais -->
    <section class="max-w-5xl mx-auto">
      <div class="bg-[#F5F0E8] rounded-[2.5rem] p-8 md:p-12">
        <div class="text-center mb-8">
          <h2 class="text-3xl md:text-4xl font-black font-serif text-gray-900">Contexte Sénégalais</h2>
          <div class="w-16 h-1 bg-[#8B7340] rounded-full mx-auto mt-4"></div>
        </div>
        <div class="grid md:grid-cols-2 gap-8">
          <div class="space-y-4">
            <h3 class="text-lg font-black font-serif text-[#2D5A27]">Le Secteur Primaire au Sénégal</h3>
            <p class="text-gray-700 font-medium leading-relaxed text-sm">
              L'agriculture, l'élevage et la pêche représentent près de 15% du PIB sénégalais et emploient plus de 60% de la population active. Pourtant, ces secteurs font face à des défis majeurs : accès limité aux marchés, absence de structuration de la filière, et difficultés d'accès au financement.
            </p>
            <p class="text-gray-700 font-medium leading-relaxed text-sm">
              Les producteurs sont souvent contraints de vendre à des prix très bas faute d'accès direct aux consommateurs, tandis que les importations massives fragilisent la production locale.
            </p>
          </div>
          <div class="space-y-4">
            <h3 class="text-lg font-black font-serif text-[#2D5A27]">La Solution Numérique</h3>
            <p class="text-gray-700 font-medium leading-relaxed text-sm">
              Prime Market répond à ces défis en proposant une place de marché numérique adaptée au contexte sénégalais. Notre plateforme permet aux producteurs de publiés leurs produits, aux distributeurs de s'approvisionner, aux transporteurs d'offrir leurs services, et aux consommateurs d'acheter des produits frais directement.
            </p>
            <p class="text-gray-700 font-medium leading-relaxed text-sm">
              Avec le mobile money (Wave, Orange Money) comme moyen de paiement, Prime Market est accessible à tous, même sans compte bancaire.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Équipe -->
    <section class="max-w-5xl mx-auto">
      <div class="text-center mb-10">
        <h2 class="text-3xl md:text-4xl font-black font-serif text-gray-900">Notre Équipe</h2>
        <div class="w-16 h-1 bg-[#8B7340] rounded-full mx-auto mt-4"></div>
        <p class="text-gray-500 font-medium mt-4">Groupe 8 — Licence 3 GLSI, UCAD ESP</p>
      </div>
      <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8 md:p-12">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div class="text-center">
            <div class="w-24 h-24 rounded-2xl bg-[#2D5A27]/10 flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl font-black text-[#2D5A27] font-serif">SI</span>
            </div>
            <h4 class="font-black text-gray-900">Sory Ibrahim Soumaré</h4>
            <p class="text-xs text-gray-500 font-medium mt-1">Développeur</p>
          </div>
          <div class="text-center">
            <div class="w-24 h-24 rounded-2xl bg-[#2D5A27]/10 flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl font-black text-[#2D5A27] font-serif">DS</span>
            </div>
            <h4 class="font-black text-gray-900">Daba Sene</h4>
            <p class="text-xs text-gray-500 font-medium mt-1">Développeur</p>
          </div>
          <div class="text-center">
            <div class="w-24 h-24 rounded-2xl bg-[#2D5A27]/10 flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl font-black text-[#2D5A27] font-serif">AD</span>
            </div>
            <h4 class="font-black text-gray-900">Arame Yvonne Diop</h4>
            <p class="text-xs text-gray-500 font-medium mt-1">Développeur</p>
          </div>
          <div class="text-center">
            <div class="w-24 h-24 rounded-2xl bg-[#2D5A27]/10 flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl font-black text-[#2D5A27] font-serif">AN</span>
            </div>
            <h4 class="font-black text-gray-900">Aminata Ndiaye</h4>
            <p class="text-xs text-gray-500 font-medium mt-1">Développeur</p>
          </div>
          <div class="text-center">
            <div class="w-24 h-24 rounded-2xl bg-[#2D5A27]/10 flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl font-black text-[#2D5A27] font-serif">AM</span>
            </div>
            <h4 class="font-black text-gray-900">Aicha Mbaye</h4>
            <p class="text-xs text-gray-500 font-medium mt-1">Développeur</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="max-w-5xl mx-auto">
      <div class="bg-gradient-to-br from-[#2D5A27] to-[#1E3F1A] rounded-[2.5rem] p-8 md:p-12 text-center text-white shadow-xl shadow-[#2D5A27]/10">
        <h2 class="text-3xl md:text-4xl font-black font-serif mb-4">Rejoignez l'Aventure</h2>
        <p class="text-white/80 font-medium max-w-xl mx-auto mb-8">
          Que vous soyez producteur, distributeur, transporteur ou consommateur, Prime Market vous ouvre ses portes.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4">
          <NuxtLink to="/register" class="px-8 py-4 bg-white text-[#2D5A27] rounded-2xl font-black text-sm shadow-lg hover:shadow-xl transition-all">
            Créer un Compte
          </NuxtLink>
          <NuxtLink to="/contact" class="px-8 py-4 bg-[#8B7340] text-white rounded-2xl font-black text-sm shadow-lg hover:shadow-xl transition-all">
            Nous Contacter
          </NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })
</script>
