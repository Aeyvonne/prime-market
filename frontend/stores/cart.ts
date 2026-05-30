interface CartItem {
  id: number
  nom: string
  prix: number
  photo: string | null
  quantite: number
  quantite_stock: number
  vendeur_id: number
  vendeur_nom: string
}

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>([])

  const total = computed(() =>
    items.value.reduce((sum, item) => sum + item.prix * item.quantite, 0)
  )

  const count = computed(() =>
    items.value.reduce((sum, item) => sum + item.quantite, 0)
  )

  function ajouter(produit: {
    id: number
    nom: string
    prix: number
    photo: string | null
    quantite_stock: number
    vendeur_id: number
    vendeur_nom: string
  }) {
    const existant = items.value.find(i => i.id === produit.id)
    if (existant) {
      existant.quantite = Math.min(existant.quantite + 1, produit.quantite_stock)
    } else {
      items.value.push({
        id: produit.id,
        nom: produit.nom,
        prix: produit.prix,
        photo: produit.photo || null,
        quantite: 1,
        quantite_stock: produit.quantite_stock || 999,
        vendeur_id: produit.vendeur_id,
        vendeur_nom: produit.vendeur_nom || '',
      })
    }
  }

  function modifierQuantite(id: number, quantite: number) {
    const item = items.value.find(i => i.id === id)
    if (!item) return
    if (quantite <= 0) {
      retirer(id)
      return
    }
    item.quantite = Math.min(quantite, item.quantite_stock)
  }

  function retirer(id: number) {
    items.value = items.value.filter(i => i.id !== id)
  }

  function vider() {
    items.value = []
  }

  return { items, total, count, ajouter, modifierQuantite, retirer, vider }
})
