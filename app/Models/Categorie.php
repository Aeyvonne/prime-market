<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Categorie extends Model
{
    protected $table = 'categories';
 
    protected $fillable = [
        'nom',
        'description',
    ];
 
    // Une catégorie contient plusieurs sous-catégories
    public function sousCategories()
    {
        return $this->hasMany(SousCategorie::class, 'categorie_id');
    }
}
