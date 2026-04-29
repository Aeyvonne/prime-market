<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mission extends Model
{
    use HasFactory;

    protected $table = 'missions';

    protected $fillable = [
        'status',
        'remuneration',
        'transporteur_id',
        'livraison_id',
    ];

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