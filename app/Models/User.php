<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /*
    |----------------------------------------------------------------------
    | Champs autorisés à l'assignation de masse
    |----------------------------------------------------------------------
    */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',       // producteur | distributeur | consommateur | transporteur | admin
        'telephone',
        'adresse',
        'statut',     // actif | en_attente | suspendu
    ];

    /*
    |----------------------------------------------------------------------
    | Champs cachés dans les réponses JSON
    |----------------------------------------------------------------------
    */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |----------------------------------------------------------------------
    | Casts de types automatiques
    |----------------------------------------------------------------------
    */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /*
    |----------------------------------------------------------------------
    | Helpers de rôles (pratiques dans les controllers et les gates)
    |----------------------------------------------------------------------
    */
    public function estProducteur(): bool
    {
        return $this->role === 'producteur';
    }

    public function estDistributeur(): bool
    {
        return $this->role === 'distributeur';
    }

    public function estConsommateur(): bool
    {
        return $this->role === 'consommateur';
    }

    public function estTransporteur(): bool
    {
        return $this->role === 'transporteur';
    }

    public function estAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function estActif(): bool
    {
        return $this->statut === 'actif';
    }
}