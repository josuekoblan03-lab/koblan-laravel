<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prestation;

class PrestationController extends Controller
{
    public function index()
    {
        $prestations = Prestation::with(['user', 'serviceType.category'])
            ->withTrashed(false)
            ->latest()
            ->get();

        return view('admin.services.prestations', compact('prestations'));
    }

    public function approve(Prestation $prestation)
    {
        $prestation->update(['status' => 'active']);

        // Notify the provider
        $prestation->user?->pushNotification(
            '✅ Prestation approuvée !',
            "Votre prestation \"{$prestation->title}\" a été approuvée et est maintenant visible sur la plateforme.",
            'success',
            '/prestataire/services'
        );

        return back()->with('success', "✅ Prestation \"{$prestation->title}\" approuvée avec succès !");
    }

    public function reject(Prestation $prestation)
    {
        $prestation->update(['status' => 'rejected']);

        // Notify the provider
        $prestation->user?->pushNotification(
            '❌ Prestation refusée',
            "Votre prestation \"{$prestation->title}\" a été refusée. Contactez l'administration pour plus d'informations.",
            'error',
            '/prestataire/services'
        );

        return back()->with('success', "Prestation \"{$prestation->title}\" refusée.");
    }
}
