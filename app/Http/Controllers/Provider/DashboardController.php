<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Prestation;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\WalletTransaction;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Les services du prestataire
        $services = Prestation::where('user_id', $user->id)
            ->with(['serviceType.category'])
            ->withCount('orders')
            ->latest()
            ->get();

        // Les commandes où l'utilisateur est le prestataire
        // Comme on a ajouté le prestataire_id dans Orders, on peut utiliser ordersAsPrestataire
        $allOrders = $user->ordersAsPrestataire()
            ->with(['client', 'prestation'])
            ->latest()
            ->get();

        $pendingOrders = $allOrders->whereIn('status', ['pending', 'en_attente']);
        $recentOrders = $allOrders->whereIn('status', ['accepted', 'confirmed', 'in_progress', 'completed'])->take(5);

        $wallet = $user->wallet;

        $stats = [
            'total_revenue' => $wallet ? $wallet->transactions()->where('type', 'credit')->sum('amount') : 0,
            'total_services' => $services->count(),
            'rating' => $user->rating_avg,
            'total_reviews' => $user->total_reviews ?? 0
        ];

        return view('provider.dashboard', compact('services', 'pendingOrders', 'recentOrders', 'stats', 'wallet'));
    }
}
