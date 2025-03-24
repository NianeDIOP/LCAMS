@extends('layouts.app')

@section('title', 'Paramètres')

@section('styles')
<style>
    /* Styles spécifiques à la page paramètres */
    .page-header {
        background: linear-gradient(120deg, #f5f7fa, #e4e9f2);
        padding: 1.5rem 0;
        margin-bottom: 1.75rem;
    }
    
    .tab-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }
    
    .nav-tabs {
        border-bottom: none;
    }
    
    .nav-tabs .nav-item {
        margin-bottom: 0;
    }
    
    .nav-tabs .nav-link {
        border: none;
        font-size: 0.875rem;
        font-weight: 500;
        padding: 0.75rem 1rem;
        color: var(--secondary);
        border-bottom: 2px solid transparent;
        border-radius: 0;
        transition: all 0.2s;
    }
    
    .nav-tabs .nav-link:hover:not(.active) {
        color: var(--dark);
        border-color: rgba(0, 0, 0, 0.1);
    }
    
    .nav-tabs .nav-link.active {
        color: var(--primary);
        border-bottom: 2px solid var(--primary);
        background-color: transparent;
    }
    
    .form-control, .form-select {
        font-size: 0.875rem;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        border-color: #dce1e6;
        box-shadow: none;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #8bb8f8;
        box-shadow: 0 0 0 0.2rem rgba(0, 98, 204, 0.15);
    }
    
    .form-label {
        font-weight: 500;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    
    .logo-container {
        width: 150px;
        height: 150px;
        border-radius: 0.5rem;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border: 1px dashed #dee2e6;
        margin: 0 auto;
    }
    
    .logo-container img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }
    
    .no-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
    }
</style>
@endsection

@section('content')
<!-- En-tête de page -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title">Paramètres</h1>
        <p class="page-subtitle text-muted">Configurez les paramètres de l'application</p>
    </div>
</div>

<div class="container">
    <!-- Messages de notification -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Onglets de navigation -->
    <div class="tab-header">
        <ul class="nav nav-tabs" id="parametresTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="etablissement-tab" data-bs-toggle="tab" data-bs-target="#etablissement" type="button" role="tab" aria-controls="etablissement" aria-selected="true">
                    <i class="fas fa-school me-2"></i>Établissement
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" href="{{ route('parametres.niveaux') }}">
                    <i class="fas fa-layer-group me-2"></i>Niveaux
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" href="{{ route('parametres.classes') }}">
                    <i class="fas fa-chalkboard me-2"></i>Classes
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" href="{{ route('parametres.annee') }}">
                    <i class="fas fa-calendar-alt me-2"></i>Année scolaire
                </a>
            </li>
        </ul>
    </div>

    <!-- Contenu des onglets -->
    <div class="tab-content" id="parametresTabContent">
        <!-- Onglet Établissement -->
        <div class="tab-pane fade show active" id="etablissement" role="tabpanel" aria-labelledby="etablissement-tab">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informations de l'établissement</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('parametres.etablissement') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-4 text-center mb-3 mb-md-0">
                                <div class="logo-container mb-3">
                                    @if(isset($etablissement) && $etablissement->logo)
                                        <img src="{{ asset($etablissement->logo) }}" alt="Logo" class="img-fluid" id="logo-preview">
                                    @else
                                        <div class="no-logo">
                                            <span class="text-muted small">Aucun logo</span>
                                        </div>
                                    @endif
                                </div>
                                <label for="logo" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-upload me-2"></i> Choisir un logo
                                </label>
                                <input type="file" id="logo" name="logo" class="d-none" accept="image/*">
                            </div>
                            
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="nom" class="form-label">Nom de l'établissement *</label>
                                        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ $etablissement->nom ?? old('nom') }}" required>
                                        @error('nom')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="academie" class="form-label">Académie</label>
                                        <input type="text" class="form-control" id="academie" name="academie" value="{{ $etablissement->academie ?? old('academie') }}">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="ief" class="form-label">IEF</label>
                                        <input type="text" class="form-control" id="ief" name="ief" value="{{ $etablissement->ief ?? old('ief') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                            <label for="adresse" class="form-label">Adresse</label>
                                <input type="text" class="form-control" id="adresse" name="adresse" value="{{ $etablissement->adresse ?? old('adresse') }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="telephone" name="telephone" value="{{ $etablissement->telephone ?? old('telephone') }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $etablissement->email ?? old('email') }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="annee_scolaire" class="form-label">Année scolaire en cours *</label>
                                <select class="form-select" id="annee_scolaire" name="annee_scolaire" required>
                                    @foreach($anneesScolaires as $annee)
                                        <option value="{{ $annee }}" {{ (isset($etablissement) && $etablissement->annee_scolaire == $annee) ? 'selected' : '' }}>
                                            {{ $annee }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Prévisualisation du logo
    document.addEventListener('DOMContentLoaded', function() {
        const logoInput = document.getElementById('logo');
        const logoPreview = document.getElementById('logo-preview');
        const noLogo = document.querySelector('.no-logo');
        
        logoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    if (logoPreview) {
                        // Mettre à jour l'image existante
                        logoPreview.src = e.target.result;
                    } else if (noLogo) {
                        // Créer une nouvelle image
                        noLogo.innerHTML = '';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Logo';
                        img.id = 'logo-preview';
                        img.className = 'img-fluid';
                        
                        noLogo.appendChild(img);
                    }
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endsection