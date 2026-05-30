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
        'commande_id',
    ];

    public function evaluateur()
    {
        return $this->belongsTo(User::class, 'evaluateur_id');
    }

    public function evalue()
    {
        return $this->belongsTo(User::class, 'evalue_id');
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }
}