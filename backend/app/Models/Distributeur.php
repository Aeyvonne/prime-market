<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Distributeur extends Model
{
    protected $table = 'distributeurs';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
 
    protected $fillable = [
        'id',
        'type_distributeur',
        'adresse',
        'note',
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
 
    // Evaluations reçues en tant que distributeur
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'evalue_id', 'id');
    }
}
 
