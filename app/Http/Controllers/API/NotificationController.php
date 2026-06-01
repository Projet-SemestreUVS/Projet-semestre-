<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{

    public function index()
    {

        return response()->json(
            Notification::with('user')->latest()->get()
        );
    }

    public function store(Request $request)
    {

        $notification = Notification::create([

            'user_id' => $request->user_id,

            'type' => $request->type,

            'contenu' => $request->contenu,

            'lu' => false
        ]);

        return response()->json([
            'message' => 'Notification créée',
            'notification' => $notification
        ], 201);
    }

    public function show($id)
    {

        return response()->json(
            Notification::findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {

        $notification = Notification::findOrFail($id);

        $notification->update($request->all());

        return response()->json([
            'message' => 'Notification modifiée',
            'notification' => $notification
        ]);
    }

    public function destroy($id)
    {

        Notification::destroy($id);

        return response()->json([
            'message' => 'Notification supprimée'
        ]);
    }
}
