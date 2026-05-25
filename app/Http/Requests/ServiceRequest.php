<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    // Vérifie si l'utilisateur est autorisé
    public function authorize(): bool
    {
        // Autoriser temporairement les requêtes sans token pour les tests locaux
        return true;
    }

    // Règles de validation
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            // Permettre l'envoi optionnel de `user_id` pour tester sans authentification
            'user_id' => $isUpdate ? 'sometimes|exists:users,id' : 'sometimes|exists:users,id',
            'categorie_id' => $isUpdate ? 'sometimes|exists:categories,id' : 'required|exists:categories,id',
            'titre' => $isUpdate ? 'sometimes|string|max:255' : 'required|string|max:255',
            'description' => $isUpdate ? 'sometimes|string' : 'required|string',
            'tarif' => $isUpdate ? 'sometimes|numeric|min:0' : 'required|numeric|min:0',

            // Validation des photos
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'disponibilite' => 'sometimes|boolean',
            'statut' => 'sometimes|in:active,inactive,archived',
        ];
    }

    // Messages personnalisés (optionnel)
    public function messages(): array
    {
        return [
            'categorie_id.required' => 'La catégorie est obligatoire.',
            'categorie_id.exists' => 'La catégorie sélectionnée est invalide.',

            'titre.required' => 'Le titre est obligatoire.',
            'titre.max' => 'Le titre ne doit pas dépasser 255 caractères.',

            'description.required' => 'La description est obligatoire.',

            'tarif.required' => 'Le tarif est obligatoire.',
            'tarif.numeric' => 'Le tarif doit être un nombre.',
            'tarif.min' => 'Le tarif doit être supérieur ou égal à 0.',

            'photos.array' => 'Les photos doivent être sous forme de tableau.',
            'photos.*.image' => 'Chaque fichier doit être une image.',
            'photos.*.mimes' => 'Formats autorisés : jpeg, png, jpg, gif.',
            'photos.*.max' => 'Chaque image ne doit pas dépasser 2 Mo.',
        ];
    }
}
    
