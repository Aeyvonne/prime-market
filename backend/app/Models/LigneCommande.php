<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class LigneCommande extends Model
{
    protected $table = 'ligne_commandes';
 
    protected $fillable = [
        'quantite',
        'prix_unitaire',
        'prix_total',
        'commande_id',
        'produit_id',
    ];
 
    // ========== RELATIONS ==========
 
    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }
 
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }
}
 
