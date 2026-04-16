<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        
        $stats = [
            'orders'    => $user->ordersAsClient()->count(),
            'favorites' => $user->favoris()->count(),
            'messages'  => 0,
        ];

        return view('client.profile', compact('user', 'stats'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['name', 'phone', 'bio']);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('profiles', 'public');
        }

        $user->update($data);

        return redirect()->route('client.profile')->with('success', 'Profil mis à jour avec succès ! ✅');
    }

    public function upgradeToProvider(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'client') {
            $user->update([
                'role' => 'prestataire',
                'is_verified' => false
            ]);
            return redirect()->route('prestataire.dashboard')->with('success', "Félicitations ! Vous êtes passé(e) en mode Prestataire. Votre compte est en attente d'approbation par un administrateur avant que vous puissiez recevoir des commandes publiques. 🎉");
        }
        return redirect()->route('prestataire.dashboard');
    }
}
