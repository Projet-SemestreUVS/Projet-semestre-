<?php
// app/Http/Controllers/API/StatistiqueController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Vérifier que l'utilisateur est admin
            if ($request->user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            // Statistiques générales
            $total_users = User::count();
            $total_prestataires = User::where('role', 'prestataire')->count();
            $total_demandeurs = User::where('role', 'demandeur')->count();
            $total_services = Service::count();
            $total_reservations = Reservation::count();
            $total_revenus = Reservation::where('statut', 'termine')->sum('montant_total');

            // Réservations par mois (6 derniers mois)
            $reservations_par_mois = Reservation::select(
                DB::raw('DATE_FORMAT(created_at, "%b") as mois'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('mois')
            ->orderBy('created_at')
            ->get();

            // Revenus par mois (6 derniers mois)
            $revenus_par_mois = Reservation::select(
                DB::raw('DATE_FORMAT(created_at, "%b") as mois'),
                DB::raw('SUM(montant_total) as total')
            )
            ->where('statut', 'termine')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('mois')
            ->orderBy('created_at')
            ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_users' => $total_users,
                    'total_prestataires' => $total_prestataires,
                    'total_demandeurs' => $total_demandeurs,
                    'total_services' => $total_services,
                    'total_reservations' => $total_reservations,
                    'total_revenus' => $total_revenus,
                    'reservations_par_mois' => $reservations_par_mois,
                    'revenus_par_mois' => $revenus_par_mois,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}