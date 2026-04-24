<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'status', 'total',
        'payment_method', 'payment_status'
    ];

    // Une commande appartient à un consommateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Une commande a plusieurs items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}