<!-- resources/views/layouts/main.blade.php -->
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
    
    <!-- Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        main {
            flex: 1;
        }
        
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
        
        .footer {
            background-color: var(--white);
            padding: 1rem 0;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            font-size: 0.85rem;
            color: var(--secondary);
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    @include('partials.navbar')

    <!-- Contenu principal -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Scripts personnalisés -->
    @yield('scripts')
</body>
</html>