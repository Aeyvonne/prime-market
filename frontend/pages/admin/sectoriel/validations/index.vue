<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <div class="bg-gradient-to-br from-[#2D5A27] to-[#1E3F1A] rounded-[2.5rem] p-8 md:p-12 text-white relative overflow-hidden shadow-xl shadow-[#2D5A27]/10">
      <div class="relative z-10">
        <h1 class="text-3xl md:text-4xl font-black font-serif leading-tight mb-2">Validations Documents</h1>
        <p class="text-white/80 text-sm md:text-base font-medium">Vérifiez les documents des producteurs en attente dans votre secteur.</p>
      </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
      <div v-if="loading" class="p-12 space-y-4">
        <div v-for="i in 3" :key="i" class="h-16 bg-gray-50 animate-pulse rounded-xl"></div>
      </div>

      <div v-else-if="comptes.length === 0" class="text-center py-16">
        <div class="w-16 h-16 bg-[#F5F0E8] rounded-full flex items-center justify-center mx-auto text-[#8B7340] mb-4">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="text-lg font-black text-gray-800">Aucune validation en attente</h3>
        <p class="text-sm text-gray-400 font-medium mt-1">Tous les producteurs de votre secteur ont été vérifiés.</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Producteur</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Documents</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="c in comptes" :key="c.id" class="hover:bg-[#F5F0E8]/20 transition-all">
              <td class="px-8 py-5">
                <div class="flex items-center gap-4">
                  <div class="w-10 h-10 rounded-2xl bg-[#2D5A27]/10 flex items-center justify-center text-[#2D5A27] font-black">{{ c.prenom?.[0] }}</div>
                  <div>
                    <p class="text-sm font-black text-gray-900">{{ c.prenom }} {{ c.nom }}</p>
                    <p class="text-xs text-gray-400 font-medium">{{ c.email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5 text-center">
                <div class="flex items-center justify-center gap-2 text-xs font-bold">
                  <span class="text-green-600">{{ c.documents.verifies }} V</span>
                  <span class="text-amber-600">{{ c.documents.en_attente }} A</span>
                  <span v-if="c.documents.rejetes" class="text-red-600">{{ c.documents.rejetes }} R</span>
                </div>
              </td>
              <td class="px-8 py-5 text-center">
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase"
                  :class="c.statut === 'actif' ? 'bg-green-100 text-green-700' : c.statut === 'suspendu' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'"
                >{{ c.statut }}</span>
              </td>
              <td class="px-8 py-5 text-right">
                <NuxtLink :to="`/admin/sectoriel/comptes/${c.id}/documents`"
                  class="inline-flex items-center gap-2 px-4 py-2 bg-[#2D5A27] text-white rounded-xl font-bold text-xs shadow-lg hover:shadow-[#2D5A27]/20 transition-all hover:-translate-y-0.5 active:scale-95"
                >
                  Voir les documents
                </NuxtLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard', middleware: ['auth', 'role'] })

const config = useRuntimeConfig()
const apiUrl = config.public.apiUrl || 'http://127.0.0.1:8000/api'

const comptes = ref([])
const loading = ref(true)

async function load() {
  const token = localStorage.getItem('token')
  try {
    const res = await $fetch(`${apiUrl}/admin-sectoriel/comptes/en-attente`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    comptes.value = res.comptes || []
  } catch (err) { console.error(err) }
  finally { loading.value = false }
}

onMounted(load)
</script>
