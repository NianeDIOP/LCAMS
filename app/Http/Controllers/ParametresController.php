<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\AnneeScolaire;
use App\Models\Niveau;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParametresController extends Controller
{
    /**
     * Affiche la page de configuration
     */
    public function index()
    {
        $configuration = Configuration::first();
        $anneeScolaires = AnneeScolaire::orderBy('libelle', 'desc')->get();
        $niveaux = Niveau::orderBy('libelle')->get();
        
        return view('parametres.index', compact('configuration', 'anneeScolaires', 'niveaux'));
    }
    
    /**
     * Enregistre la configuration de l'établissement
     */
    public function saveConfiguration(Request $request)
    {
        $validated = $request->validate([
            'nom_etablissement' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'inspection_academie' => 'nullable|string|max:255',
            'inspection_education_formation' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $configuration = Configuration::first();
        if (!$configuration) {
            $configuration = new Configuration();
        }
        
        $configuration->nom_etablissement = $validated['nom_etablissement'];
        $configuration->adresse = $validated['adresse'];
        $configuration->telephone = $validated['telephone'];
        $configuration->inspection_academie = $validated['inspection_academie'];
        $configuration->inspection_education_formation = $validated['inspection_education_formation'];
        
        if ($request->hasFile('logo')) {
            if ($configuration->logo_path) {
                Storage::delete($configuration->logo_path);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
            $configuration->logo_path = $logoPath;
        }
        
        $configuration->save();
        
        return redirect()->route('parametres.index')->with('success', 'Configuration enregistrée avec succès.');
    }
    
    /**
     * Ajoute une nouvelle année scolaire
     */
    public function saveAnneeScolaire(Request $request)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255|unique:annee_scolaires,libelle',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'active' => 'nullable|boolean',
        ]);
        
        // Si une nouvelle année est active, désactiver les autres
        if (isset($validated['active']) && $validated['active']) {
            AnneeScolaire::where('active', true)->update(['active' => false]);
        }
        
        AnneeScolaire::create($validated);
        
        return redirect()->route('parametres.index')->with('success', 'Année scolaire ajoutée avec succès.');
    }
    
    /**
     * Met à jour une année scolaire existante
     */
    public function updateAnneeScolaire(Request $request, $id)
    {
        $anneeScolaire = AnneeScolaire::findOrFail($id);
        
        $validated = $request->validate([
            'libelle' => 'required|string|max:255|unique:annee_scolaires,libelle,'.$id,
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'active' => 'nullable|boolean',
        ]);
        
        // Si une nouvelle année est active, désactiver les autres
        if (isset($validated['active']) && $validated['active']) {
            AnneeScolaire::where('id', '!=', $id)->where('active', true)->update(['active' => false]);
        }
        
        $anneeScolaire->update($validated);
        
        return redirect()->route('parametres.index')->with('success', 'Année scolaire mise à jour avec succès.');
    }
    
    /**
     * Ajoute un nouveau niveau
     */
    public function saveNiveau(Request $request)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'actif' => 'nullable|boolean',
        ]);
        
        Niveau::create($validated);
        
        return redirect()->route('parametres.index')->with('success', 'Niveau ajouté avec succès.');
    }
    
    /**
     * Met à jour un niveau existant
     */
    public function updateNiveau(Request $request, $id)
    {
        $niveau = Niveau::findOrFail($id);
        
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'actif' => 'nullable|boolean',
        ]);
        
        $niveau->update($validated);
        
        return redirect()->route('parametres.index')->with('success', 'Niveau mis à jour avec succès.');
    }
    
    /**
     * Affiche les classes pour un niveau spécifique
     */
    public function getClasses($niveau_id)
    {
        $niveau = Niveau::findOrFail($niveau_id);
        $classes = $niveau->classes;
        
        return view('parametres.classes', compact('niveau', 'classes'));
    }
    
    /**
     * Ajoute une nouvelle classe
     */
    public function saveClasse(Request $request)
    {
        $validated = $request->validate([
            'niveau_id' => 'required|exists:niveaux,id',
            'libelle' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'effectif' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
        ]);
        
        Classe::create($validated);
        
        return redirect()->route('parametres.classes', $validated['niveau_id'])->with('success', 'Classe ajoutée avec succès.');
    }
    
    /**
     * Met à jour une classe existante
     */
    public function updateClasse(Request $request, $id)
    {
        $classe = Classe::findOrFail($id);
        
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'effectif' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
        ]);
        
        $classe->update($validated);
        
        return redirect()->route('parametres.classes', $classe->niveau_id)->with('success', 'Classe mise à jour avec succès.');
    }
}