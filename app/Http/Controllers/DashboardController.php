<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function stats()
    {

        return response()->json([

            "total" => User::count(),

            "admins" =>
                User::where('role','admin')->count(),

            "prestataires" =>
                User::where('role','prestataire')->count(),

            "demandeurs" =>
                User::where('role','demandeur')->count(),

        ]);

    }


}