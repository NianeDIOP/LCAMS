{{-- resources/views/layouts/module.blade.php (Corrigé) --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LCAMS - @yield('title', 'Logiciel de Calcul et Analyse des Moyennes Semestrielles')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    <!-- Police Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Style personnalisé -->
    <style>
        :root {
            --primary: #0062cc;
            --primary-dark: #004c9e;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --white: #ffffff;
            --body-bg: #f5f8fa;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background-color: var(--body-bg);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Layout principal */
        .main-wrapper {
            display: flex;
            flex: 1;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.03);
        }
        
        /* Header du module */
        .module-header {
            background-color: var(--white);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding: 0.75rem 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
            width: 100%;
        }
        
        .module-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
        }
        
        .module-title i {
            margin-right: 0.5rem;
        }
        
        .school-info {
            background-color: rgba(0, 98, 204, 0.1);
            border-radius: 0.375rem;
            padding: 0.5rem 1rem;
            text-align: center;
        }
        
        .school-name {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        
        .school-year {
            font-size: 0.8rem;
            color: var(--secondary);
            margin-bottom: 0;
        }
        
        .home-link {
            text-decoration: none;
            color: var(--dark);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        
        .home-link:hover {
            color: var(--primary);
        }
        
        .home-link i {
            margin-right: 0.5rem;
        }
        
        /* Sidebar */
        .sidebar {
            width: 240px;
            min-width: 240px;
            background-color: var(--white);
            border-right: 1px solid rgba(0, 0, 0, 0.1);
            padding: 1.5rem 0;
            height: calc(100vh - 62px);
            position: sticky;
            top: 62px;
            overflow-y: auto;
            z-index: 900;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0 1rem;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            color: var(--dark);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .sidebar-menu a:hover {
            background-color: rgba(0, 98, 204, 0.05);
            color: var(--primary);
        }
        
        .sidebar-menu a.active {
            background-color: var(--primary);
            color: var(--white);
        }
        
        .sidebar-menu .icon {
            display: inline-flex;
            width: 20px;
            margin-right: 0.75rem;
            text-align: center;
        }
        
        /* Contenu principal */
        .content {
            flex: 1;
            padding: 1.5rem;
            background-color: var(--white);
            overflow-x: hidden;
        }
        
        .page-header {
            margin-bottom: 1.5rem;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .breadcrumb {
            padding: 0;
            margin-bottom: 0;
            background-color: transparent;
            font-size: 0.85rem;
        }
        
        /* Card styles */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            padding: 1rem 1.25rem;
        }
        
        /* Dashboard styles */
        .dashboard-container {
            width: 100%;
        }
        
        .dashboard-title {
            background-color: var(--primary);
            color: white;
            font-size: 1rem;
            font-weight: 600;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .filter-container {
            background-color: var(--white);
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .card-col-4 {
            grid-column: span 4;
        }
        
        .card-col-6 {
            grid-column: span 6;
        }
        
        .card-col-12 {
            grid-column: span 12;
        }
        
        .stat-card {
            background-color: var(--white);
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            height: 100%;
            overflow: hidden;
        }
        
        .stat-card-header {
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .stat-card-body {
            padding: 1rem;
        }
        
        /* Chart containers */
        .chart-container {
            width: 100%;
            height: 250px;
            position: relative;
        }
        
        /* Footer */
        .app-footer {
            background-color: var(--white);
            padding: 0.75rem 0;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            text-align: center;
            font-size: 0.8rem;
            color: var(--secondary);
        }
        
        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .main-wrapper {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                min-width: auto;
                height: auto;
                position: static;
                border-right: none;
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                padding: 1rem 0;
            }
            
            .sidebar-menu {
                display: flex;
                flex-wrap: wrap;
                padding: 0 0.5rem;
            }
            
            .sidebar-menu li {
                margin-right: 0.5rem;
                margin-bottom: 0.5rem;
            }
            
            .sidebar-menu a {
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
            }
            
            .content {
                padding: 1rem;
            }
            
            .school-info {
                display: none;
            }
            
            .cards-grid {
                grid-template-columns: repeat(6, 1fr);
            }
            
            .card-col-4, .card-col-6 {
                grid-column: span 6;
            }
        }
        
        @media (max-width: 576px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }
            
            .card-col-4, .card-col-6, .card-col-12 {
                grid-column: span 1;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Header du module -->
    <header class="module-header">
        <div class="header-container">
            <div class="module-title">
                @yield('module-title', '<i class="fas fa-chart-line me-2"></i> LCAMS')
            </div>
            
            <!-- Récupération des informations d'établissement -->
            @php
                $etablissement = DB::table('etablissements')->first();
            @endphp
            
            @if($etablissement)
            <div class="school-info">
                <p class="school-name">{{ $etablissement->nom }}</p>
                <p class="school-year">Année scolaire: {{ $etablissement->annee_scolaire }}</p>
            </div>
            @endif
            
            <a href="{{ route('home') }}" class="home-link">
                <i class="fas fa-home"></i> Accueil
            </a>
        </div>
    </header>

    <!-- Wrapper principal -->
    <div class="main-wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <ul class="sidebar-menu">
                @yield('sidebar-menu')
            </ul>
        </div>
        
        <!-- Contenu principal -->
        <main class="content">
            <div class="page-header">
                <h1 class="page-title">@yield('page-title')</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
            
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
            
            @yield('content')
        </main>
    </div>
    
    <!-- Footer -->
    <footer class="app-footer">
        <div class="footer-container">
            <p class="mb-0">LCAMS - Logiciel de Calcul et Analyse des Moyennes Semestrielles</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Scripts personnalisés -->
    @yield('scripts')
</body>
</html>