<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'description', 'price',
        'stock', 'image', 'category_id', 'user_id'
    ];

    // Un produit appartient à une catégorie
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Un produit appartient à un producteur (user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un produit apparaît dans plusieurs order_items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}