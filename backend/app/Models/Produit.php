<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Produit extends Model
{
    protected $table = 'produits';
 
    protected $fillable = [
        'nom',
        'description',
        'prix',
        'quantite',
        'photo',
        'status',
        'sous_categorie_id',
        'producteur_id',
        'proprietaire_type',
        'proprietaire_id',
    ];
 
    // ========== RELATIONS ==========
 
    // Appartient à une sous-catégorie
    public function sousCategorie()
    {
        return $this->belongsTo(SousCategorie::class, 'sous_categorie_id');
    }
 
    // Appartient à un producteur
    public function producteur()
    {
        return $this->belongsTo(Producteur::class, 'producteur_id');
    }
 
    // Apparaît dans plusieurs lignes de commande
    public function lignesCommande()
    {
        return $this->hasMany(LigneCommande::class, 'produit_id');
    }

    // Appartient à un distributeur (optionnel)
    public function distributeur()
    {
        return $this->belongsTo(Distributeur::class, 'proprietaire_id');
    }
}
 
