<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil
     */
    public function index()
    {
        $configuration = Configuration::first();
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        
        return view('home', compact('configuration', 'anneeScolaireActive'));
    }
}