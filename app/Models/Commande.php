<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Commande extends Model
{
    protected $table = 'commandes';
 
    protected $fillable = [
        'acheteur_id',
        'vendeur_id',
        'date_commande',
        'status',
        'montant_total',
        'mode_livraison',
        'adresse_livraison',
    ];
 
    // ========== RELATIONS ==========
 
    // L'acheteur (consommateur ou distributeur)
    public function acheteur()
    {
        return $this->belongsTo(User::class, 'acheteur_id');
    }
 
    // Le vendeur (producteur ou distributeur)
    public function vendeur()
    {
        return $this->belongsTo(User::class, 'vendeur_id');
    }
 
    // Lignes de la commande
    public function lignesCommande()
    {
        return $this->hasMany(LigneCommande::class, 'commande_id');
    }
 
    // Paiement associé (1 commande = 1 paiement)
    public function paiement()
    {
        return $this->hasOne(Paiement::class, 'commande_id');
    }
 
    // Livraison associée (1 commande = 1 livraison)
    public function livraison()
    {
        return $this->hasOne(Livraison::class, 'commande_id');
    }
}
 
