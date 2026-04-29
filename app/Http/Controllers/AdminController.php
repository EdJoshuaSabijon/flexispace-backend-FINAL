<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')->get();
        return response()->json($customers);
    }
}
