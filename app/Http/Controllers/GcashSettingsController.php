<?php

namespace App\Http\Controllers;

use App\Models\GcashSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GcashSettingsController extends Controller
{
    public function index()
    {
        $settings = GcashSettings::firstOrCreate(
            ['id' => 1],
            [
                'gcash_number' => null,
                'gcash_qr_code' => null,
                'gcash_account_name' => null,
                'is_active' => true,
            ]
        );
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $settings = GcashSettings::firstOrCreate(['id' => 1]);

        $request->validate([
            'gcash_number' => 'nullable|string|max:20',
            'gcash_account_name' => 'nullable|string|max:255',
            'gcash_qr_code' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $request->except('gcash_qr_code');

        // Handle QR code upload
        if ($request->hasFile('gcash_qr_code')) {
            // Delete old QR code if exists
            if ($settings->gcash_qr_code) {
                Storage::disk('public')->delete($settings->gcash_qr_code);
            }

            $path = $request->file('gcash_qr_code')->store('gcash-qr', 'public');
            $data['gcash_qr_code'] = $path;
        }

        $settings->update($data);

        return response()->json($settings);
    }
}
