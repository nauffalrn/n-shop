<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('Admin');
    }

    public function dashboard()
    {
        // Statistik untuk dashboard admin
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'total_users' => User::where('is_admin', false)->count(),
            'pending_orders' => Order::where('is_paid', false)->whereNotNull('payment_receipt')->count(),
            'total_revenue' => $this->calculateTotalRevenue()
        ];

        // Recent orders
        $recentOrders = Order::with(['user', 'transactions.product'])
                           ->orderBy('created_at', 'desc')
                           ->take(10)
                           ->get();

        // Low stock products
        $lowStockProducts = Product::where('stock', '<=', 5)->take(10)->get();

        return view('admin.admin_dashboard', compact('stats', 'recentOrders', 'lowStockProducts'));
    }

    public function orders()
    {
        $orders = Order::with(['user', 'transactions.product'])
                     ->orderBy('created_at', 'desc')
                     ->paginate(20);
        
        return view('admin.admin_orders', compact('orders'));
    }

    public function users()
    {
        $users = User::where('is_admin', false)
                   ->withCount(['orders', 'carts', 'wishlists'])
                   ->orderBy('created_at', 'desc')
                   ->paginate(20);
        
        return view('admin.admin_users', compact('users'));
    }

    private function calculateTotalRevenue()
    {
        return Order::where('is_paid', true)
                  ->with('transactions.product')
                  ->get()
                  ->sum(function($order) {
                      return $order->transactions->sum(function($transaction) {
                          return $transaction->product->price * $transaction->amount; 
                      });
                  });
    }
}