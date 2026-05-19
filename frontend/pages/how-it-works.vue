<template>
  <div class="min-h-screen bg-[#F5F0E8] font-sans pb-0">
    <!-- Section 1 : Hero -->
    <section class="bg-[#2D5A27] pt-32 pb-24 px-4 relative overflow-hidden">
      <div class="absolute inset-0 opacity-10 pointer-events-none">
        <svg class="absolute -top-24 -left-24 w-96 h-96 text-white" fill="currentColor" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"><path d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,81.3,-46.3C90.8,-33.5,96.8,-18,97.3,-2.3C97.8,13.4,92.8,29.3,83.1,42.5C73.4,55.7,59.1,66.2,43.5,73.4C27.9,80.6,11,84.5,-5.2,85.6C-21.4,86.7,-36.9,85.1,-50.2,77.7C-63.5,70.3,-74.6,57.1,-81.4,41.9C-88.2,26.7,-90.7,9.5,-87.3,-6.2C-83.9,-21.9,-74.6,-36.1,-62.4,-46.8C-50.2,-57.5,-35.1,-64.7,-20.9,-71.4C-6.7,-78.1,6.6,-84.3,21.3,-84.3C36,-84.3,50.7,-78.1,44.7,-76.4Z" transform="translate(100 100)" /></svg>
      </div>
      <div class="max-w-4xl mx-auto text-center relative z-10">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white font-serif mb-6 drop-shadow-sm">Comment fonctionne Prime Market ?</h1>
        <p class="text-white/80 text-lg md:text-xl font-medium max-w-2xl mx-auto">Une plateforme unifiée qui connecte producteurs, distributeurs, consommateurs et transporteurs du secteur primaire sénégalais de manière sécurisée et transparente.</p>
      </div>
    </section>

    <!-- Section 2 : Les étapes générales -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 relative">
      <div class="text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-black text-[#1A1A1A] font-serif mb-4">Un processus simplifié pour tous</h2>
        <p class="text-gray-500 font-medium">Rejoignez la plateforme et commencez à opérer en 4 étapes simples.</p>
      </div>

      <div class="relative max-w-5xl mx-auto">
        <!-- Ligne de connexion Desktop -->
        <div class="hidden md:block absolute top-10 left-12 right-12 h-1 bg-[#2D5A27]/20 -z-10"></div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
          <!-- Etape 1 -->
          <div class="relative flex flex-col items-center text-center group animate-in fade-in slide-in-from-bottom-8 duration-700" style="animation-delay: 100ms;">
            <div class="w-20 h-20 bg-white border-4 border-[#2D5A27] rounded-full flex items-center justify-center text-2xl font-black text-[#2D5A27] shadow-xl group-hover:scale-110 group-hover:bg-[#2D5A27] group-hover:text-white transition-all duration-300 mb-6">1</div>
            <h3 class="text-xl font-bold text-[#1A1A1A] mb-3">Crée ton compte</h3>
            <p class="text-gray-500 text-sm">Crée ton compte selon ton rôle (Producteur, Distributeur, etc.) et valide tes informations.</p>
          </div>
          <!-- Etape 2 -->
          <div class="relative flex flex-col items-center text-center group animate-in fade-in slide-in-from-bottom-8 duration-700" style="animation-delay: 200ms;">
            <div class="w-20 h-20 bg-white border-4 border-[#2D5A27] rounded-full flex items-center justify-center text-2xl font-black text-[#2D5A27] shadow-xl group-hover:scale-110 group-hover:bg-[#2D5A27] group-hover:text-white transition-all duration-300 mb-6">2</div>
            <h3 class="text-xl font-bold text-[#1A1A1A] mb-3">Accède à ton espace</h3>
            <p class="text-gray-500 text-sm">Découvre un tableau de bord et un espace personnalisé adapté à tes besoins.</p>
          </div>
          <!-- Etape 3 -->
          <div class="relative flex flex-col items-center text-center group animate-in fade-in slide-in-from-bottom-8 duration-700" style="animation-delay: 300ms;">
            <div class="w-20 h-20 bg-white border-4 border-[#2D5A27] rounded-full flex items-center justify-center text-2xl font-black text-[#2D5A27] shadow-xl group-hover:scale-110 group-hover:bg-[#2D5A27] group-hover:text-white transition-all duration-300 mb-6">3</div>
            <h3 class="text-xl font-bold text-[#1A1A1A] mb-3">Publie ou Commande</h3>
            <p class="text-gray-500 text-sm">Publie des produits en vente ou parcours le catalogue pour commander.</p>
          </div>
          <!-- Etape 4 -->
          <div class="relative flex flex-col items-center text-center group animate-in fade-in slide-in-from-bottom-8 duration-700" style="animation-delay: 400ms;">
            <div class="w-20 h-20 bg-white border-4 border-[#2D5A27] rounded-full flex items-center justify-center text-2xl font-black text-[#2D5A27] shadow-xl group-hover:scale-110 group-hover:bg-[#2D5A27] group-hover:text-white transition-all duration-300 mb-6">4</div>
            <h3 class="text-xl font-bold text-[#1A1A1A] mb-3">Paiement & Livraison</h3>
            <p class="text-gray-500 text-sm">Paiement sécurisé sur la plateforme et livraison suivie en temps réel.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 3 : Par type d'acteur -->
    <section class="bg-white py-24">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-black text-[#1A1A1A] font-serif mb-4">À chacun son espace</h2>
          <p class="text-gray-500 font-medium">Découvrez ce que Prime Market vous réserve selon votre profil.</p>
        </div>

        <div class="max-w-5xl mx-auto">
          <!-- Tabs Navigation -->
          <div class="flex flex-wrap justify-center gap-4 mb-12">
            <button 
              v-for="role in roles" 
              :key="role.id"
              @click="activeRole = role.id"
              class="px-6 py-3 rounded-full font-bold text-sm transition-all duration-300 flex items-center gap-2"
              :class="activeRole === role.id ? 'bg-[#2D5A27] text-white shadow-lg' : 'bg-[#F5F0E8] text-[#1A1A1A] hover:bg-[#2D5A27]/10'"
            >
              <span v-html="role.icon"></span>
              {{ role.name }}
            </button>
          </div>

          <!-- Tab Content -->
          <div class="bg-[#F5F0E8] rounded-[2.5rem] p-8 md:p-12 transition-all duration-500">
            <div 
              class="flex flex-col md:flex-row items-center gap-12 animate-in fade-in zoom-in-95 duration-500" 
              :key="activeRole"
            >
              <div class="flex-1">
                <h3 class="text-3xl font-black text-[#1A1A1A] font-serif mb-8 flex items-center gap-4">
                  <span class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-[#2D5A27] shadow-sm" v-html="activeRoleData.icon"></span>
                  Producteur / {{ activeRoleData.name }}
                </h3>
                <ul class="space-y-6 mb-10">
                  <li v-for="(step, idx) in activeRoleData.steps" :key="idx" class="flex gap-4 group">
                    <div class="w-10 h-10 rounded-full bg-[#8B7340]/20 flex items-center justify-center flex-shrink-0 text-[#8B7340] font-black group-hover:bg-[#8B7340] group-hover:text-white transition-colors duration-300 mt-1">
                      {{ idx + 1 }}
                    </div>
                    <div>
                      <h4 class="font-bold text-[#1A1A1A] text-lg mb-1">{{ step.title }}</h4>
                      <p class="text-gray-600 text-sm leading-relaxed">{{ step.desc }}</p>
                    </div>
                  </li>
                </ul>
                <NuxtLink :to="`/register?role=${activeRoleData.id}`" class="inline-flex px-8 py-4 bg-[#8B7340] text-white rounded-2xl font-black shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all active:scale-95 items-center gap-3">
                  Rejoindre en tant que {{ activeRoleData.name }}
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </NuxtLink>
              </div>
              
              <!-- SVG Illustration container -->
              <div class="flex-1 hidden md:flex justify-center w-full">
                <div class="w-full h-96 bg-white/60 backdrop-blur rounded-[2rem] border-4 border-white shadow-sm flex items-center justify-center text-[#2D5A27]/20 transition-all duration-500 hover:text-[#2D5A27]/40" v-html="activeRoleData.illustration"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 4 : FAQ -->
    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
      <div class="text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-black text-[#1A1A1A] font-serif mb-4">Questions Fréquentes</h2>
        <p class="text-gray-500 font-medium">Tout ce que vous devez savoir sur Prime Market.</p>
      </div>

      <div class="space-y-4">
        <div v-for="(faq, i) in faqs" :key="i" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-300">
          <button @click="toggleFaq(i)" class="w-full px-6 py-5 flex items-center justify-between text-left font-bold text-[#1A1A1A] hover:bg-[#F5F0E8]/50 transition-colors">
            <span class="text-lg">{{ faq.q }}</span>
            <div class="w-8 h-8 rounded-full bg-[#2D5A27]/10 flex items-center justify-center flex-shrink-0 ml-4 transition-transform duration-300" :class="{'rotate-180': openFaq === i, 'bg-[#2D5A27] text-white': openFaq === i}">
              <svg class="w-5 h-5 text-[#2D5A27]" :class="{'text-white': openFaq === i}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
            </div>
          </button>
          <div 
            v-show="openFaq === i" 
            class="px-6 pb-6 text-gray-500 text-sm leading-relaxed animate-in slide-in-from-top-2 fade-in duration-300"
          >
            {{ faq.a }}
          </div>
        </div>
      </div>
    </section>

    <!-- Section 5 : CTA Final -->
    <section class="bg-[#1A2E18] py-24 px-4 text-center relative overflow-hidden">
      <!-- Decor -->
      <div class="absolute inset-0 opacity-10 pointer-events-none">
        <svg class="absolute top-0 right-0 w-96 h-96 text-[#8B7340] transform translate-x-1/2 -translate-y-1/2" fill="currentColor" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"><path d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,81.3,-46.3C90.8,-33.5,96.8,-18,97.3,-2.3C97.8,13.4,92.8,29.3,83.1,42.5C73.4,55.7,59.1,66.2,43.5,73.4C27.9,80.6,11,84.5,-5.2,85.6C-21.4,86.7,-36.9,85.1,-50.2,77.7C-63.5,70.3,-74.6,57.1,-81.4,41.9C-88.2,26.7,-90.7,9.5,-87.3,-6.2C-83.9,-21.9,-74.6,-36.1,-62.4,-46.8C-50.2,-57.5,-35.1,-64.7,-20.9,-71.4C-6.7,-78.1,6.6,-84.3,21.3,-84.3C36,-84.3,50.7,-78.1,44.7,-76.4Z" transform="translate(100 100)" /></svg>
      </div>
      
      <div class="max-w-4xl mx-auto relative z-10">
        <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-white font-serif mb-6 drop-shadow-lg">Prêt à rejoindre Prime Market ?</h2>
        <p class="text-[#D4E8CC] text-lg md:text-xl font-medium mb-12 max-w-2xl mx-auto">Rejoignez des milliers d'acteurs de l'agro-alimentaire au Sénégal et boostez votre activité grâce à notre plateforme unifiée.</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
          <NuxtLink to="/register" class="w-full sm:w-auto px-10 py-5 bg-[#8B7340] text-white rounded-full font-black text-lg shadow-xl shadow-[#8B7340]/20 hover:shadow-2xl hover:-translate-y-1 transition-all active:scale-95">
            Créer mon compte
          </NuxtLink>
          <NuxtLink to="/catalogue" class="w-full sm:w-auto px-10 py-5 bg-transparent border-2 border-[#D4E8CC] text-[#D4E8CC] rounded-full font-black text-lg hover:bg-[#D4E8CC] hover:text-[#1A2E18] transition-all active:scale-95">
            Voir le catalogue
          </NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

definePageMeta({
  layout: 'default'
})

const activeRole = ref('producteur')
const openFaq = ref(0)

function toggleFaq(i) {
  openFaq.value = openFaq.value === i ? -1 : i
}

const faqs = [
  { q: "C'est gratuit de s'inscrire ?", a: "Oui, l'inscription est totalement gratuite pour tous les acteurs. Des frais de service minimes ne s'appliquent que sur les transactions réussies." },
  { q: "Comment sont sécurisés les paiements ?", a: "Nous utilisons des passerelles de paiement reconnues et sécurisées. Les fonds sont bloqués jusqu'à la confirmation de livraison pour protéger à la fois l'acheteur et le vendeur." },
  { q: "Quelles régions du Sénégal sont couvertes ?", a: "Prime Market est accessible partout au Sénégal. Notre réseau de transporteurs permet de relier les producteurs de Casamance ou de la vallée du fleuve jusqu'aux marchés de Dakar et d'ailleurs." },
  { q: "Comment la traçabilité des produits est-elle garantie ?", a: "Chaque produit sur Prime Market est lié directement à son producteur d'origine ou à un distributeur vérifié. Les informations de provenance sont transparentes sur la fiche produit." },
  { q: "Que faire en cas de problème avec une commande ?", a: "Notre service client est disponible 7j/7. Vous pouvez signaler un litige directement depuis votre espace personnel dans la section 'Mes Commandes'." }
]

const roles = [
  { 
    id: 'producteur', 
    name: 'Producteur',
    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>',
    illustration: '<svg class="w-48 h-48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
    steps: [
      { title: 'Ajoute tes récoltes', desc: 'Publie tes produits frais avec photos et prix de gros.' },
      { title: 'Reçois des commandes', desc: 'Les distributeurs te contactent et paient en ligne.' },
      { title: 'Expédie via nos transporteurs', desc: 'On s\'occupe de te trouver un livreur.' },
      { title: 'Sois payé rapidement', desc: 'Reçois tes fonds directement sur Mobile Money.' }
    ]
  },
  { 
    id: 'distributeur', 
    name: 'Distributeur',
    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
    illustration: '<svg class="w-48 h-48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
    steps: [
      { title: 'Sourcing facile', desc: 'Achète en gros auprès des meilleurs producteurs.' },
      { title: 'Gère ton stock', desc: 'Un inventaire numérique pour suivre tes achats.' },
      { title: 'Revends au détail', desc: 'Crée ta propre vitrine en ligne pour les consommateurs.' },
      { title: 'Marge garantie', desc: 'Fixe tes prix de revente en toute liberté.' }
    ]
  },
  { 
    id: 'consommateur', 
    name: 'Consommateur',
    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
    illustration: '<svg class="w-48 h-48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>',
    steps: [
      { title: 'Explore le catalogue', desc: 'Trouve des produits locaux frais au meilleur prix.' },
      { title: 'Paiement sans tracas', desc: 'Paye par carte bancaire ou Mobile Money.' },
      { title: 'Suis ta commande', desc: 'Sois notifié de l\'expédition et de l\'arrivée.' },
      { title: 'Laisse un avis', desc: 'Partage ton expérience avec la communauté.' }
    ]
  },
  { 
    id: 'transporteur', 
    name: 'Transporteur',
    icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>',
    illustration: '<svg class="w-48 h-48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>',
    steps: [
      { title: 'Trouve des missions', desc: 'Accepte les demandes de livraison sur ton trajet.' },
      { title: 'Optimise tes voyages', desc: 'Ne roule plus jamais à vide.' },
      { title: 'Mise à jour du statut', desc: 'Signale quand le colis est récupéré ou livré.' },
      { title: 'Gagne plus d\'argent', desc: 'Paiements rapides et fiables pour chaque mission.' }
    ]
  }
]

const activeRoleData = computed(() => {
  return roles.find(r => r.id === activeRole.value) || roles[0]
})
</script>
