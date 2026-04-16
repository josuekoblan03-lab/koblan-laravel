<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Category;

class StatisticsController extends Controller
{
    public function index()
    {
        // Inscriptions mensuelles (12 derniers mois)
        $monthly_registrations = User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mois, COUNT(*) as nb')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('mois')
            ->orderBy('mois', 'desc')
            ->get();

        // Commandes mensuelles (12 derniers mois)
        $monthly_orders = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mois, COUNT(*) as nb')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('mois')
            ->orderBy('mois', 'desc')
            ->get();

        // Top catégories par nombre de prestations
        $top_categories = Category::withCount('prestations')
            ->orderByDesc('prestations_count')
            ->take(8)
            ->get();

        return view('admin.statistics.index', compact(
            'monthly_registrations',
            'monthly_orders',
            'top_categories'
        ));
    }
}
