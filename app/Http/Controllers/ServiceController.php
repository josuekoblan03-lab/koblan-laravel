<?php

namespace App\Http\Controllers;

use App\Models\Prestation;
use App\Models\Category;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Prestation::with(['user.city', 'user.neighborhood', 'serviceType.category', 'mainMedia'])
            ->active()
            ->whereHas('user', function($q){
                $q->verified()->active();
            });

        // Filters
        if ($request->filled('category')) {
            $query->whereHas('serviceType', function($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('q')) {
            $searched = $request->q;
            $query->where(function($q) use ($searched) {
                $q->where('title', 'LIKE', "%{$searched}%")
                  ->orWhere('description', 'LIKE', "%{$searched}%");
            });
        }
        if ($request->filled('city')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('city_id', $request->city);
            });
        }
        if ($request->filled('neighborhood')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('neighborhood_id', $request->neighborhood);
            });
        }

        // Sorting
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'prix_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'prix_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'populaire':
                $query->orderBy('views', 'desc');
                break;
            default:
                // $query->orderBy('is_sponsored', 'desc')->latest();
                $query->latest();
                break;
        }

        $prestations = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        return view('public.services', compact('prestations', 'categories', 'cities'));
    }

    public function show($id)
    {
        $prestation = Prestation::with([
            'user.reviewsReceived.client', 
            'serviceType.category', 
            'medias'
        ])
        ->where('id', $id)
        ->firstOrFail();

        // Increment views
        $prestation->increment('views');

        // Similar Services
        $similar = Prestation::with(['user', 'serviceType.category', 'mainMedia'])
            ->active()
            ->where('id', '!=', $prestation->id)
            ->where('service_type_id', $prestation->service_type_id)
            ->limit(4)
            ->get();

        return view('public.service_detail', compact('prestation', 'similar'));
    }

    public function providers()
    {
        $providers = User::prestataires()
            ->verified()
            ->active()
            ->withCount('prestations')
            ->orderBy('rating_avg', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->paginate(12);

        $categories = Category::orderBy('name')->get();
        return view('public.providers', compact('providers', 'categories'));
    }

    public function categories()
    {
        $categories = Category::withCount(['prestations' => function($q) {
            $q->active();
        }])
        ->with('serviceTypes')
        ->orderBy('prestations_count', 'desc')
        ->get();

        return view('public.categories', compact('categories'));
    }

    public function providerProfile($id)
    {
        $query = User::prestataires()
            ->with([
                'city', 
                'neighborhood',
                'prestations' => function($q) { $q->active(); },
                'reviewsReceived.client'
            ]);

        $isAdmin = auth()->check() && auth()->user()->role === 'admin';
        $isSelf = auth()->check() && auth()->id() == $id;

        if (!$isAdmin && !$isSelf) {
            $query->verified()->active();
        }

        $provider = $query->findOrFail($id);

        return view('public.profile', compact('provider'));
    }
}
