<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Affiche la liste des notifications de l'utilisateur connecté.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupère toutes les notifications de l'utilisateur, les plus récentes en premier
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();

        return view('public.notifications', compact('notifications'));
    }

    /**
     * Marque une notification spécifique comme lue.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->update(['is_read' => true]);

        return back();
    }

    /**
     * Marque toutes les notifications comme lues.
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
