<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'prix',
        'duree',
        'categorie_id',
        'prestataire_id',
        'image'
    ];

    public function prestataire()
    {
        return $this->belongsTo(User::class, 'prestataire_id');
    }

    public function categorie()
    {
        return $this->belongsTo(Categori::class, 'categorie_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}