<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des moyennes - Semestre 1</title>
    <style>
        /* Styles généraux et mise en page */
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            margin: 0;
            padding: 5px; /* Marges réduites */
            color: #333;
        }
        
        /* En-tête de l'établissement */
        .institution-header {
            text-align: center;
            margin-bottom: 10px; /* Marge inférieure réduite */
            line-height: 1.1; /* Interligne réduit */
        }
        
        .ministry {
            font-size: 9pt;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        
        .academic-info {
            font-size: 9pt;
            margin-bottom: 3px;
        }
        
        .school-name {
            font-size: 12pt; /* Taille définie à Times New Roman 12 */
            font-weight: bold;
            margin: 3px 0;
        }
        
        .school-address {
            font-size: 9pt;
            margin-bottom: 2px;
        }
        
        .school-year {
            font-size: 10pt;
            font-weight: bold;
            margin: 3px 0;
        }
        
        .class-info {
            font-size: 11pt;
            font-weight: bold;
            margin: 3px 0;
        }
        
        /* Titre du document */
        .document-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin: 10px 0; /* Marges réduites */
            text-decoration: underline;
        }
        
        /* Tableau de données */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 7px; /* Marge supérieure réduite */
            table-layout: fixed;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
            padding: 4px 2px;
            border: 1px solid #000;
            font-size: 9pt;
            overflow: hidden;
            word-wrap: break-word;
        }
        
        td {
            padding: 4px 2px;
            border: 1px solid #000;
            overflow: hidden;
            word-wrap: break-word;
            font-size: 9pt;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Pied de page */
        .footer {
            text-align: center;
            font-size: 8pt;
            margin-top: 10px; /* Marge supérieure réduite */
            width: 100%;
        }
        
        /* Ajustements de largeur des colonnes */
        .col-id { width: 13%; }
        .col-prenom { width: 13%; }
        .col-nom { width: 13%; }
        .col-sexe { width: 5%; }
        .col-absences { width: 7%; }
        .col-moy { width: 7%; }
        .col-rang { width: 7%; }
        .col-decision { width: 15%; }
        .col-appreciation { width: 20%; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .page-break {
            page-break-after: always;
        }

        /* En-tête de page répété */
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
    </style>
</head>
<body>
    <!-- En-tête avec informations de l'établissement -->
    <div class="institution-header">
        <div class="ministry">République du Sénégal</div>
        <div class="academic-info">
            Académie de {{ $file->academie ?? 'Non définie' }} - IEF de {{ $file->ief ?? 'Non définie' }}
        </div>
        <div class="school-name">{{ $file->nom_etablissement ?? 'ÉTABLISSEMENT SCOLAIRE' }}</div>
        <div class="school-address">{{ $file->adresse_etablissement ?? 'Adresse de l\'établissement' }}</div>
        <div class="school-year">Année Scolaire: {{ $file->annee_scolaire ?? '2024-2025' }}</div>
        <div class="class-info">
            Niveau: {{ $file->niveau_nom ?? 'Non spécifié' }} - Classe: {{ $file->classe_nom ?? 'Non spécifié' }}
        </div>
    </div>
    
    <!-- Titre du document -->
    <div class="document-title">
        LISTE DES MOYENNES DU SEMESTRE 1
        @if(isset($searchTerm) || isset($minMoy) || isset($maxMoy))
        (DONNÉES FILTRÉES)
        @else
        PAR ORDRE DE MÉRITE
        @endif
    </div>
    
    <!-- Tableau des données -->
    <table>
        <thead>
            <tr>
                <th class="col-id">IEN</th>
                <th class="col-prenom">Prénom</th>
                <th class="col-nom">Nom</th>
                <th class="col-sexe">Sexe</th>
                <th class="col-absences">Abs.</th>
                <th class="col-moy">Moy</th>
                <th class="col-rang">Rang</th>
                <th class="col-decision">Décision</th>
                <th class="col-appreciation">Appréciation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ $row['data'][0] }}</td>
                    <td>{{ $row['data'][1] }}</td>
                    <td>{{ $row['data'][2] }}</td>
                    <td class="text-center">{{ $row['data'][3] }}</td>
                    <td class="text-center">{{ $row['data'][7] ?? '0' }}</td>
                    <td class="text-center"><strong>{{ $row['data'][9] }}</strong></td>
                    <td class="text-center">{{ $row['data'][10] }}</td>
                    <td class="text-center">{{ $row['data'][11] ?? '' }}</td>
                    <td>{{ substr($row['data'][12] ?? '', 0, 50) }}{{ strlen($row['data'][12] ?? '') > 50 ? '...' : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Pied de page -->
    <div class="footer">
        <p>Document généré par LCAMS le {{ date('d/m/Y à H:i') }} | {{ count($data) }} élèves</p>
    </div>
</body>
</html>