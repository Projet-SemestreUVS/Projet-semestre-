<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
         'user_id',
        'categorie_id',
        'titre',
        'description',
        'tarif',
        'photos',
        'disponibilite',
        'statut',
    ];

    protected $casts = [
        'photos' => 'array',
        'disponibilite' => 'boolean',
        'tarif' => 'decimal:2',
    ];

    // Relations
    public function prestataire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function avis(): HasMany
    {
        return $this->hasMany(Avis::class, 'service_id'); // ajuste selon ton schéma
    }

    // Accesseur pour l'URL complète des photos
    public function getPhotosUrlAttribute(): array
    {
        if (empty($this->photos)) {
            return [];
        }

        return array_map(function ($photo) {
            return asset('storage/services/' . $photo);
        }, $this->photos);
    }
}