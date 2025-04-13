@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Historique des importations</h5>
                    <a href="{{ route('admin.import.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle importation
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Niveau scolaire</th>
                                    <th>Utilisateur</th>
                                    <th>Statut</th>
                                    <th class="text-center">Élèves</th>
                                    <th class="text-center">Notes</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($imports as $import)
                                    @php
                                        $details = json_decode($import->details);
                                    @endphp
                                    <tr>
                                        <td>{{ $import->id }}</td>
                                        <td>{{ $import->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $import->gradeLevel->name ?? 'N/A' }}</td>
                                        <td>{{ $import->user->name ?? 'Système' }}</td>
                                        <td>
                                            @if($import->status === 'terminé')
                                                <span class="badge bg-success">Terminé</span>
                                            @elseif($import->status === 'en_cours')
                                                <span class="badge bg-warning">En cours</span>
                                            @else
                                                <span class="badge bg-danger">Échoué</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($import->status === 'terminé' && isset($details->statistiques))
                                                {{ $details->statistiques->total_students }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($import->status === 'terminé' && isset($details->statistiques))
                                                {{ $details->statistiques->total_marks }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('admin.import.show', $import->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($import->status !== 'en_cours')
                                                <form method="POST" action="{{ route('admin.import.destroy', $import->id) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette importation ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            Aucun historique d'importation disponible
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $imports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection