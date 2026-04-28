<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Livraison extends Model
{
    protected $table = 'livraisons';
 
    protected $fillable = [
        'adresse_livraison',
        'status',
        'date_expedition',
        'date_livraison_estimee',
        'date_livraison_reelle',
        'commande_id',
    ];
 
    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }
 
    // Une livraison = une mission
    public function mission()
    {
        return $this->hasOne(Mission::class, 'livraison_id');
    }
}
 
