
@extends('dashboard.acceuil')

@section('contenu')

<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Tables /</span> Table Projet</h4>
@if(session('success'))
<div class="alert alert-primary alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
    <!-- Formulaire de recherche et de tri -->
<form method="GET" action="{{ route('projects') }}" class="mb-4">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Rechercher un projet" value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">Rechercher</button>
                            </div>
                        </div>
                    </div>
</form>

<div class="card">
                <h5 class="card-header">Table Projet</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nom</th>
                            <th>Département</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($projects as $project)
                        <tr>
                            <td>{{ $project->id }}</td>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{ $project->name }}</strong></td>
                            @if (isset($project->department->name) )
                                <td>{{ $project->department->name }}</td>
                            @else
                                <td>Projet géneral</td>
                            @endif

                            <td>{{ $project->description }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{route('modifyproject', ['id' => $project->id])}}"><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        {{-- L'identifiant du projet voyage jusqu'à la modale : elle est
                                             unique et partagée par toutes les lignes. --}}
                                        <a class="dropdown-item declencheur-suppression" href="#"
                                           data-projet-id="{{ $project->id }}"
                                           data-projet-nom="{{ $project->name }}">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </a>

                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
                </div>
                <div class="p-3">
                  {{ $projects->links() }}
                </div>
</div>
<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header  text-white">
                <h5 class="modal-title" id="confirmationModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer le projet
                <strong id="nom-projet-a-supprimer"></strong> ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                {{-- L'action est fixée au moment du clic. Elle pointait auparavant
                     vers $project, variable fuitée par la boucle : la modale
                     supprimait donc toujours le dernier projet de la liste, quelle
                     que soit la ligne choisie — et la page tombait en erreur dès que
                     la table était vide. --}}
                <form id="formulaire-suppression-projet" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var modale = document.getElementById('confirmationModal');
    var formulaire = document.getElementById('formulaire-suppression-projet');
    var libelle = document.getElementById('nom-projet-a-supprimer');
    var gabarit = @json(route('delete', ['table' => 'projects', 'id' => '__ID__']));

    document.querySelectorAll('.declencheur-suppression').forEach(function (lien) {
      lien.addEventListener('click', function (e) {
        e.preventDefault();
        formulaire.setAttribute('action', gabarit.replace('__ID__', this.dataset.projetId));
        libelle.textContent = this.dataset.projetNom || '';
        bootstrap.Modal.getOrCreateInstance(modale).show();
      });
    });
  });
</script>
@endsection
