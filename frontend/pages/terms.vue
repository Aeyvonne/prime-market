<template>
  <div class="min-h-screen py-16 px-4 sm:px-6 lg:px-8" style="background-color: #F5F0E8;">
    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-3xl shadow-xl border border-gray-100">
      <NuxtLink to="/register" class="inline-flex items-center gap-2 text-sm font-medium mb-8 hover:underline" style="color: #2D5A27;">
        ← Retour à l'inscription
      </NuxtLink>
      
      <h1 class="text-4xl font-extrabold mb-8" style="color: #2D5A27; font-family: 'Playfair Display', serif;">
        Conditions d'Utilisation
      </h1>
      
      <div class="prose prose-green max-w-none space-y-6 text-gray-700" style="font-family: 'DM Sans', sans-serif;">
        <p><strong>Dernière mise à jour : 08 Mai 2026</strong></p>
        
        <section>
          <h2 class="text-2xl font-bold" style="color: #8B7340;">1. Acceptation des Conditions</h2>
          <p>En créant un compte sur Prime Market, vous acceptez d'être lié par les présentes conditions d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser nos services.</p>
        </section>

        <section>
          <h2 class="text-2xl font-bold" style="color: #8B7340;">2. Description du Service</h2>
          <p>Prime Market est une plateforme numérique facilitant la mise en relation entre les producteurs (agriculture, élevage, pêche), les distributeurs, les transporteurs et les consommateurs finaux au Sénégal. Nous fournissons des outils de gestion de catalogue, de commande et de suivi logistique.</p>
        </section>

        <section>
          <h2 class="text-2xl font-bold" style="color: #8B7340;">3. Responsabilités des Utilisateurs</h2>
          <ul class="list-disc pl-5 space-y-2">
            <li><strong>Producteurs :</strong> Vous vous engagez à fournir des produits conformes aux normes sanitaires en vigueur et à tenir vos stocks à jour.</li>
            <li><strong>Transporteurs :</strong> Vous êtes responsable de la sécurité des marchandises durant le transport et du respect des délais annoncés.</li>
            <li><strong>Tous :</strong> Vous garantissez l'exactitude des informations fournies lors de votre inscription (téléphone, adresse, secteur d'activité).</li>
          </ul>
        </section>

        <section>
          <h2 class="text-2xl font-bold" style="color: #8B7340;">4. Transactions et Paiements</h2>
          <p>Prime Market facilite les transactions mais n'est pas partie au contrat de vente entre le producteur et l'acheteur, sauf indication contraire. Les frais de service éventuels sont affichés avant chaque transaction.</p>
        </section>

        <section>
          <h2 class="text-2xl font-bold" style="color: #8B7340;">5. Suspension de Compte</h2>
          <p>Nous nous réservons le droit de suspendre tout compte ne respectant pas les règles de déontologie de la plateforme ou fournissant des informations frauduleuses.</p>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })
</script>
