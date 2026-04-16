<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order)
    {
        if ($order->client_id !== Auth::id() || $order->status !== 'completed') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'prestation_id' => 'required|exists:prestations,id'
        ]);

        $prestation = $order->prestations()->where('prestation_id', $request->prestation_id)->first();
        if (!$prestation) {
            abort(404, 'Service not found in this order.');
        }

        Review::create([
            'client_id' => Auth::id(),
            'prestation_id' => $request->prestation_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => true // Default to true based on old system, or admin approval
        ]);

        return redirect()->back()->with('success', 'Avis ajouté avec succès !');
    }
}
