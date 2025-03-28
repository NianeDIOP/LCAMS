<!-- resources/views/partials/navbar.blade.php -->
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