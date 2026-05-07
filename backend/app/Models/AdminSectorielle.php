<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class AdminSectorielle extends Model
{
    protected $table = 'admin_sectorielles';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
 
    protected $fillable = [
        'id',
        'secteur',
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
