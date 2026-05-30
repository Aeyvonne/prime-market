<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentProducteur extends Model
{
    protected $table = 'documents_producteur';

    protected $fillable = [
        'producteur_id',
        'type',
        'fichier',
        'statut_verification',
        'motif_rejet',
    ];

    public function producteur()
    {
        return $this->belongsTo(Producteur::class, 'producteur_id', 'id');
    }

    public static function types(): array
    {
        return [
            'carte_identite' => "Carte d'identité nationale",
            'registre_commerce' => 'Registre de commerce',
            'certificat_agricole' => 'Certificat agricole',
            'attestation_terrain' => "Attestation de terrain",
            'photo_exploitation' => "Photo de l'exploitation",
        ];
    }
}
