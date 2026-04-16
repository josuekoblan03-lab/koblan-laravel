<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Prestation;
use App\Models\Notification;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $orders = Order::where('client_id', $user->id)
            ->with(['prestation.user', 'prestation.serviceType.category', 'prestation.mainMedia'])
            ->latest()
            ->take(10)
            ->get();

        $favorites = $user->favorites()
            ->with(['user', 'serviceType.category', 'mainMedia'])
            ->latest()
            ->get();

        $stats = [
            'total_orders' => Order::where('client_id', $user->id)->count(),
            'active_orders' => Order::where('client_id', $user->id)
                ->whereIn('status', ['pending', 'accepted', 'in_progress'])
                ->count(),
            'favorites' => $favorites->count(),
        ];

        // Placeholder for notifications, wallet
        $notifications = [];
        $wallet = Wallet::where('user_id', $user->id)->first();
        $messages_count = 0; // Replace when messaging is implemented

        $recommendations = Prestation::with(['user', 'serviceType.category', 'mainMedia'])
            ->active()
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('client.dashboard', compact(
            'orders', 'favorites', 'stats', 'notifications', 'wallet', 'messages_count', 'recommendations'
        ));
    }

    public function favorites()
    {
        $user = Auth::user();
        $favorites = $user->favorites()
            ->with(['user', 'serviceType.category', 'mainMedia'])
            ->latest()
            ->get();

        $recommendations = Prestation::with(['user', 'serviceType.category', 'mainMedia'])
            ->active()
            ->whereNotIn('id', $favorites->pluck('id'))
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('client.favorites', compact('favorites', 'recommendations'));
    }

    public function toggleFavorite(Request $request, Prestation $service)
    {
        $user = Auth::user();
        $result = $user->favoris()->toggle($service->id);

        $action = count($result['attached']) > 0 ? 'added' : 'removed';

        return response()->json(['status' => 'success', 'action' => $action]);
    }
}
