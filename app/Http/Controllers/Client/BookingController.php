<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prestation;
use App\Models\Order;
use App\Models\Neighborhood;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('client_id', $user->id)
            ->with([
                'prestation.mainMedia',
                'prestataire',
            ])
            ->latest()
            ->paginate(10);
            
        return view('client.orders', compact('orders'));
    }

    public function create(Prestation $service)
    {
        $neighborhoods = Neighborhood::with('city')->orderBy('name')->get();
        return view('client.checkout', compact('service', 'neighborhoods'));
    }

    public function store(Request $request, Prestation $service)
    {
        $request->validate([
            'neighborhood_id' => 'required|exists:neighborhoods,id',
            'date_intervention' => 'required|date',
            'instructions' => 'nullable|string'
        ]);

        $neighborhood = Neighborhood::with('city')->find($request->neighborhood_id);
        $addressStr = $neighborhood->name . ' (' . $neighborhood->city->name . ')';

        $order = Order::create([
            'client_id' => Auth::id(),
            'prestataire_id' => $service->user_id,
            'prestation_id' => $service->id,
            'amount' => $service->price,
            'status' => 'pending',
            'scheduled_at' => $request->date_intervention,
            'address' => $addressStr,
            'client_notes' => $request->instructions
        ]);

        // Notification au prestataire
        if ($service->user) {
            $clientName = Auth::user()->name;
            $service->user->pushNotification(
                'Nouvelle commande ! 💼',
                "$clientName a réservé votre service '{$service->title}'.",
                'success',
                route('prestataire.orders.index')
            );
        }

        return redirect()->route('client.bookings')->with('success', 'Commande validée avec succès ! Le prestataire a été notifié.');
    }

    public function receipt(Order $order)
    {
        // Check if the order belongs to the user
        if ($order->client_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['client', 'prestataire', 'prestation']);
        
        return view('shared.receipt', compact('order'));
    }
}
