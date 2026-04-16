<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ProviderController extends Controller
{
    public function index()
    {
        // On récupère uniquement les prestataires avec leurs prestations (pour compter le nb de prestations)
        $prestataires = User::where('role', 'prestataire')
            ->with('city')
            ->withCount('prestations')
            ->latest()
            ->get();
            
        return view('admin.providers.index', compact('prestataires'));
    }

    public function validateProvider(Request $request, User $provider)
    {
        if ($provider->role !== 'prestataire') {
            return back()->with('error', 'Cet utilisateur n\'est pas un prestataire.');
        }

        $provider->update(['is_verified' => true, 'is_active' => true]);

        $provider->pushNotification(
            'Compte Prestataire Validé 🎉',
            'Félicitations ! Votre compte de prestataire a été validé par l\'administration. Vous pouvez désormais recevoir des commandes.',
            'success',
            route('prestataire.dashboard')
        );

        return back()->with('success', '✅ Le prestataire a été validé avec succès.');
    }

    public function rejectProvider(Request $request, User $provider)
    {
        if ($provider->role !== 'prestataire') {
            return back()->with('error', 'Cet utilisateur n\'est pas un prestataire.');
        }

        // On peut soit le supprimer, soit simplement le garder is_verified = false, 
        // ou le remettre en 'client' selon la logique du projet. L'ancienne version PHP semblait le bloquer.
        $provider->update(['is_verified' => false, 'is_active' => false]);

        $provider->pushNotification(
            'Demande refusée ❌',
            'Votre demande pour devenir prestataire a malheureusement été refusée ou votre compte a été suspendu.',
            'danger',
            '#'
        );

        return back()->with('success', '❌ La demande du prestataire a été refusée.');
    }
}
