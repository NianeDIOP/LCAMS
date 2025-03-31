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
    
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
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
            --sidebar-width: 260px;
            --topbar-height: 60px;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            overflow-x: hidden;
        }
        
        /* Topbar styles */
        .topbar {
            height: var(--topbar-height);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            padding: 0 1rem;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .topbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            margin-right: 1rem;
            text-decoration: none;
            color: white;
        }
        
        .topbar-info {
            margin-left: auto;
            display: flex;
            align-items: center;
        }
        
        .establishment-badge {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0.3rem 0.7rem;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-right: 0.5rem;
        }
        
        .year-badge {
            background-color: var(--warning-color);
            padding: 0.3rem 0.7rem;
            border-radius: 20px;
            font-size: 0.85rem;
            color: white;
        }
        
        /* Sidebar styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: white;
            color: #7b8190;
            position: fixed;
            top: var(--topbar-height);
            bottom: 0;
            left: 0;
            z-index: 1020;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        
        .sidebar-nav {
            padding: 1.5rem 0;
        }
        
        .sidebar .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .sidebar .nav-link {
            color: #7b8190;
            padding: 0.75rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar .nav-link:hover {
            color: var(--primary-color);
            background-color: rgba(67, 97, 238, 0.05);
            border-left: 3px solid var(--primary-color);
        }
        
        .sidebar .nav-link.active {
            color: var(--primary-color);
            background-color: rgba(67, 97, 238, 0.1);
            border-left: 3px solid var(--primary-color);
            font-weight: 600;
        }
        
        .sidebar .nav-icon {
            width: 1.5rem;
            display: inline-block;
            text-align: center;
            margin-right: 0.75rem;
        }
        
        .sidebar .nav-title {
            display: block;
            padding: 0.5rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #a9a9a9;
            letter-spacing: 0.05em;
            margin-top: 1rem;
        }
        
        .sidebar-submenu {
            padding-left: 1rem;
        }
        
        .sidebar-submenu .nav-link {
            font-size: 0.875rem;
            padding: 0.5rem 1.5rem;
        }
        
        /* Main content styles */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 2rem;
            min-height: calc(100vh - var(--topbar-height));
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
        }
        
        .page-subtitle {
            font-size: 1rem;
            color: #7b8190;
            margin-bottom: 2rem;
        }
        
        /* Card styles */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }
        
        .card-header.header-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }
        
        .card-header.header-success {
            background: linear-gradient(135deg, var(--success-color) 0%, var(--info-color) 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }
        
        .card-header.header-warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, var(--danger-color) 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Stats card */
        .stats-card {
            padding: 1.5rem;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
        }
        
        .stats-icon.stats-primary {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
        }
        
        .stats-icon.stats-success {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success-color);
        }
        
        .stats-icon.stats-warning {
            background-color: rgba(247, 37, 133, 0.1);
            color: var(--warning-color);
        }
        
        .stats-icon.stats-danger {
            background-color: rgba(255, 77, 109, 0.1);
            color: var(--danger-color);
        }
        
        .stats-details {
            flex: 1;
        }
        
        .stats-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }
        
        .stats-text {
            font-size: 0.875rem;
            color: #7b8190;
        }
        
        /* Button styles */
        .btn {
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
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
            color: white;
        }
        
        .btn-warning:hover {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: white;
        }
        
        /* Responsiveness */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar-toggler {
                display: block;
            }
        }
        
        /* Loader */
        .app-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.3s, visibility 0.3s;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(67, 97, 238, 0.1);
            border-left-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Topbar -->
    <div class="topbar">
        <button class="btn btn-link text-white sidebar-toggler d-lg-none me-2" id="sidebarToggler">
            <i class="fas fa-bars"></i>
        </button>
        <a href="{{ route('home') }}" class="topbar-brand">
            <i class="fas fa-chart-line me-2"></i>LCAMS
        </a>
        <div class="topbar-info">
            @if(isset($configuration))
                <div class="establishment-badge">{{ $configuration->nom_etablissement ?? 'Établissement' }}</div>
            @endif
            @if(isset($anneeScolaireActive))
                <div class="year-badge">{{ $anneeScolaireActive->libelle ?? 'Année scolaire' }}</div>
            @endif
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <span class="nav-icon"><i class="fas fa-home"></i></span>
                        <span>Accueil</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('parametres.*') ? 'active' : '' }}" href="{{ route('parametres.index') }}">
                        <span class="nav-icon"><i class="fas fa-cog"></i></span>
                        <span>Paramètres</span>
                    </a>
                </li>
                
                
                <div class="nav-title">Semestres</div>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('semestre1.*') || request()->routeIs('importation.s1') ? 'active' : '' }}" href="{{ route('semestre1.index') }}">
                        <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>
                        <span>Semestre 1</span>
                    </a>
                </li>
                
                @if(request()->routeIs('semestre1.*') || request()->routeIs('importation.s1'))
                <div class="sidebar-submenu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('semestre1.index') ? 'active' : '' }}" href="{{ route('semestre1.index') }}">
                                <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                                <span>Vue d'ensemble</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('semestre1.analyse-moyennes') ? 'active' : '' }}" href="{{ route('semestre1.analyse-moyennes') }}">
                                <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                                <span>Analyse Moyennes</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('semestre1.analyse-disciplines') ? 'active' : '' }}" href="{{ route('semestre1.analyse-disciplines') }}">
                                <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                                <span>Analyse Disciplines</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('semestre1.rapports') ? 'active' : '' }}" href="{{ route('semestre1.rapports') }}">
                                <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
                                <span>Rapports</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('importation.s1') ? 'active' : '' }}" href="{{ route('importation.s1') }}">
                                <span class="nav-icon"><i class="fas fa-file-import"></i></span>
                                <span>Importation</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endif
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('semestre2.*') || request()->routeIs('importation.s2') ? 'active' : '' }}" href="{{ route('semestre2.index') }}">
                        <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>
                        <span>Semestre 2</span>
                    </a>
                </li>
                
                @if(request()->routeIs('semestre2.*') || request()->routeIs('importation.s2'))
                <div class="sidebar-submenu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('semestre2.index') ? 'active' : '' }}" href="{{ route('semestre2.index') }}">
                                <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                                <span>Vue d'ensemble</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('semestre2.analyse-moyennes') ? 'active' : '' }}" href="{{ route('semestre2.analyse-moyennes') }}">
                                <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                                <span>Analyse Moyennes</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('semestre2.analyse-disciplines') ? 'active' : '' }}" href="{{ route('semestre2.analyse-disciplines') }}">
                                <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                                <span>Analyse Disciplines</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('semestre2.rapports') ? 'active' : '' }}" href="{{ route('semestre2.rapports') }}">
                                <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
                                <span>Rapports</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('importation.s2') ? 'active' : '' }}" href="{{ route('importation.s2') }}">
                                <span class="nav-icon"><i class="fas fa-file-import"></i></span>
                                <span>Importation</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endif
                
                <div class="nav-title">Analyse Annuelle</div>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('general.*') ? 'active' : '' }}" href="{{ route('general.index') }}">
                        <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span>
                        <span>Général</span>
                    </a>
                </li>
                
                @if(request()->routeIs('general.*'))
                <div class="sidebar-submenu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('general.index') ? 'active' : '' }}" href="{{ route('general.index') }}">
                                <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                                <span>Vue d'ensemble</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('general.analyse-moyennes') ? 'active' : '' }}" href="{{ route('general.analyse-moyennes') }}">
                                <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                                <span>Analyse Moyennes</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('general.analyse-disciplines') ? 'active' : '' }}" href="{{ route('general.analyse-disciplines') }}">
                                <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                                <span>Analyse Disciplines</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('general.decisions') ? 'active' : '' }}" href="{{ route('general.decisions') }}">
                                <span class="nav-icon"><i class="fas fa-check-double"></i></span>
                                <span>Décisions</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('general.rapports') ? 'active' : '' }}" href="{{ route('general.rapports') }}">
                                <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
                                <span>Rapports</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endif
            </ul>
        </div>
    </div>
    
    <!-- Main content -->
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @yield('content')
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialiser les DataTables
            if ($('.datatable').length) {
                $('.datatable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                    }
                });
            }
            
            // Alert auto-close
            $('.alert').fadeTo(2000, 500).slideUp(500, function() {
                $('.alert').slideUp(500);
            });
            
            // Toggle sidebar on mobile
            $('#sidebarToggler').click(function() {
                $('#sidebar').toggleClass('show');
            });
            
            // Close sidebar when clicking outside on mobile
            $(document).click(function(e) {
                if ($(window).width() < 992) {
                    var container = $("#sidebar, #sidebarToggler");
                    if (!container.is(e.target) && container.has(e.target).length === 0) {
                        $('#sidebar').removeClass('show');
                    }
                }
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>