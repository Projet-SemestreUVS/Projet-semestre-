<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;

class User extends Authenticatable implements MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, MustVerifyEmailTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'email_verified_at',
        'password',
        'role',
        'telephone',
        'photo',
        'localisation'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /* Accessors / helpers */
    public function getFullNameAttribute()
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPrestataire()
    {
        return $this->role === 'prestataire';
    }

    public function isDemandeur()
    {
        return $this->role === 'demandeur';
    }

    /* Relations */
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
