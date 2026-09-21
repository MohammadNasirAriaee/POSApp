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
            ->lowStock()
            ->orderBy('stock_quantity')
            // orderBy('name') sorts case-sensitively on SQLite (uppercase
            // before any lowercase letter), unlike MySQL's default
            // case-insensitive collation; LOWER() keeps this consistent.
            ->orderByRaw('LOWER(name)')
            ->get();

        return Inertia::render('InventoryAlerts/Index', [
            'alerts' => $alerts,
            'lowStockThreshold' => Product::LOW_STOCK_THRESHOLD,
        ]);
    }
}
