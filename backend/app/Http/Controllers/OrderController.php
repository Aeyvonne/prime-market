<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Order::with('orderItems.product')
                 ->where('user_id', $request->user()->id)
                 ->get()
        );
    }

    public function show($id)
    {
        return response()->json(
            Order::with('orderItems.product')->findOrFail($id)
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items'              => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'payment_method'     => 'nullable|in:wave,orange_money,cash',
        ]);

        $total = 0;
        $items = [];

        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $subtotal = $product->price * $item['quantity'];
            $total += $subtotal;
            $items[] = [
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'unit_price' => $product->price,
            ];
        }

        $order = Order::create([
            'user_id'        => $request->user()->id,
            'total'          => $total,
            'payment_method' => $data['payment_method'] ?? null,
            'status'         => 'en_attente',
        ]);

        $order->orderItems()->createMany($items);

        return response()->json($order->load('orderItems.product'), 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $data  = $request->validate([
            'status' => 'required|in:en_attente,confirmee,en_livraison,livree,annulee',
        ]);
        $order->update($data);
        return response()->json($order);
    }
}