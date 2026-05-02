<?php

namespace App\Http\Controllers;

use App\Models\ReturnRequest;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $returns = $user->returnRequests()->with('order')->latest()->get();
        return response()->json($returns);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason' => 'required|string|min:10',
            'defect_image' => 'required|image|max:5120', // Max 5MB
        ]);

        $user = request()->user();

        // Check if order belongs to user
        $order = \App\Models\Order::findOrFail($request->order_id);
        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Only allow returns on Delivered orders
        if ($order->status !== 'Delivered') {
            return response()->json(['message' => 'Only delivered orders can be returned.'], 400);
        }

        // Check if return already exists for this order
        $existingReturn = ReturnRequest::where('order_id', $request->order_id)->first();
        if ($existingReturn) {
            return response()->json(['message' => 'Return request already exists for this order'], 400);
        }

        $defectImagePath = null;
        if ($request->hasFile('defect_image')) {
            $defectImagePath = $request->file('defect_image')->store('defects', 'public');
        }

        $returnRequest = ReturnRequest::create([
            'order_id' => $request->order_id,
            'user_id' => $user->id,
            'reason' => $request->reason,
            'status' => 'Pending',
            'defect_image' => $defectImagePath,
        ]);

        return response()->json($returnRequest, 201);
    }

    public function show($id)
    {
        $user = request()->user();
        $returnRequest = ReturnRequest::with(['order', 'order.orderItems'])->findOrFail($id);

        if ($returnRequest->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($returnRequest);
    }

    // Admin methods
    public function adminIndex()
    {
        $returns = ReturnRequest::with(['user', 'order'])->latest()->get();
        return response()->json($returns);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Approved,Rejected,Processing,Completed',
            'admin_notes' => 'nullable|string',
        ]);

        $returnRequest = ReturnRequest::findOrFail($id);
        $returnRequest->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        return response()->json($returnRequest);
    }
}
