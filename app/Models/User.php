<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasApiTokens, HasFactory, Notifiable, MustVerifyEmailTrait;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'email_verified_at',  // CORRECTION: Ajouté dans fillable
        'password',
        'role',
        'telephone',
        'photo',
        'localisation'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',  // CORRECTION: Laravel 10+ utilise 'hashed' au lieu de 'hash'
    ];

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */
    
    // CORRECTION: Ajout d'un accesseur pour le nom complet
    public function getFullNameAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }
    
    // CORRECTION: Vérifier si l'utilisateur est admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    
    // CORRECTION: Vérifier si l'utilisateur est prestataire
    public function isPrestataire()
    {
        return $this->role === 'prestataire';
    }
    
    // CORRECTION: Vérifier si l'utilisateur est demandeur
    public function isDemandeur()
    {
        return $this->role === 'demandeur';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function reservationsDemandeur()
    {
        return $this->hasMany(Reservation::class, 'demandeur_id');
    }

    public function reservationsPrestataire()
    {
        return $this->hasMany(Reservation::class, 'prestataire_id');
    }

    public function avisAuteur()
    {
        return $this->hasMany(Avis::class, 'auteur_id');
    }

    public function avisCible()
    {
        return $this->hasMany(Avis::class, 'cible_id');
    }

    public function messagesEnvoyes()
    {
        return $this->hasMany(Message::class, 'expediteur_id');
    }

    public function messagesRecus()
    {
        return $this->hasMany(Message::class, 'destinataire_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}