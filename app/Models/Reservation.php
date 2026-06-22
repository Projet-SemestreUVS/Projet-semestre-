<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Service;
use App\Models\Avis;

class Reservation extends Model
{
    protected $fillable = [
        'demandeur_id',
        'prestataire_id',
        'service_id',
        'date_debut',
        'date_fin',
        'statut',
        'commentaire'
    ];

    public function demandeur()
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    public function prestataire()
    {
        return $this->belongsTo(User::class, 'prestataire_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function avis()
    {
        return $this->hasOne(Avis::class, 'reservation_id');
    }
}