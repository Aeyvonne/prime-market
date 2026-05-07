<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class SousCategorie extends Model
{
    protected $table = 'sous_categories';
 
    protected $fillable = [
        'nom',
        'description',
        'categorie_id',
    ];
 
    // Appartient à une catégorie
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }
 
    // Contient plusieurs produits
    public function produits()
    {
        return $this->hasMany(Produit::class, 'sous_categorie_id');
    }
}
 
