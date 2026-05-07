<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Paiement extends Model
{
    protected $table = 'paiements';
 
    protected $fillable = [
        'methode_paiement',
        'montant',
        'status',
        'date_transaction',
        'commande_id',
    ];
 
    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }
}
