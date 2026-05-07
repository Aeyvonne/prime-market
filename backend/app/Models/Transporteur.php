<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Transporteur extends Model
{
    protected $table = 'transporteurs';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
 
    protected $fillable = [
        'id',
        'type_vehicule',
        'zone_intervention',
        'disponibilite',
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
 
    // Missions assignées à ce transporteur
    public function missions()
    {
        return $this->hasMany(Mission::class, 'transporteur_id');
    }
}
