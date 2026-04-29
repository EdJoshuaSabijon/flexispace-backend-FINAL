<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        Log::info('OrderController@index - User ID: ' . $userId);
        
        // Only return orders for the authenticated user
        $orders = Order::where('user_id', $userId)
            ->with(['orderItems.product'])
            ->latest()
            ->get();
        
        Log::info('OrderController@index - Found ' . $orders->count() . ' orders for user ' . $userId);

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
            'contact_number' => 'required|string',
        ]);

        $user = $request->user();

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $orderItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    return response()->json(['message' => "Insufficient stock for {$product->name}"], 400);
                }
                $totalAmount += $product->price * $item['quantity'];
                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ];
            }

            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'Pending',
                'total_amount' => $totalAmount,
                'shipping_address' => $validated['shipping_address'],
                'contact_number' => $validated['contact_number'],
            ]);

            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);

                $product = Product::findOrFail($item['product_id']);
                $product->stock -= $item['quantity'];
                $product->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order->load('orderItems.product'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to place order'], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $order = Order::with(['orderItems.product'])->findOrFail($id);
        
        // Ensure user can only view their own orders (unless admin)
        if ($request->user()->role !== 'admin' && $order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json($order);
    }
}
