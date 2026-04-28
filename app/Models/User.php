<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'telephone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
 
    // ========== RELATIONS ==========
 
    public function producteur()
    {
        return $this->hasOne(Producteur::class, 'id');
    }
 
    public function distributeur()
    {
        return $this->hasOne(Distributeur::class, 'id');
    }
 
    public function consommateur()
    {
        return $this->hasOne(Consommateur::class, 'id');
    }
 
    public function transporteur()
    {
        return $this->hasOne(Transporteur::class, 'id');
    }
 
    public function adminSectorielle()
    {
        return $this->hasOne(AdminSectorielle::class, 'id');
    }
 
    public function superAdministrateur()
    {
        return $this->hasOne(SuperAdministrateur::class, 'id');
    }
 
    // Commandes passées (en tant qu'acheteur)
    public function commandesAcheteur()
    {
        return $this->hasMany(Commande::class, 'acheteur_id');
    }
 
    // Commandes reçues (en tant que vendeur)
    public function commandesVendeur()
    {
        return $this->hasMany(Commande::class, 'vendeur_id');
    }
 
    // Evaluations données
    public function evaluationsDonnees()
    {
        return $this->hasMany(Evaluation::class, 'evaluateur_id');
    }
 
    // Evaluations reçues
    public function evaluationsRecues()
    {
        return $this->hasMany(Evaluation::class, 'evalue_id');
    }
}
 
