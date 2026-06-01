<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Service;
use App\Models\User;
class Reservation extends Model
{
    protected $fillable = [
        'service_id',
        'demandeur_id',
        'prestataire_id',
        'date_debut',
        'statut',
        'commentaire'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function demandeur()
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    public function prestataire()
    {
        return $this->belongsTo(User::class, 'prestataire_id');
    }
}