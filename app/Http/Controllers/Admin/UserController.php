<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('city')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $data = [];

        // Validation / vérification
        if ($request->has('is_verified')) {
            $data['is_verified'] = (bool) $request->is_verified;
        }
        if ($request->has('is_active')) {
            $data['is_active'] = (bool) $request->is_active;
        }

        $user->update($data);

        $msg = $request->has('is_verified')
            ? ($data['is_verified'] ? '✅ Prestataire validé !' : '❌ Prestataire refusé.')
            : ($data['is_active'] ? '✅ Compte réactivé !' : '⚠️ Compte suspendu.');

        if ($request->has('is_active')) {
            $statusMsg = $data['is_active'] 
                ? 'Votre compte a été réactivé par l\'administration. Bon retour !' 
                : 'Votre compte a été temporairement suspendu par l\'administration.';
            $type = $data['is_active'] ? 'success' : 'danger';
            
            $user->pushNotification(
                'Statut du compte 🛡️',
                $statusMsg,
                $type,
                '#'
            );
        }

        return back()->with('success', $msg);
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Impossible de supprimer un administrateur.');
        }

        $user->delete();
        return back()->with('success', '🗑️ Utilisateur supprimé.');
    }
}
