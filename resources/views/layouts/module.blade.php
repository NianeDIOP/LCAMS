<!-- resources/views/layouts/module.blade.php -->
@extends('layouts.main')

@section('content')
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar" style="width: 250px; min-width: 250px; background-color: white; border-right: 1px solid rgba(0, 0, 0, 0.1); height: calc(100vh - 56px);">
        <!-- Titre du module -->
        <div class="module-header p-3 border-bottom bg-light">
            <h5 class="m-0 d-flex align-items-center">
                @yield('module-icon', '<i class="fas fa-chart-line me-2"></i>')
                @yield('module-title', 'LCAMS')
            </h5>
        </div>
        
        <!-- Menu du module -->
        <nav class="py-2">
            <ul class="sidebar-menu" style="list-style: none; padding: 0; margin: 0;">
                @yield('sidebar-menu')
            </ul>
        </nav>
    </div>

    <!-- Contenu du module -->
    <div class="content p-4" style="flex: 1; overflow-x: auto;">
        <!-- En-tête de page -->
        <div class="page-header mb-4">
            <h1 class="h4 fw-bold mb-2">@yield('page-title', 'Tableau de bord')</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        
        <!-- Messages de notification -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        <!-- Contenu principal -->
        @yield('module-content')
    </div>
</div>

<style>
    /* Styles améliorés pour la sidebar */
    .sidebar-menu li {
        margin-bottom: 5px;
    }
    
    .sidebar-menu a {
        padding: 12px 15px;
        display: block;
        color: #343a40;
        text-decoration: none;
        border-radius: 4px;
        margin: 0 8px;
        font-size: 0.95rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .sidebar-menu a:hover {
        background-color: #f8f9fa;
        color: #0062cc;
    }
    
    .sidebar-menu a.active {
        background-color: #e9f2ff;
        color: #0062cc;
        font-weight: 600;
    }
    
    .sidebar-menu i {
        width: 24px;
        text-align: center;
        margin-right: 8px;
    }
    
    /* Styles généraux pour le contenu */
    .content {
        background-color: #f8f9fa;
    }
    
    .page-header {
        background-color: #fff;
        padding: 15px;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    /* Styles pour les cartes */
    .card {
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: none;
        border-radius: 4px;
    }
    
    .card-header {
        background-color: #f8f9fa;
        font-weight: 600;
        padding: 12px 15px;
    }
    
    /* Styles pour les boutons */
    .btn-primary {
        background-color: #0062cc;
    }
    
    .btn-success {
        background-color: #00994d;
    }
    
    /* Styles pour les tableaux */
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f0f7ff;
    }
    
    /* Styles pour les notifications */
    .alert {
        border-left-width: 4px;
    }
    
    .alert-success {
        border-left-color: #00994d;
    }
    
    .alert-danger {
        border-left-color: #dc3545;
    }
</style>
@endsection