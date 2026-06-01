<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
    /**
     * Afficher tous les messages
     */
    public function index()
    {
        $messages = Message::all();

        return response()->json($messages);
    }

    /**
     * Envoyer un nouveau message
     */
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'sender_id' => 'required|integer',
            'receiver_id' => 'required|integer',
            'contenu' => 'required|string',
        ]);

        // Création du message
        $message = Message::create([
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'contenu' => $request->contenu,
        ]);

        return response()->json([
            'message' => 'Message envoyé avec succès',
            'data' => $message
        ], 201);
    }

    /**
     * Afficher un seul message
     */
    public function show($id)
    {
        $message = Message::findOrFail($id);

        return response()->json($message);
    }

    /**
     * Modifier un message
     */
    public function update(Request $request, $id)
    {
        // Validation des données
        $request->validate([
            'receiver_id' => 'required|integer',
            'contenu' => 'required|string',
        ]);

        // Rechercher le message
        $message = Message::findOrFail($id);

        // Mise à jour du message
        $message->update([
            'receiver_id' => $request->receiver_id,
            'contenu' => $request->contenu,
        ]);

        return response()->json([
            'message' => 'Message modifié avec succès',
            'data' => $message
        ]);
    }

    /**
     * Supprimer un message
     */
    public function destroy($id)
    {
        // Rechercher le message
        $message = Message::findOrFail($id);

        // Supprimer le message
        $message->delete();

        return response()->json([
            'message' => 'Message supprimé avec succès'
        ]);
    }
}