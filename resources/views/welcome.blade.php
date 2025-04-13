<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LCAMS - Logiciel de Calcul et Analyse des Moyennes Semestrielles</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Styles -->
        <style>
            body {
                font-family: 'Montserrat', sans-serif;
                background: linear-gradient(135deg, #f5f7fa 0%, #e4eaef 100%);
                color: #333;
                margin: 0;
                padding: 0;
                min-height: 100vh;
            }
            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
            }
            header {
                background: linear-gradient(135deg, #3a1c71 0%, #d76d77 50%, #ffaf7b 100%);
                color: white;
                padding: 1rem 0;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                position: relative;
            }
            .header-content {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .logo h1 {
                font-size: 1.8rem;
                font-weight: 700;
                margin: 0;
            }
            .logo span {
                font-size: 1rem;
                font-weight: 300;
                display: block;
            }
            nav ul {
                display: flex;
                list-style: none;
                margin: 0;
                padding: 0;
            }
            nav ul li {
                margin-left: 1.5rem;
            }
            nav ul li a {
                color: white;
                text-decoration: none;
                font-weight: 500;
                transition: all 0.3s ease;
                padding: 0.5rem 1rem;
                border-radius: 4px;
            }
            nav ul li a:hover {
                background-color: rgba(255, 255, 255, 0.2);
            }
            .hero {
                padding: 4rem 0;
                text-align: center;
            }
            .hero h2 {
                font-size: 2.5rem;
                margin-bottom: 1rem;
                color: #3a1c71;
            }
            .hero p {
                font-size: 1.2rem;
                max-width: 800px;
                margin: 0 auto 2rem auto;
                color: #555;
            }
            .btn {
                display: inline-block;
                padding: 0.8rem 2rem;
                background: linear-gradient(135deg, #3a1c71 0%, #d76d77 100%);
                color: white;
                border-radius: 50px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s ease;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            .btn:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
            }
            .features {
                padding: 4rem 0;
                background-color: white;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
                border-radius: 10px;
                margin-top: 2rem;
            }
            .section-title {
                text-align: center;
                font-size: 2rem;
                margin-bottom: 3rem;
                color: #3a1c71;
            }
            .features-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 2rem;
            }
            .feature-card {
                background: linear-gradient(135deg, #f5f7fa 0%, #e4eaef 100%);
                border-radius: 8px;
                padding: 1.5rem;
                text-align: center;
                transition: all 0.3s ease;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            }
            .feature-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            }
            .feature-icon {
                font-size: 2.5rem;
                margin-bottom: 1rem;
                color: #d76d77;
            }
            .feature-card h3 {
                font-size: 1.3rem;
                margin-bottom: 1rem;
                color: #3a1c71;
            }
            .process {
                padding: 4rem 0;
            }
            .process-steps {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                counter-reset: step-counter;
            }
            .step {
                flex: 0 0 calc(25% - 2rem);
                margin: 1rem;
                padding: 1.5rem;
                background-color: white;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                position: relative;
                counter-increment: step-counter;
            }
            .step::before {
                content: counter(step-counter);
                position: absolute;
                top: -15px;
                left: -15px;
                width: 40px;
                height: 40px;
                background: linear-gradient(135deg, #3a1c71 0%, #d76d77 100%);
                color: white;
                font-weight: bold;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
            }
            .step h3 {
                margin-bottom: 1rem;
                color: #3a1c71;
            }
            .step p {
                color: #555;
                margin: 0;
            }
            .indicators {
                padding: 4rem 0;
                background: linear-gradient(135deg, #f5f7fa 0%, #e4eaef 100%);
                border-radius: 10px;
                margin-top: 2rem;
            }
            .indicators-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 2rem;
            }
            .indicator-card {
                background-color: white;
                border-radius: 8px;
                padding: 1.5rem;
                text-align: center;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            }
            .indicator-icon {
                font-size: 2rem;
                color: #3a1c71;
                margin-bottom: 1rem;
            }
            .indicator-card h3 {
                font-size: 1.2rem;
                margin-bottom: 0.5rem;
                color: #3a1c71;
            }
            .indicator-card p {
                color: #555;
                margin: 0;
            }
            footer {
                background: linear-gradient(135deg, #3a1c71 0%, #d76d77 100%);
                color: white;
                padding: 2rem 0;
                text-align: center;
                margin-top: 4rem;
            }
            footer p {
                margin: 0;
            }
            @media (max-width: 768px) {
                .header-content {
                    flex-direction: column;
                    text-align: center;
                }
                nav ul {
                    margin-top: 1rem;
                }
                nav ul li {
                    margin-left: 0.5rem;
                    margin-right: 0.5rem;
                }
                .hero h2 {
                    font-size: 2rem;
                }
                .step {
                    flex: 0 0 calc(50% - 2rem);
                }
            }
            @media (max-width: 480px) {
                .step {
                    flex: 0 0 100%;
                }
            }
        </style>
    </head>
    <body>
        <header>
            <div class="container header-content">
                <div class="logo">
                    <h1>LCAMS</h1>
                    <span>Logiciel de Calcul et Analyse des Moyennes Semestrielles</span>
                </div>
                <nav>
                    <ul>
                        <li><a href="{{ url('/') }}">Accueil</a></li>
                        <li><a href="{{ route('semestre1.analyse-moyennes') }}">Semestre 1</a></li>
                        <li><a href="#">Semestre 2</a></li>
                        <li><a href="#">Général</a></li>
                        <li><a href="{{ route('settings.index') }}"><i class="fas fa-cog"></i> Paramètres</a></li>
                        @if (Route::has('login'))
                            @auth
                                <li><a href="{{ url('/dashboard') }}">Tableau de bord</a></li>
                            @else
                                <li><a href="{{ route('login') }}">Connexion</a></li>
                                @if (Route::has('register'))
                                    <li><a href="{{ route('register') }}">S'inscrire</a></li>
                                @endif
                            @endauth
                        @endif
                    </ul>
                </nav>
            </div>
        </header>

        <section class="hero">
            <div class="container">
                <h2>Optimisez vos conseils de classe avec LCAMS</h2>
                <p>Notre solution innovante permet de gérer efficacement les conseils de classe du premier et second semestre, en optimisant le suivi des performances des élèves à travers des analyses statistiques complètes et détaillées.</p>
                <a href="#" class="btn">Commencer maintenant</a>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <h2 class="section-title">Fonctionnalités clés</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Statistiques par niveau</h3>
                        <p>Analysez les performances des élèves par niveau scolaire pour identifier les tendances et les besoins spécifiques.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Statistiques par classe</h3>
                        <p>Obtenez une vue détaillée des performances par classe pour mieux orienter les interventions pédagogiques.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-venus-mars"></i>
                        </div>
                        <h3>Statistiques par sexe</h3>
                        <p>Analysez les différences de performance entre les groupes pour assurer l'équité dans l'éducation.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h3>Décisions du conseil</h3>
                        <p>Suivez et analysez les décisions prises lors des conseils de classe pour assurer la cohérence et l'équité.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-medal"></i>
                        </div>
                        <h3>Classement par mérite</h3>
                        <p>Générez automatiquement des listes d'élèves classés par ordre de mérite pour chaque classe.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h3>Statistiques par discipline</h3>
                        <p>Analysez les performances dans chaque matière pour identifier les forces et les domaines nécessitant une attention particulière.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="process">
            <div class="container">
                <h2 class="section-title">Comment utiliser LCAMS</h2>
                <div class="process-steps">
                    <div class="step">
                        <h3>Copier-coller les moyennes</h3>
                        <p>Utilisez les statistiques de PLANETE pour copier et coller les moyennes des élèves dans la base des semestres 1 ou 2 du logiciel.</p>
                    </div>
                    <div class="step">
                        <h3>Validation des données</h3>
                        <p>Validez les données collées pour assurer leur exactitude et fiabilité avant analyse.</p>
                    </div>
                    <div class="step">
                        <h3>Intégration des disciplines</h3>
                        <p>Copiez et collez les moyennes des disciplines dans leurs bases respectives du logiciel.</p>
                    </div>
                    <div class="step">
                        <h3>Exploitation des résultats</h3>
                        <p>Naviguez librement dans le logiciel et téléchargez toutes les informations souhaitées pour vos analyses.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="indicators">
            <div class="container">
                <h2 class="section-title">Indicateurs de performance</h2>
                <div class="indicators-grid">
                    <div class="indicator-card">
                        <div class="indicator-icon">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <h3>Taux de réussite</h3>
                        <p>Suivez l'évolution du taux de réussite par classe, niveau et établissement.</p>
                    </div>
                    <div class="indicator-card">
                        <div class="indicator-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3>Moyenne générale</h3>
                        <p>Analysez les tendances de la moyenne générale à différents niveaux.</p>
                    </div>
                    <div class="indicator-card">
                        <div class="indicator-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h3>Performance par matière</h3>
                        <p>Identifiez les points forts et les faiblesses dans chaque discipline.</p>
                    </div>
                    <div class="indicator-card">
                        <div class="indicator-icon">
                            <i class="fas fa-hand-point-up"></i>
                        </div>
                        <h3>Progression semestrielle</h3>
                        <p>Mesurez l'évolution des performances entre le premier et le second semestre.</p>
                    </div>
                </div>
            </div>
        </section>

        <footer>
            <div class="container">
                <p>&copy; {{ date('Y') }} LCAMS - Logiciel de Calcul et Analyse des Moyennes Semestrielles. Tous droits réservés.</p>
            </div>
        </footer>
    </body>
</html>
