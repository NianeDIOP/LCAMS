<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Paramètres LCAMS</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f5f7fa;
        }

        /* Header principal plus visible */
        .main-header {
            background: linear-gradient(135deg, #3a1c71 0%, #d76d77 100%);
            color: white;
            padding: 15px 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .main-header .logo-text {
            font-weight: 700;
            margin: 0;
        }

        /* Bouton d'accueil plus grand et visible */
        .home-btn {
            padding: 8px 20px;
            font-weight: 600;
            border: 2px solid white;
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .home-btn:hover {
            background-color: white;
            color: #3a1c71;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .home-btn i {
            margin-right: 8px;
        }

        /* Menu latéral amélioré */
        .sidebar {
            background: linear-gradient(135deg, #3a1c71 0%, #d76d77 100%);
            color: white;
            min-height: 100vh;
            padding-top: 30px;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .sidebar h4 {
            font-weight: 600;
            letter-spacing: 1px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin: 0 20px 20px;
        }

        .sidebar .nav-link {
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            margin: 5px 15px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            position: relative;
        }

        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active::before {
            content: "";
            position: absolute;
            left: -15px;
            top: 50%;
            transform: translateY(-50%);
            height: 70%;
            width: 4px;
            background-color: white;
            border-radius: 0 4px 4px 0;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        /* Style pour la navigation mobile */
        .mobile-nav-btn {
            padding: 12px 20px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        .mobile-nav-btn i {
            font-size: 1.2rem;
            margin-right: 10px;
        }

        /* Améliorations pour le contenu */
        .content-wrapper {
            min-height: 100vh;
            padding: 20px;
        }

        .main-content {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            color: #3a1c71;
            border-bottom: 2px solid #d76d77;
            padding-bottom: 12px;
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 1.8rem;
        }

        /* Pied de page */
        footer {
            background: #f8f9fa;
            padding: 15px 0;
            margin-top: 40px;
            border-top: 1px solid #e9ecef;
        }

        .footer-link {
            color: #3a1c71;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-link:hover {
            color: #d76d77;
            text-decoration: underline;
        }

        /* Autres styles existants */
        .form-label {
            font-weight: 500;
            color: #555;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3a1c71 0%, #d76d77 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #321b5e 0%, #c35f68 100%);
        }

        .alert {
            border-radius: 5px;
        }

        .card {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4eaef 100%);
            font-weight: 600;
            color: #3a1c71;
            border-bottom: 2px solid #d76d77;
        }
    </style>
</head>
<body>
    <!-- Header principal amélioré avec une meilleure visibilité -->
    <header class="main-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="logo-text h3">LCAMS</h1>
                    <p class="text-white-50 small m-0">Logiciel de Calcul et Analyse des Moyennes Semestrielles</p>
                </div>
                <div>
                    <a href="{{ url('/') }}" class="btn home-btn btn-outline-light">
                        <i class="fas fa-home"></i> Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </header>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar avec meilleure visibilité -->
            <div class="col-md-3 col-lg-2 sidebar d-none d-md-block">
                <h4 class="text-center">Navigation</h4>
                <nav class="nav flex-column">
                    <a class="nav-link {{ Request::routeIs('settings.index') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                        <i class="fas fa-school"></i> Infos Établissement
                    </a>
                    <a class="nav-link {{ Request::routeIs('settings.grade_levels') ? 'active' : '' }}" href="{{ route('settings.grade_levels') }}">
                        <i class="fas fa-layer-group"></i> Niveaux Scolaires
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 content-wrapper">
                <!-- Mobile Header amélioré -->
                <div class="d-md-none mb-4">
                    <button class="btn btn-primary mobile-nav-btn w-100" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                        <i class="fas fa-bars"></i> Menu de Navigation
                    </button>
                    
                    <!-- Mobile Sidebar amélioré -->
                    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
                        <div class="offcanvas-header" style="background: linear-gradient(135deg, #3a1c71 0%, #d76d77 100%);">
                            <h5 class="offcanvas-title text-white" id="mobileSidebarLabel">Menu Navigation</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <nav class="nav flex-column">
                                <a class="nav-link py-3 {{ Request::routeIs('settings.index') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                                    <i class="fas fa-school"></i> Infos Établissement
                                </a>
                                <a class="nav-link py-3 {{ Request::routeIs('settings.grade_levels') ? 'active' : '' }}" href="{{ route('settings.grade_levels') }}">
                                    <i class="fas fa-layer-group"></i> Niveaux Scolaires
                                </a>
                                <div class="border-top my-3"></div>
                                <a class="nav-link py-3" href="{{ url('/') }}">
                                    <i class="fas fa-home"></i> Retour à l'Accueil
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
                
                <div class="main-content">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <!-- Page Title -->
                    <h2 class="section-title">@yield('title')</h2>
                    
                    <!-- Main Content -->
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer with navigation link -->
    <footer>
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <p class="m-0">&copy; {{ date('Y') }} LCAMS</p>
                <div>
                    <a href="{{ url('/') }}" class="footer-link">
                        <i class="fas fa-home me-1"></i> Page d'accueil
                    </a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script pour gérer les confirmations de suppression
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');
            
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    const confirmMessage = this.getAttribute('data-confirm') || 'Êtes-vous sûr de vouloir supprimer cet élément?';
                    
                    if (!confirm(confirmMessage)) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>