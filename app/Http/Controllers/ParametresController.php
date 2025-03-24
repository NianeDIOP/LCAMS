<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Etablissement;
use App\Models\Niveau;
use App\Models\Classe;
use Illuminate\Support\Facades\DB;

class ParametresController extends Controller
{
    public function index()
    {
        // Récupérer les informations de l'établissement (s'il existe)
        $etablissement = Etablissement::first();
        
        // Récupérer les années scolaires disponibles
        $anneesScolaires = ['2023-2024', '2024-2025', '2025-2026', '2026-2027'];
        
        return view('parametres.index', compact('etablissement', 'anneesScolaires'));
    }
    
    public function saveEtablissement(Request $request)
    {
        // Valider les données
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'academie' => 'nullable|string|max:255',
            'ief' => 'nullable|string|max:255',
            'annee_scolaire' => 'required|string|max:10',
        ]);
        
        // Créer ou mettre à jour l'établissement
        $etablissement = Etablissement::first();
        
        if ($etablissement) {
            $etablissement->update($validated);
        } else {
            Etablissement::create($validated);
        }
        
        // Gérer le logo si fourni
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = 'logo.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('images'), $logoName);
            
            $etablissement = Etablissement::first();
            $etablissement->logo = 'images/' . $logoName;
            $etablissement->save();
        }
        
        return redirect()->route('parametres.index')
            ->with('success', 'Informations de l\'établissement mises à jour avec succès.');
    }
    
    public function niveaux()
    {
        // Récupérer tous les niveaux
        $niveaux = Niveau::orderBy('ordre')->get();
        
        return view('parametres.niveaux', compact('niveaux'));
    }
    
    public function saveNiveau(Request $request)
    {
        // Valider les données
        $validated = $request->validate([
            'code' => 'required|string|max:10',
            'nom' => 'required|string|max:255',
            'cycle' => 'required|string|max:255',
            'ordre' => 'required|integer',
        ]);
        
        // Créer ou mettre à jour le niveau
        if ($request->id) {
            $niveau = Niveau::findOrFail($request->id);
            $niveau->update($validated);
            $message = 'Niveau mis à jour avec succès.';
        } else {
            Niveau::create($validated);
            $message = 'Niveau créé avec succès.';
        }
        
        return redirect()->route('parametres.niveaux')
            ->with('success', $message);
    }
    
    public function deleteNiveau($id)
    {
        // Vérifier s'il y a des classes associées à ce niveau
        $classeCount = Classe::where('niveau_id', $id)->count();
        
        if ($classeCount > 0) {
            return redirect()->route('parametres.niveaux')
                ->with('error', 'Impossible de supprimer ce niveau car il est associé à des classes.');
        }
        
        // Supprimer le niveau
        $niveau = Niveau::findOrFail($id);
        $niveau->delete();
        
        return redirect()->route('parametres.niveaux')
            ->with('success', 'Niveau supprimé avec succès.');
    }
    
    public function classes()
    {
        // Récupérer tous les niveaux pour le menu déroulant
        $niveaux = Niveau::orderBy('ordre')->get();
        
        // Récupérer toutes les classes avec leurs niveaux
        $classes = Classe::with('niveau')->get();
        
        // Récupérer l'année scolaire active
        $etablissement = Etablissement::first();
        $anneeScolaire = $etablissement ? $etablissement->annee_scolaire : null;
        
        return view('parametres.classes', compact('niveaux', 'classes', 'anneeScolaire'));
    }
    
    public function saveClasse(Request $request)
    {
        // Valider les données
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'niveau_id' => 'required|exists:niveaux,id',
            'effectif_total' => 'nullable|integer',
            'effectif_garcons' => 'nullable|integer',
            'effectif_filles' => 'nullable|integer',
            'active' => 'boolean',
        ]);
        
        // Récupérer l'année scolaire active
        $etablissement = Etablissement::first();
        $validated['annee_scolaire'] = $etablissement ? $etablissement->annee_scolaire : '2024-2025';
        
        // Créer ou mettre à jour la classe
        if ($request->id) {
            $classe = Classe::findOrFail($request->id);
            $classe->update($validated);
            $message = 'Classe mise à jour avec succès.';
        } else {
            Classe::create($validated);
            $message = 'Classe créée avec succès.';
        }
        
        return redirect()->route('parametres.classes')
            ->with('success', $message);
    }
    
    public function deleteClasse($id)
    {
        // Vérifier s'il y a des données associées à cette classe
        // (À implémenter avec les futures tables)
        
        // Supprimer la classe
        $classe = Classe::findOrFail($id);
        $classe->delete();
        
        return redirect()->route('parametres.classes')
            ->with('success', 'Classe supprimée avec succès.');
    }
    
    public function anneeScolaire()
    {
        // Récupérer l'établissement
        $etablissement = Etablissement::first();
        
        // Liste des années scolaires disponibles
        $anneesScolaires = ['2023-2024', '2024-2025', '2025-2026', '2026-2027'];
        
        return view('parametres.annee', compact('etablissement', 'anneesScolaires'));
    }
    
    public function saveAnneeScolaire(Request $request)
    {
        // Valider les données
        $validated = $request->validate([
            'annee_scolaire' => 'required|string|max:10',
        ]);
        
        // Mettre à jour l'année scolaire dans l'établissement
        $etablissement = Etablissement::first();
        
        if ($etablissement) {
            $etablissement->annee_scolaire = $validated['annee_scolaire'];
            $etablissement->save();
        } else {
            // Créer l'établissement s'il n'existe pas
            Etablissement::create([
                'nom' => 'Établissement scolaire',
                'annee_scolaire' => $validated['annee_scolaire'],
            ]);
        }
        
        return redirect()->route('parametres.anneeScolaire')
            ->with('success', 'Année scolaire mise à jour avec succès.');
    }
}