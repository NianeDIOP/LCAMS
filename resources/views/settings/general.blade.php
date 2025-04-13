@extends('settings.layout')

@section('title', 'Paramètres Généraux')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-school me-2"></i> Informations de l'établissement
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.save_general') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="school_name" class="form-label">Nom de l'établissement <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('school_name') is-invalid @enderror" id="school_name" name="school_name" value="{{ old('school_name', $settings->school_name ?? '') }}" required>
                            @error('school_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Numéro de téléphone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $settings->phone ?? '') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Adresse</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $settings->address ?? '') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="current_school_year" class="form-label">Année scolaire en cours <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('current_school_year') is-invalid @enderror" id="current_school_year" name="current_school_year" placeholder="Ex: 2024-2025" value="{{ old('current_school_year', $settings->current_school_year ?? '') }}" required>
                            @error('current_school_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection