<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class SuperAdministrateur extends Model
{
    protected $table = 'super_administrateurs';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
 
    protected $fillable = [
        'id',
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
