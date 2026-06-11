<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardHomeController extends Controller
{
    public function index()
    {
        $metrics = [
            'products'   => Product::count(),
            'categories' => Category::count(),
            'users'      => User::count(),
            'orders'     => Order::count(),
            'banners'    => Banner::count(),
        ];

        return view('admin.home', compact('metrics'));
    }
}