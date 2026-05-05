<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Notifications\OrderStatusUpdated;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'shipping_address' => $order->shipping_address,
                    'contact_number' => $order->contact_number,
                    'payment_method' => $order->payment_method,
                    'proof_of_payment' => $order->proof_of_payment,
                    'cancel_reason' => $order->cancel_reason,
                    'rejection_reason' => $order->rejection_reason,
                    'created_at' => $order->created_at,
                    'customer' => [
                        'name' => $order->user->name ?? 'N/A',
                        'email' => $order->user->email ?? 'N/A',
                        'phone' => $order->user->phone ?? null,
                    ],
                    'user' => $order->user,
                    'items' => $order->orderItems->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'price' => $item->unit_price,
                            'product_name' => $item->product->name ?? 'Unknown',
                            'product' => $item->product,
                        ];
                    }),
                    'order_items' => $order->orderItems->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'product' => $item->product,
                        ];
                    }),
                ];
            });

        return response()->json($orders);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:Pending,Processing,Shipped,Delivered,Cancelled,Rejected',
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->save();

        if ($order->user) {
            try {
                $order->user->notify(new OrderStatusUpdated($order, $oldStatus, $request->status));
            } catch (\Exception $e) {
                // Notification failed but status was updated - continue silently
            }
        }

        return response()->json([
            'message' => 'Order status updated successfully.',
            'order'   => [
                'id'     => $order->id,
                'status' => $order->status,
            ],
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product']);
        return response()->json($order);
    }

    public function rejectOrder(Request $request, Order $order)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        // Only allow rejecting Pending orders
        if ($order->status !== 'Pending') {
            return response()->json(['message' => 'Only pending orders can be rejected.'], 400);
        }

        $order->update([
            'status' => 'Rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Restore stock - load relationship first
        $order->load('orderItems');
        foreach ($order->orderItems as $item) {
            $product = \App\Models\Product::find($item->product_id);
            if ($product) {
                $product->stock += $item->quantity;
                $product->save();
            }
        }

        if ($order->user) {
            try {
                $order->user->notify(new OrderStatusUpdated($order, 'Pending', 'Rejected'));
            } catch (\Exception $e) {
                // Notification failed but status was updated
            }
        }

        return response()->json([
            'message' => 'Order rejected successfully.',
            'order' => $order->fresh()->load('orderItems.product'),
        ]);
    }

    public function export()
    {
        $orders = Order::with(['user', 'orderItems.product'])->latest()->get();
        $filename = 'orders_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order ID', 'Customer Name', 'Customer Email', 'Total (₱)', 'Status', 'Date']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    '#' . $order->id,
                    $order->user->name ?? 'N/A',
                    $order->user->email ?? 'N/A',
                    number_format($order->total_amount, 2),
                    $order->status,
                    $order->created_at->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
