<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Prestation;
use App\Models\Review;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers      = User::where('role', 'client')->count();
        $totalPrestataires = User::where('role', 'prestataire')->count();
        $enAttente       = User::where('role', 'prestataire')->where('is_verified', false)->count();
        $totalCommandes  = Order::count();
        $totalPrestations = Prestation::where('status', 'approved')->count();
        $totalAvis       = Review::count();
        $revenus         = Order::whereIn('status', ['completed'])->sum('amount') * 0.10;

        $stats = [
            'total_users'       => $totalUsers,
            'prestataires'      => $totalPrestataires,
            'en_attente'        => $enAttente,
            'total_commandes'   => $totalCommandes,
            'total_prestations' => $totalPrestations,
            'total_avis'        => $totalAvis,
            'revenus'           => $revenus,
        ];

        $prestataires_attente = User::where('role', 'prestataire')
            ->where('is_verified', false)
            ->latest()
            ->take(5)
            ->get();

        $recent_users  = User::latest()->take(6)->get();
        $recent_orders = Order::with('client', 'prestataire')->latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'stats', 'prestataires_attente', 'recent_users', 'recent_orders'
        ));
    }
}
