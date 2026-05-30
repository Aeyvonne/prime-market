<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mission extends Model
{
    use HasFactory;

    protected $table = 'missions';

    protected $appends = ['noms_produits'];

    protected $fillable = [
        'status',
        'remuneration',
        'transporteur_id',
        'livraison_id',
    ];

    public function getNomsProduitsAttribute()
    {
        $lignes = $this->livraison?->commande?->lignesCommande;
        if (!$lignes || $lignes->isEmpty()) {
            return null;
        }
        $noms = $lignes->pluck('produit.nom')->filter()->unique()->values();
        return $noms->isNotEmpty() ? $noms->join(', ') : null;
    }

    /**
     * Relation avec Transporteur
     */
    public function transporteur()
    {
        return $this->belongsTo(Transporteur::class);
    }

    /**
     * Relation avec Livraison
     */
    public function livraison()
    {
        return $this->belongsTo(Livraison::class);
    }
}