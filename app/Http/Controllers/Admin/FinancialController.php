<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    public function getReport(Request $request)
    {
        // 1. Total Revenue (Delivered Orders)
        $totalRevenue = Order::where('status', 'Delivered')->sum('total_amount');

        // 2. Pending Revenue (Pending, Processing, Shipped)
        $pendingRevenue = Order::whereIn('status', ['Pending', 'Processing', 'Shipped'])->sum('total_amount');

        // 3. Lost Revenue (Returned/Rejected Orders)
        $lostRevenue = Order::whereHas('returnRequests', function($q) {
            $q->where('status', 'Approved');
        })->sum('total_amount');

        // 4. Monthly Revenue (Last 6 months)
        $monthlyRevenue = Order::select(
            DB::raw('sum(total_amount) as revenue'),
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
        )
        ->where('status', 'Delivered')
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->take(6)
        ->get();

        // 5. Payment Methods Breakdown
        $paymentMethods = Order::select(
            'payment_method',
            DB::raw('count(*) as count'),
            DB::raw('sum(total_amount) as total')
        )
        ->groupBy('payment_method')
        ->get();

        // 6. Recent Transactions
        $recentTransactions = Order::with('user')
            ->select('id', 'user_id', 'total_amount', 'payment_method', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'summary' => [
                'total_revenue' => $totalRevenue,
                'pending_revenue' => $pendingRevenue,
                'lost_revenue' => $lostRevenue,
            ],
            'monthly_revenue' => $monthlyRevenue,
            'payment_methods' => $paymentMethods,
            'recent_transactions' => $recentTransactions,
        ]);
    }

    public function getExportData(Request $request)
    {
        $period = $request->query('period', 'monthly');
        $query = Order::with('user')->where('status', 'Delivered');

        $now = \Carbon\Carbon::now();
        if ($period === 'weekly') {
            $query->where('created_at', '>=', $now->subDays(7));
            $title = "Weekly Financial Report";
        } elseif ($period === 'monthly') {
            $query->where('created_at', '>=', $now->subDays(30));
            $title = "Monthly Financial Report";
        } elseif ($period === 'yearly') {
            $query->where('created_at', '>=', $now->subDays(365));
            $title = "Yearly Financial Report";
        }

        $orders = $query->orderBy('created_at', 'desc')->get();
        
        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();

        return response()->json([
            'title' => $title,
            'period' => $period,
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'orders' => $orders
        ]);
    }
}
