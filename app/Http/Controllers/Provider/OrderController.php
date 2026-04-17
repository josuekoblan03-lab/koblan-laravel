<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $orders = Order::whereHas('prestation', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['client', 'prestation'])
            ->latest()
            ->paginate(15);

        return view('provider.orders', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // Ensure this provider actually has a prestation in this order
        // The order belongs to a prestation, and the prestation belongs to the user
        $hasPrestation = $order->prestation && $order->prestation->user_id === $user->id;
        if (!$hasPrestation) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:accepted,in_progress,completed,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        // Trigger Notification Logic Here based on status
        $statusLabels = [
            'accepted' => 'acceptée ✅',
            'in_progress' => 'en cours ⏳',
            'completed' => 'terminée 🎉',
            'cancelled' => 'annulée ❌'
        ];
        $statusLabel = $statusLabels[$request->status] ?? $request->status;

        if ($order->client) {
            $order->client->pushNotification(
                'Mise à jour Commande #' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                "Le prestataire {$user->name} a marqué votre commande comme $statusLabel.",
                $request->status === 'cancelled' ? 'danger' : 'info',
                route('client.bookings')
            );
        }

        return redirect()->route('prestataire.orders.index')->with('success', 'Statut de la commande mis à jour.');
    }
}
