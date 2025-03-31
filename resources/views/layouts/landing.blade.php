<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LCAMS - @yield('title', 'Logiciel de Calcul et Analyse des Moyennes Semestrielles')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --info-color: #4895ef;
            --warning-color: #f72585;
            --danger-color: #ff4d6d;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --navbar-height: 70px;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            overflow-x: hidden;
        }
        
        /* Navbar styles */
        .main-navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 0.75rem 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .navbar-brand-icon {
            font-size: 1.75rem;
            margin-right: 0.5rem;
        }
        
        .navbar-nav .nav-link {
            padding: 0.6rem 1rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.85);
            border-radius: 5px;
            transition: all 0.3s;
            margin: 0 0.2rem;
        }
        
        .navbar-nav .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .navbar-nav .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .establishment-badge {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0.3rem 0.7rem;
            border-radius: 20px;
            font-size: 0.85rem;
            color: white;
            margin-right: 0.5rem;
        }
        
        .year-badge {
            background-color: var(--warning-color);
            padding: 0.3rem 0.7rem;
            border-radius: 20px;
            font-size: 0.85rem;
            color: white;
        }
        
        /* Hero section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
        }
        
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        /* Feature cards */
        .feature-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
        }
        
        .feature-icon.icon-primary {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
        }
        
        .feature-icon.icon-success {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success-color);
        }
        
        .feature-icon.icon-warning {
            background-color: rgba(247, 37, 133, 0.1);
            color: var(--warning-color);
        }
        
        .feature-icon.icon-info {
            background-color: rgba(72, 149, 239, 0.1);
            color: var(--info-color);
        }
        
        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .feature-desc {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }
        
        /* Buttons */
        .btn {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-success:hover {
            background-color: var(--info-color);
            border-color: var(--info-color);
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }
        
        .btn-warning:hover {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .btn-outline-light {
            border-width: 2px;
        }
        
        .btn-lg {
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
        }
        
        /* Footer */
        .footer {
            background-color: var(--dark-color);
            color: rgba(255, 255, 255, 0.7);
            padding: 3rem 0;
            margin-top: 3rem;
        }
        
        .footer-title {
            color: white;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .footer-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s;
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .footer-link:hover {
            color: white;
        }
        
        .footer-social {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .footer-social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s;
        }
        
        .footer-social-icon:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1.5rem;
            margin-top: 2rem;
            text-align: center;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.5);
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg main-navbar">
        <div class="container">
            <a class="navbar-brand text-white" href="{{ route('home') }}">
                <i class="fas fa-chart-line navbar-brand-icon"></i>LCAMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('parametres.*') ? 'active' : '' }}" href="{{ route('parametres.index') }}">
                            <i class="fas fa-cog me-1"></i> Paramètres
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('semestre1.*') ? 'active' : '' }}" href="{{ route('semestre1.index') }}">
                            <i class="fas fa-calendar-alt me-1"></i> Semestre 1
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('semestre2.*') ? 'active' : '' }}" href="{{ route('semestre2.index') }}">
                            <i class="fas fa-calendar-alt me-1"></i> Semestre 2
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('general.*') ? 'active' : '' }}" href="{{ route('general.index') }}">
                            <i class="fas fa-clipboard-list me-1"></i> Général
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    @if(isset($configuration))
                        <span class="establishment-badge">{{ $configuration->nom_etablissement ?? 'Établissement' }}</span>
                    @endif
                    @if(isset($anneeScolaireActive))
                        <span class="year-badge">{{ $anneeScolaireActive->libelle ?? 'Année scolaire' }}</span>
                    @endif
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Content -->
    @yield('content')
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5 class="footer-title">LCAMS</h5>
                    <p>Logiciel de Calcul et Analyse des Moyennes Semestrielles pour les établissements scolaires sénégalais.</p>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h5 class="footer-title">Liens</h5>
                    <a href="{{ route('home') }}" class="footer-link">Accueil</a>
                    <a href="{{ route('parametres.index') }}" class="footer-link">Paramètres</a>
                    <a href="{{ route('semestre1.index') }}" class="footer-link">Semestre 1</a>
                    <a href="{{ route('semestre2.index') }}" class="footer-link">Semestre 2</a>
                    <a href="{{ route('general.index') }}" class="footer-link">Général</a>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="footer-title">Modules</h5>
                    <a href="{{ route('importation.s1') }}" class="footer-link">Importation Semestre 1</a>
                    <a href="{{ route('importation.s2') }}" class="footer-link">Importation Semestre 2</a>
                    <a href="{{ route('semestre1.analyse-moyennes') }}" class="footer-link">Analyse des moyennes</a>
                    <a href="{{ route('semestre1.analyse-disciplines') }}" class="footer-link">Analyse des disciplines</a>
                </div>
                <div class="col-lg-3">
                    <h5 class="footer-title">Contact</h5>
                    <p>Pour toute question ou assistance, n'hésitez pas à nous contacter.</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="mb-0">&copy; {{ date('Y') }} LCAMS. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Alert auto-close
            $('.alert').fadeTo(2000, 500).slideUp(500, function() {
                $('.alert').slideUp(500);
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>