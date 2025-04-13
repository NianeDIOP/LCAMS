<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Afficher la page des paramètres généraux.
     */
    public function index()
    {
        $settings = SchoolSetting::first() ?? new SchoolSetting();
        return view('settings.general', compact('settings'));
    }

    /**
     * Enregistrer les paramètres généraux.
     */
    public function saveGeneralSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'current_school_year' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->route('settings.index')
                ->withErrors($validator)
                ->withInput();
        }

        $settings = SchoolSetting::first();
        
        if (!$settings) {
            $settings = new SchoolSetting();
        }
        
        $settings->school_name = $request->school_name;
        $settings->phone = $request->phone;
        $settings->address = $request->address;
        $settings->current_school_year = $request->current_school_year;
        $settings->save();

        return redirect()->route('settings.index')
            ->with('success', 'Les paramètres généraux ont été enregistrés avec succès.');
    }

    /**
     * Afficher la page de gestion des niveaux scolaires.
     */
    public function gradeLevels()
    {
        $gradeLevels = GradeLevel::orderBy('order')->get();
        return view('settings.grade_levels', compact('gradeLevels'));
    }

    /**
     * Enregistrer un nouveau niveau scolaire.
     */
    public function saveGradeLevel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->route('settings.grade_levels')
                ->withErrors($validator)
                ->withInput();
        }

        GradeLevel::create([
            'name' => $request->name,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'active' => true,
        ]);

        return redirect()->route('settings.grade_levels')
            ->with('success', 'Le niveau scolaire a été ajouté avec succès.');
    }

    /**
     * Mettre à jour un niveau scolaire.
     */
    public function updateGradeLevel(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('settings.grade_levels')
                ->withErrors($validator)
                ->withInput();
        }

        $gradeLevel = GradeLevel::findOrFail($id);
        $gradeLevel->update([
            'name' => $request->name,
            'description' => $request->description,
            'order' => $request->order ?? $gradeLevel->order,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('settings.grade_levels')
            ->with('success', 'Le niveau scolaire a été mis à jour avec succès.');
    }

    /**
     * Supprimer un niveau scolaire.
     */
    public function deleteGradeLevel($id)
    {
        $gradeLevel = GradeLevel::findOrFail($id);
        $gradeLevel->delete();

        return redirect()->route('settings.grade_levels')
            ->with('success', 'Le niveau scolaire a été supprimé avec succès.');
    }

    /**
     * Afficher la page des classes d'un niveau.
     */
    public function classrooms($gradeLevelId)
    {
        $gradeLevel = GradeLevel::with('classrooms')->findOrFail($gradeLevelId);
        return view('settings.classrooms', compact('gradeLevel'));
    }

    /**
     * Enregistrer une nouvelle classe.
     */
    public function saveClassroom(Request $request, $gradeLevelId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->route('settings.classrooms', $gradeLevelId)
                ->withErrors($validator)
                ->withInput();
        }

        $gradeLevel = GradeLevel::findOrFail($gradeLevelId);
        
        Classroom::create([
            'name' => $request->name,
            'grade_level_id' => $gradeLevel->id,
            'active' => true,
        ]);

        return redirect()->route('settings.classrooms', $gradeLevelId)
            ->with('success', 'La classe a été ajoutée avec succès.');
    }

    /**
     * Mettre à jour une classe.
     */
    public function updateClassroom(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            $classroom = Classroom::findOrFail($id);
            return redirect()->route('settings.classrooms', $classroom->grade_level_id)
                ->withErrors($validator)
                ->withInput();
        }

        $classroom = Classroom::findOrFail($id);
        $classroom->update([
            'name' => $request->name,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('settings.classrooms', $classroom->grade_level_id)
            ->with('success', 'La classe a été mise à jour avec succès.');
    }

    /**
     * Supprimer une classe.
     */
    public function deleteClassroom($id)
    {
        $classroom = Classroom::findOrFail($id);
        $gradeLevelId = $classroom->grade_level_id;
        $classroom->delete();

        return redirect()->route('settings.classrooms', $gradeLevelId)
            ->with('success', 'La classe a été supprimée avec succès.');
    }
}