<?php

namespace App\Http\Controllers;

use App\Models\LogisticsProvider;
use Illuminate\Http\Request;

class LogisticsProviderController extends Controller
{
    public function index()
    {
        $providers = LogisticsProvider::where('is_active', true)->get();
        return response()->json($providers);
    }

    public function adminIndex()
    {
        $providers = LogisticsProvider::all();
        return response()->json($providers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:logistics_providers,code',
            'tracking_url' => 'nullable|string',
            'shipping_fee' => 'required|numeric|min:0',
        ]);

        $provider = LogisticsProvider::create([
            'name' => $request->name,
            'code' => $request->code,
            'tracking_url' => $request->tracking_url,
            'shipping_fee' => $request->shipping_fee,
            'is_active' => true,
        ]);

        return response()->json($provider, 201);
    }

    public function update(Request $request, $id)
    {
        $provider = LogisticsProvider::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|unique:logistics_providers,code,' . $id,
            'tracking_url' => 'nullable|string',
            'shipping_fee' => 'sometimes|required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $provider->update($request->all());

        return response()->json($provider);
    }

    public function destroy($id)
    {
        $provider = LogisticsProvider::findOrFail($id);
        $provider->delete();

        return response()->json(['message' => 'Logistics provider deleted successfully']);
    }
}
