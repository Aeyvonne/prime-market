<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluation extends Model
{
    use HasFactory;

    protected $table = 'evaluations';

    protected $fillable = [
        'note',
        'commentaire',
        'date',
        'evaluateur_id',
        'evalue_id',
    ];

    /**
     * L'utilisateur qui évalue
     */
    public function evaluateur()
    {
        return $this->belongsTo(User::class, 'evaluateur_id');
    }

    /**
     * L'utilisateur évalué
     */
    public function evalue()
    {
        return $this->belongsTo(User::class, 'evalue_id');
    }
}