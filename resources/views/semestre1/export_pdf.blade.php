<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des moyennes - Semestre 1</title>
    <style>
        /* Styles généraux et mise en page */
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            margin: 0;
            padding: 10px;
            color: #333;
        }
        
        /* En-tête de l'établissement */
        .institution-header {
            text-align: center;
            margin-bottom: 15px;
            line-height: 1.2;
        }
        
        .ministry {
            font-size: 10pt;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        
        .academic-info {
            font-size: 10pt;
            margin-bottom: 5px;
        }
        
        .school-name {
            font-size: 16pt;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .school-address {
            font-size: 10pt;
            margin-bottom: 2px;
        }
        
        .school-year {
            font-size: 12pt;
            font-weight: bold;
            margin: 5px 0;
        }
        
        /* Titre du document */
        .document-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 15px 0;
            text-decoration: underline;
        }
        
        /* Tableau de données */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
            padding: 6px 4px;
            border: 1px solid #000;
            font-size: 11pt;
            overflow: hidden;
            word-wrap: break-word;
        }
        
        td {
            padding: 6px 4px;
            border: 1px solid #000;
            overflow: hidden;
            word-wrap: break-word;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Pied de page */
        .footer {
            text-align: center;
            font-size: 10pt;
            margin-top: 15px;
            width: 100%;
        }
        
        /* Ajustements de largeur des colonnes - pour format portrait */
        .col-id { width: 15%; }
        .col-prenom { width: 15%; }
        .col-nom { width: 15%; }
        .col-sexe { width: 5%; }
        .col-moy { width: 8%; }
        .col-rang { width: 8%; }
        .col-appreciation { width: 34%; }
        
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
                <th class="col-moy">Moy</th>
                <th class="col-rang">Rang</th>
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
                    <td class="text-center"><strong>{{ $row['data'][9] }}</strong></td>
                    <td class="text-center">{{ $row['data'][10] }}</td>
                    <td>{{ $row['data'][12] }}</td>
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