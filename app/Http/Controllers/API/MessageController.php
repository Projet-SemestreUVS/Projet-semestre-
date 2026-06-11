<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Récupérer les conversations de l'utilisateur connecté
     */
    public function conversations(Request $request)
    {
        try {
            $userId = $request->user()->id;
            
            $conversations = Message::where('sender_id', $userId)
                ->orWhere('receiver_id', $userId)
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy(function ($message) use ($userId) {
                    return $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
                })
                ->map(function ($messages, $otherUserId) {
                    $lastMessage = $messages->first();
                    $otherUser = $lastMessage->sender_id == auth()->id() 
                        ? $lastMessage->receiver 
                        : $lastMessage->sender;
                    
                    return [
                        'userId' => $otherUserId,
                        'nom' => $otherUser->nom,
                        'prenom' => $otherUser->prenom,
                        'role' => $otherUser->role,
                        'dernierMessage' => $lastMessage->contenu,
                        'dernierMessageDate' => $lastMessage->created_at,
                        'nonLu' => $messages->where('receiver_id', auth()->id())->where('lu', false)->count(),
                    ];
                })
                ->values();
            
            return response()->json([
                'success' => true,
                'data' => $conversations
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Récupérer les messages avec un utilisateur spécifique
     */
    public function messagesWithUser(Request $request, $userId)
    {
        try {
            $currentUserId = $request->user()->id;
            
            $messages = Message::where(function ($query) use ($currentUserId, $userId) {
                $query->where('sender_id', $currentUserId)
                    ->where('receiver_id', $userId);
            })->orWhere(function ($query) use ($currentUserId, $userId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', $currentUserId);
            })->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();
            
            // Marquer les messages comme lus
            Message::where('sender_id', $userId)
                ->where('receiver_id', $currentUserId)
                ->where('lu', false)
                ->update(['lu' => true]);
            
            return response()->json([
                'success' => true,
                'data' => $messages
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Envoyer un message
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'receiver_id' => 'required|exists:users,id',
                'contenu' => 'required|string|max:1000',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $message = Message::create([
                'sender_id' => $request->user()->id,
                'receiver_id' => $request->receiver_id,
                'contenu' => $request->contenu,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Message envoyé',
                'data' => $message->load(['sender', 'receiver'])
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}