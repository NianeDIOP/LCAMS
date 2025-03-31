<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Liste des élèves - Semestre 1</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 12px;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            font-size: 9px;
            text-align: left;
        }
        th {
            background-color: #4361ee;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-success {
            color: #4cc9f0;
        }
        .text-danger {
            color: #ff4d6d;
        }
        .footer {
            margin-top: 20px;
            font-size: 8px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            display: inline-block;
        }
        .bg-success {
            background-color: #4cc9f0;
            color: white;
        }
        .bg-info {
            background-color: #4895ef;
            color: white;
        }
        .bg-warning {
            background-color: #f72585;
            color: white;
        }
        .bg-danger {
            background-color: #ff4d6d;
            color: white;
        }
        .bg-secondary {
            background-color: #6c757d;
            color: white;
        }
        .page-break {
            page-break-after: always;
        }
        .filter-info {
            font-size: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            padding: 5px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Liste des élèves - Semestre 1</div>
        <div class="subtitle">Année scolaire : {{ $anneeScolaireActive->libelle }}</div>
        <div>Date d'export : {{ $dateExport }}</div>
    </div>
    
    @if($filtres)
    <div class="filter-info">
        <strong>Filtres appliqués :</strong> 
        @foreach($filtres as $key => $value)
            @if($value)
                {{ $key }}: {{ $value }} |
            @endif
        @endforeach
    </div>
    @endif
    
    <table>
        <thead>
            <tr>
                <th style="width: 70px;">IEN</th>
                <th style="width: 80px;">Nom</th>
                <th style="width: 80px;">Prénom</th>
                <th style="width: 30px;">Sexe</th>
                <th style="width: 70px;">Classe</th>
                <th style="width: 60px;">Moyenne</th>
                <th style="width: 40px;">Rang</th>
                <th style="width: 100px;">Appréciation</th>
                <th style="width: 100px;">Décision</th>
                <th style="width: 40px;">Retard</th>
                <th style="width: 40px;">Absence</th>
            </tr>
        </thead>
        <tbody>
            @foreach($eleves as $eleve)
                @php
                    // Déterminer la décision
                    $badgeClass = 'bg-secondary';
                    $decision = 'Non définie';
                    
                    if ($eleve->moyenneGeneraleS1 && $eleve->moyenneGeneraleS1->moyenne) {
                        $moyenne = $eleve->moyenneGeneraleS1->moyenne;
                        if ($moyenne >= 16) {
                            $decision = 'Travail excellent';
                            $badgeClass = 'bg-success';
                        } elseif ($moyenne >= 12) {
                            $decision = 'Satisfaisant doit continuer';
                            $badgeClass = 'bg-info';
                        } elseif ($moyenne >= 10) {
                            $decision = 'Peut Mieux Faire';
                            $badgeClass = 'bg-warning';
                        } elseif ($moyenne >= 8) {
                            $decision = 'Insuffisant';
                            $badgeClass = 'bg-danger';
                        } elseif ($moyenne >= 5) {
                            $decision = 'Risque de Redoubler';
                            $badgeClass = 'bg-danger';
                        } else {
                            $decision = 'Risque l\'exclusion';
                            $badgeClass = 'bg-danger';
                        }
                    }
                @endphp
                <tr>
                    <td>{{ $eleve->ien }}</td>
                    <td>{{ $eleve->nom }}</td>
                    <td>{{ $eleve->prenom }}</td>
                    <td class="text-center">
                        {{ $eleve->sexe == 'M' ? 'M' : ($eleve->sexe == 'F' ? 'F' : '-') }}
                    </td>
                    <td>{{ $eleve->classe->libelle }}</td>
                    <td class="text-center {{ $eleve->moyenneGeneraleS1 && $eleve->moyenneGeneraleS1->moyenne >= 10 ? 'text-success' : 'text-danger' }}">
                        {{ $eleve->moyenneGeneraleS1 ? number_format($eleve->moyenneGeneraleS1->moyenne, 2) : '-' }}
                    </td>
                    <td class="text-center">{{ $eleve->moyenneGeneraleS1 ? $eleve->moyenneGeneraleS1->rang : '-' }}</td>
                    <td>
                        @if($eleve->moyenneGeneraleS1 && $eleve->moyenneGeneraleS1->appreciation)
                            {{ $eleve->moyenneGeneraleS1->appreciation }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ $decision }}</span>
                    </td>
                    <td class="text-center">{{ $eleve->moyenneGeneraleS1 ? $eleve->moyenneGeneraleS1->retard : '-' }}</td>
                    <td class="text-center">{{ $eleve->moyenneGeneraleS1 ? $eleve->moyenneGeneraleS1->absence : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Document généré par LCAMS - Logiciel de Calcul et Analyse des Moyennes Semestrielles</p>
        <p>Page {{ $page }} sur {{ $totalPages }}</p>
    </div>
</body>
</html>