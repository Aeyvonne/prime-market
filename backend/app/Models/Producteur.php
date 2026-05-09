<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Producteur extends Model
{
    protected $table = 'producteurs';
    protected $primaryKey = 'id';
    public $incrementing = false; // PK non auto-increment (partagée avec users)
    public $timestamps = false;
 
    protected $fillable = [
        'id',
        'secteur_travail',
        'adresse',
    ];
 
    // ========== RELATIONS ==========
 
    // Accès aux infos communes (nom, email, etc.)
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
 
    // Produits publiés par ce producteur
    public function produits()
    {
        return $this->hasMany(Produit::class, 'producteur_id');
    }
}
