<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\City;
use App\Models\Neighborhood;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        
        $stats = [
            'total_prestations' => $user->prestations->count(),
            'total_orders' => 0, // Placeholder
            'messages' => 0 // to implement
        ];

        $cities = City::with('neighborhoods')->get();

        return view('provider.profile', compact('user', 'stats', 'cities'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'city_id' => 'nullable|exists:cities,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
        ]);

        $data = $request->only(['name', 'phone', 'bio', 'city_id', 'neighborhood_id']);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('profiles', 'public');
        }

        $user->update($data);

        return redirect()->route('prestataire.profile')->with('success', 'Profil prestataire mis à jour avec succès ! ✅');
    }
}
