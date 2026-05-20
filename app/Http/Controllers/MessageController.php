<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
    // afficher tous les messages
    public function index()
    {
        return Message::all();
    }

    // envoyer un message
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required',
            'contenu' => 'required',
        ]);

        $message = Message::create([
            'sender_id' => 1,
            'receiver_id' => $request->receiver_id,
            'contenu' => $request->contenu,
        ]);

        return response()->json([
            'message' => 'Message envoyé avec succès',
            'data' => $message
        ]);
    }
}
