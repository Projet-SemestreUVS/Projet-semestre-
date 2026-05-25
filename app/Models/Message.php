<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Message extends Model
{
    /**
     * Les attributs autorisés
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'contenu',
    ];

    /**
     * Relation avec l'expéditeur
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relation avec le destinataire
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}