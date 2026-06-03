<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Reservation;
use App\Models\User;

class Avis extends Model
{
    protected $table = 'avis';

    protected $fillable = [
        'reservation_id',
        'auteur_id',
        'cible_id',
        'note',
        'commentaire',
        'signale'
    ];

    // Relation avec la réservation
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    // Auteur de l'avis
    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }

    // Utilisateur ciblé
    public function cible()
    {
        return $this->belongsTo(User::class, 'cible_id');
    }
}