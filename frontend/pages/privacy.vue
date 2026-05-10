<template>
  <div class="min-h-screen py-16 px-4 sm:px-6 lg:px-8" style="background-color: #F5F0E8;">
    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-3xl shadow-xl border border-gray-100">
      <NuxtLink to="/register" class="inline-flex items-center gap-2 text-sm font-medium mb-8 hover:underline" style="color: #2D5A27;">
        ← Retour à l'inscription
      </NuxtLink>
      
      <h1 class="text-4xl font-extrabold mb-8" style="color: #2D5A27; font-family: 'Playfair Display', serif;">
        Politique de Confidentialité
      </h1>
      
      <div class="prose prose-green max-w-none space-y-6 text-gray-700" style="font-family: 'DM Sans', sans-serif;">
        <p><strong>Dernière mise à jour : 08 Mai 2026</strong></p>
        
        <section>
          <h2 class="text-2xl font-bold" style="color: #8B7340;">1. Collecte des Données</h2>
          <p>Nous collectons les informations nécessaires à votre identification et à votre activité sur la plateforme : nom, prénom, adresse email, numéro de téléphone, et données spécifiques à votre rôle (secteur de travail, type de véhicule, etc.).</p>
        </section>

        <section>
          <h2 class="text-2xl font-bold" style="color: #8B7340;">2. Utilisation des Données</h2>
          <p>Vos données sont utilisées pour :</p>
          <ul class="list-disc pl-5 space-y-2">
            <li>Gérer votre compte et vos accès.</li>
            <li>Faciliter la mise en relation avec d'autres acteurs (ex: un distributeur peut voir votre numéro de téléphone pour passer commande).</li>
            <li>Améliorer nos services et assurer la sécurité des transactions.</li>
          </ul>
        </section>

        <section>
          <h2 class="text-2xl font-bold" style="color: #8B7340;">3. Partage des Données</h2>
          <p>Prime Market ne vend jamais vos données personnelles à des tiers. Vos informations professionnelles (nom de l'exploitation, catalogue produits) sont visibles par les autres membres de la plateforme pour permettre les échanges commerciaux.</p>
        </section>

        <section>
          <h2 class="text-2xl font-bold" style="color: #8B7340;">4. Vos Droits</h2>
          <p>Conformément à la législation en vigueur, vous disposez d'un droit d'accès, de rectification et de suppression de vos données personnelles depuis les paramètres de votre compte.</p>
        </section>

        <section>
          <h2 class="text-2xl font-bold" style="color: #8B7340;">5. Sécurité</h2>
          <p>Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles pour protéger vos données contre tout accès non autorisé.</p>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })
</script>
