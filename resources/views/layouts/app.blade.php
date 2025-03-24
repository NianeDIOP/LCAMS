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
            --card-border-radius: 0.5rem;
            --btn-border-radius: 0.25rem;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background-color: var(--body-bg);
        }
        
        /* Header et navigation */
        .navbar {
            padding: 0.6rem 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.075);
            background-color: var(--white);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary);
        }
        
        .navbar-nav .nav-link {
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.75rem 1rem;
            color: var(--dark);
            transition: all 0.2s ease;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--primary);
            background-color: rgba(0, 98, 204, 0.05);
        }
        
        .navbar-nav .nav-link.active {
            color: var(--primary);
            font-weight: 600;
            border-bottom: 2px solid var(--primary);
        }
        
        /* Cards et conteneurs */
        .card {
            border: none;
            border-radius: var(--card-border-radius);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card:hover {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            font-size: 1rem;
            padding: 1rem 1.25rem;
        }
        
        /* Boutons et actions */
        .btn {
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: var(--btn-border-radius);
            transition: all 0.2s;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        /* Section d'en-tête de page */
        .page-header {
            background-color: var(--white);
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--dark);
        }
        
        .page-subtitle {
            font-size: 0.95rem;
            color: var(--secondary);
        }
        
        /* Footer */
        .footer {
            background-color: var(--white);
            padding: 1.5rem 0;
            margin-top: 3rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            font-size: 0.85rem;
        }
        
        /* Utilities */
        .shadow-sm {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05) !important;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            color: var(--dark);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar-nav .nav-link.active {
                border-bottom: none;
                background-color: rgba(0, 98, 204, 0.08);
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- En-tête -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <i class="fas fa-chart-line me-2 text-primary"></i>LCAMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('semestre1.*') ? 'active' : '' }}" href="{{ route('semestre1.index') }}">
                            <i class="fas fa-calendar-alt me-1"></i> Semestre 1
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('semestre2.*') ? 'active' : '' }}" href="{{ route('semestre2.index') }}">
                            <i class="fas fa-calendar-check me-1"></i> Semestre 2
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('general.*') ? 'active' : '' }}" href="{{ route('general.index') }}">
                            <i class="fas fa-chart-pie me-1"></i> Général
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('parametres.*') ? 'active' : '' }}" href="{{ route('parametres.index') }}">
                            <i class="fas fa-cog me-1"></i> Paramètres
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <main>
        @yield('content')
    </main>

    <!-- Pied de page -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">LCAMS - Logiciel de Calcul et Analyse des Moyennes Semestrielles</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; {{ date('Y') }} - Tous droits réservés</p>
                </div>
            </div>
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