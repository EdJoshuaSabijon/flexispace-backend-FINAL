<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class CustomerController extends Controller
{
    public function index()
    {
        // DEBUG: Get all users to see what's in database
        $customers = User::withCount('orders')
            ->latest()
            ->get()
            ->map(function ($user) {
                return [
                    'id'             => $user->id,
                    'name'           => $user->name,
                    'first_name'     => $user->first_name,
                    'last_name'      => $user->last_name,
                    'email'          => $user->email,
                    'phone'          => $user->phone,
                    'address'        => $user->address,
                    'role'           => $user->role,
                    'orders_count'   => $user->orders_count,
                    'email_verified' => !is_null($user->email_verified_at),
                    'verified_at'    => $user->email_verified_at
                        ? $user->email_verified_at->format('M d, Y')
                        : null,
                    'joined_at'      => $user->created_at->format('M d, Y'),
                ];
            });

        return response()->json($customers);
    }

    public function show(User $user)
    {
        $user->load('orders.orderItems.product');

        return response()->json([
            'id'             => $user->id,
            'name'           => $user->name,
            'email'          => $user->email,
            'phone'          => $user->phone,
            'address'        => $user->address,
            'role'           => $user->role,
            'email_verified' => !is_null($user->email_verified_at),
            'verified_at'    => $user->email_verified_at?->format('M d, Y'),
            'joined_at'      => $user->created_at->format('M d, Y'),
            'orders'         => $user->orders,
        ]);
    }
}
