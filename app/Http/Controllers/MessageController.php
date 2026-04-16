<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Affiche l'interface de messagerie avec la liste des conversations récentes
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $withUid = $request->query('with');
        
        // Trouver tous les IDs avec lesquels l'utilisateur a discuté
        $sentTo = Message::where('sender_id', $user->id)->pluck('receiver_id');
        $receivedFrom = Message::where('receiver_id', $user->id)->pluck('sender_id');
        
        $contactIds = $sentTo->merge($receivedFrom)->unique();
        
        // Si on force une discussion avec quelqu'un qui n'est pas dans l'historique
        if ($withUid && !$contactIds->contains($withUid)) {
            $contactIds->push($withUid);
        }

        // Récupérer les utilisateurs
        $contacts = User::whereIn('id', $contactIds)->get();
        
        $conversations = [];
        $first = null;

        foreach ($contacts as $contact) {
            // Dernier message entre $user et $contact
            $lastMsg = Message::where(function($q) use ($user, $contact) {
                $q->where('sender_id', $user->id)->where('receiver_id', $contact->id);
            })->orWhere(function($q) use ($user, $contact) {
                $q->where('sender_id', $contact->id)->where('receiver_id', $user->id);
            })->orderBy('created_at', 'desc')->first();

            $conv = [
                'id_utilisateur' => $contact->id,
                'nom_utilisateur' => $contact->name,
                'prenom_utilisateur' => '',
                'photo_profil' => $contact->avatar ? asset('storage/'.$contact->avatar) : null,
                'last_date' => $lastMsg ? $lastMsg->created_at : now(),
                'last_message' => $lastMsg ? $lastMsg->content : 'Démarrez la conversation !'
            ];
            $conversations[] = $conv;

            if ($withUid && $contact->id == $withUid) {
                $first = $conv;
            }
        }

        // Trier par date du dernier message (récent en haut)
        usort($conversations, function($a, $b) {
            return $b['last_date'] <=> $a['last_date'];
        });

        if (!$first && count($conversations) > 0) {
            $first = $conversations[0];
        }

        return view('client.messages', compact('conversations', 'first'));
    }

    /**
     * API: Charge l'historique de la conversation avec un utilisateur spécifique
     */
    public function history($userId)
    {
        $myId = Auth::id();
        
        // Marquer les messages reçus comme lus
        Message::where('sender_id', $userId)
               ->where('receiver_id', $myId)
               ->where('is_read', false)
               ->update(['is_read' => true]);

        $messages = Message::where(function($q) use ($myId, $userId) {
                $q->where('sender_id', $myId)->where('receiver_id', $userId);
            })->orWhere(function($q) use ($myId, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $myId);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) use ($myId) {
                return [
                    'id' => $msg->id,
                    'is_mine' => $msg->sender_id === $myId,
                    'content' => $msg->content,
                    'created_at' => $msg->created_at->format('H:i')
                ];
            });

        return response()->json($messages);
    }

    /**
     * API: Sauvegarde un nouveau message
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string|max:2000'
        ]);

        $msg = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
            'is_read' => false
        ]);

        return response()->json([
            'status' => 'success',
            'message' => [
                'id' => $msg->id,
                'is_mine' => true,
                'content' => $msg->content,
                'created_at' => $msg->created_at->format('H:i')
            ]
        ]);
    }
}
