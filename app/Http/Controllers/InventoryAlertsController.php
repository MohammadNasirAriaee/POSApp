<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class InventoryAlertsController extends Controller
{
    public function index()
    {
        $alerts = Product::query()
            ->with('category')
            ->active()
            ->where('stock_quantity', '<=', 5)
            ->orderBy('stock_quantity')
            ->orderBy('name')
            ->get();

        return Inertia::render('InventoryAlerts/Index', [
            'alerts' => $alerts,
        ]);
    }
}
