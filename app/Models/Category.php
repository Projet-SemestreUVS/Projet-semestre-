<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'icone',
        'parent_id'
    ];

    // Catégorie parent
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Sous catégories
    public function enfants()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}