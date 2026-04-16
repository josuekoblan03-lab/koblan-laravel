<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use App\Models\Prestation;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Catégories avec contage des prestations via ServiceType
        $categories = Category::withCount(['prestations'])
            ->where('is_active', true)
            ->orderBy('prestations_count', 'desc')
            ->limit(12)
            ->get();

        // Top prestataires
        $topProviders = User::prestataires()
            ->verified()
            ->active()
            ->withCount('prestations')
            ->orderBy('rating_avg', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->limit(6)
            ->get();

        // Prestations récentes
        $prestations = Prestation::with(['user.city', 'user.neighborhood', 'serviceType.category', 'mainMedia'])
            ->active()
            ->whereHas('user', function($q){
                $q->verified()->active();
            })
            // ->orderBy('is_sponsored', 'desc') // To add later if sponsored field is added
            ->latest()
            ->limit(8)
            ->get();

        // Statistiques
        $stats = [
            'prestataires' => User::prestataires()->verified()->count(),
            'clients' => User::clients()->count(),
            'services' => Prestation::active()->count(),
            'commandes' => Order::where('status', 'completed')->count(),
        ];

        // Avis récents
        $reviews = Review::with(['client', 'prestation'])
            ->visible()
            ->latest()
            ->limit(5)
            ->get();

        return view('public.home', compact('categories', 'topProviders', 'prestations', 'stats', 'reviews'));
    }
}
