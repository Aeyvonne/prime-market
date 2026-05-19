<template>
  <div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
      <NuxtLink
        to="/producteur/produits"
        class="p-2 bg-white rounded-xl border border-gray-200 hover:bg-gray-50 transition-all"
      >
        ←
      </NuxtLink>
      <div>
        <h1 class="text-2xl font-black text-gray-800">Ajouter un produit</h1>
        <p class="text-sm text-gray-500 mt-1">Remplissez les informations du produit</p>
      </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">

      <!-- Message erreur -->
      <div v-if="erreur" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-600 text-sm font-bold">
        {{ erreur }}
      </div>

      <!-- Message succès -->
      <div v-if="succes" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl text-green-600 text-sm font-bold">
        ✅ Produit ajouté avec succès !
      </div>

      <!-- Nom -->
      <div class="mb-5">
        <label class="block text-sm font-black text-gray-700 mb-2">Nom du produit *</label>
        <input
          v-model="form.nom"
          type="text"
          placeholder="Ex: Tomates fraîches"
          class="w-full px-4 py-3 bg-[#F5F0E8] border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all text-sm font-medium"
        >
      </div>

      <!-- Description -->
      <div class="mb-5">
        <label class="block text-sm font-black text-gray-700 mb-2">Description</label>
        <textarea
          v-model="form.description"
          placeholder="Décrivez votre produit..."
          rows="3"
          class="w-full px-4 py-3 bg-[#F5F0E8] border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all text-sm font-medium resize-none"
        ></textarea>
      </div>

      <!-- Prix + Quantité -->
      <div class="grid grid-cols-2 gap-4 mb-5">
        <div>
          <label class="block text-sm font-black text-gray-700 mb-2">Prix (FCFA) *</label>
          <input
            v-model="form.prix"
            type="number"
            placeholder="Ex: 5000"
            min="0"
            class="w-full px-4 py-3 bg-[#F5F0E8] border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all text-sm font-medium"
          >
        </div>
        <div>
          <label class="block text-sm font-black text-gray-700 mb-2">Quantité en stock *</label>
          <input
            v-model="form.quantite"
            type="number"
            placeholder="Ex: 100"
            min="0"
            class="w-full px-4 py-3 bg-[#F5F0E8] border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all text-sm font-medium"
          >
        </div>
      </div>

      <!-- Sous-catégorie -->
      <div class="mb-8">
        <label class="block text-sm font-black text-gray-700 mb-2">Catégorie *</label>
        <select
          v-model="form.sous_categorie_id"
          class="w-full px-4 py-3 bg-[#F5F0E8] border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all text-sm font-medium"
        >
          <option value="" disabled>Choisir une catégorie</option>
          <option v-for="cat in sousCategories" :key="cat.id" :value="cat.id">
            {{ cat.nom }}
          </option>
        </select>
      </div>

      <!-- Boutons -->
      <div class="flex gap-4">
        <NuxtLink
          to="/producteur/produits"
          class="flex-1 text-center py-4 bg-gray-100 text-gray-600 font-bold rounded-2xl hover:bg-gray-200 transition-all"
        >
          Annuler
        </NuxtLink>
        <button
          @click="soumettreFormulaire"
          :disabled="chargement"
          class="flex-1 py-4 bg-[#2D5A27] text-white font-bold rounded-2xl hover:bg-[#234820] transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
        >
          <span v-if="chargement">⏳ Ajout en cours...</span>
          <span v-else>✅ Ajouter le produit</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const config = useRuntimeConfig()
const chargement = ref(false)
const erreur = ref('')
const succes = ref(false)
const sousCategories = ref([])

const form = ref({
  nom: '',
  description: '',
  prix: '',
  quantite: '',
  sous_categorie_id: '',
})

onMounted(async () => {
  await chargerSousCategories()
})

async function chargerSousCategories() {
  try {
    const token = localStorage.getItem('token')
    const data = await $fetch(`${config.public.apiUrl}/sous-categories`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    sousCategories.value = data
  } catch (error) {
    console.error('Erreur chargement catégories:', error)
  }
}

async function soumettreFormulaire() {
  erreur.value = ''
  succes.value = false

  if (!form.value.nom || !form.value.prix || !form.value.quantite || !form.value.sous_categorie_id) {
    erreur.value = 'Veuillez remplir tous les champs obligatoires (*)'
    return
  }

  chargement.value = true
  try {
    const token = localStorage.getItem('token')
    await $fetch(`${config.public.apiUrl}/producteur/produits`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` },
      body: {
        nom: form.value.nom,
        description: form.value.description,
        prix: Number(form.value.prix),
        quantite: Number(form.value.quantite),        sous_categorie_id: Number(form.value.sous_categorie_id),
      }
    })
    succes.value = true
    setTimeout(() => navigateTo('/producteur/produits'), 1500)
  } catch (error) {
    erreur.value = 'Erreur lors de l\'ajout. Vérifiez les informations.'
    console.error(error)
  } finally {
    chargement.value = false
  }
}
</script>