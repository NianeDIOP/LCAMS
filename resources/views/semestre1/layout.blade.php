<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - LCAMS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar {
            background: linear-gradient(135deg, #3a1c71 0%, #d76d77 100%);
            color: white;
            min-width: 260px;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
            padding-top: 20px;
            transition: all 0.3s;
        }
        
        .sidebar-header {
            padding: 15px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
        }
        
        .sidebar-logo {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }
        
        .sidebar-subtitle {
            font-size: 1rem;
            font-weight: 300;
            margin-bottom: 10px;
        }
        
        .sidebar-semester {
            font-size: 1.2rem;
            font-weight: 500;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 8px 15px;
            border-radius: 4px;
            display: inline-block;
        }
        
        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .nav-menu li {
            margin-bottom: 5px;
        }
        
        .nav-menu li a {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            transition: all 0.3s;
        }
        
        .nav-menu li a:hover, .nav-menu li a.active {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        
        .nav-menu li i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .content-wrapper {
            flex: 1;
            margin-left: 260px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .top-bar {
            background: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 600;
            color: #3a1c71;
        }
        
        .breadcrumb {
            margin: 0;
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            font-size: 1.1rem;
            padding: 15px 20px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3a1c71 0%, #d76d77 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #331a60 0%, #c45c66 100%);
        }
        
        .toggle-sidebar {
            background: none;
            border: none;
            color: #3a1c71;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0;
            margin-right: 15px;
            display: none;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                margin-left: -260px;
            }
            
            .content-wrapper {
                margin-left: 0;
            }
            
            .toggle-sidebar {
                display: block;
            }
            
            .sidebar.active {
                margin-left: 0;
            }
            
            .content-wrapper.active {
                margin-left: 260px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h1 class="sidebar-logo">LCAMS</h1>
            <div class="sidebar-subtitle">Analyse et Calcul</div>
            <div class="sidebar-semester">Semestre 1</div>
        </div>
        
        <ul class="nav-menu">
            <li>
                <a href="{{ route('semestre1.analyse-moyennes') }}" class="{{ Route::currentRouteName() == 'semestre1.analyse-moyennes' ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Analyse des moyennes
                </a>
            </li>
            <li>
                <a href="{{ route('semestre1.analyse-disciplines') }}" class="{{ Route::currentRouteName() == 'semestre1.analyse-disciplines' ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Analyse par discipline
                </a>
            </li>
            <li>
                <a href="{{ route('semestre1.importation') }}" class="{{ Route::currentRouteName() == 'semestre1.importation' ? 'active' : '' }}">
                    <i class="fas fa-file-import"></i> Importation de données
                </a>
            </li>
            <li>
                <a href="{{ route('semestre1.rapport') }}" class="{{ Route::currentRouteName() == 'semestre1.rapport' ? 'active' : '' }}">
                    <i class="fas fa-file-pdf"></i> Génération de rapports
                </a>
            </li>
            <li class="mt-4">
                <a href="{{ url('/') }}">
                    <i class="fas fa-home"></i> Accueil
                </a>
            </li>
            <li>
                <a href="{{ route('settings.index') }}">
                    <i class="fas fa-cog"></i> Paramètres
                </a>
            </li>
        </ul>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper" id="content">
        <div class="top-bar">
            <div class="d-flex align-items-center">
                <button class="toggle-sidebar" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">@yield('title')</h1>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('semestre1.analyse-moyennes') }}">Semestre 1</a></li>
                    <li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
                </ol>
            </nav>
        </div>

        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                content.classList.toggle('active');
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>