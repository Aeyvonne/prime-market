<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Consommateur extends Model
{
    protected $table = 'consommateurs';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
 
    protected $fillable = [
        'id',
        'adresse',
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
 
