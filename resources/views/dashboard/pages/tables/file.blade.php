
@extends('dashboard.acceuil')

@section('contenu')

<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Tables /</span> Table Fichiers</h4>
  @if(session('success'))
  <div class="alert alert-primary alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif
  <!-- Formulaire de recherche et de tri -->
  <form method="GET" action="{{ route('files') }}" class="mb-4">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <!-- Champ de recherche -->
                                <input type="text" name="search" class="form-control" placeholder="Rechercher un projet" value="{{ request('search') }}">

                                <!-- Option de tri par département -->
                                <select name="department" class="form-select">
                                    <option value="">Trier par département</option>
                                    @foreach($dep as $department)
                                        <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Option de tri par projet -->
                                <select name="project" class="form-select">
                                    <option value="">Trier par projet</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Bouton de recherche -->
                                <button type="submit" class="btn btn-primary">Rechercher</button>
                            </div>
                        </div>
                    </div>
  </form>

<!-- Basic Bootstrap Table -->
<div class="card">
    <h5 class="card-header">Table Fichiers</h5>
    <div class="table-responsive text-nowrap">
                  <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th>Utilisateur</th>
                            <th>Projet associé</th>
                            <th>Actions</th>

                        </tr>
                    </thead>
                    @if ($files->count() != 0)
                    <tbody class="table-border-bottom-0">

                        @foreach($files as $file)
                            <tr>
                                @php
                                    $extension = pathinfo($file->filename, PATHINFO_EXTENSION);
                                    $iconClass = \App\Helpers\FileIconHelper::getIcon($extension);
                                @endphp
                                <td>{{ $file->id }}</td>
                                <td><i class="fas {{ $iconClass }}" style="color: grey"> </i> {{ $file->filename }}</i> </td>


                                <td>{{ $file->type }}</td>
                                <td>{{ $file->status }}</td>
                                <td>{{ $file->user->name }}</td>
                                @if (isset($file->project->name ))
                                <td>{{ $file->project->name }}</td>
                                @else
                                <td>Fichier autonome</td>
                                @endif

                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @if (isset($file->project->name))
                                            <a class="dropdown-item" href=""  onclick="event.preventDefault(); document.getElementById('form-archive-id{{$file->id}}').submit();"></i> Archiver</a>
                                            <form id="form-archive-id{{$file->id}}" action="{{route('archiverfile',$file->id)}}" method="POST" style="display: none;">
                                                @csrf

                                            </form>
                                            @endif
                                            <a class="dropdown-item" href="{{route('downloadfile', $file->id)}}"><i class="bx bx-download-alt me-1"></i> Télecharger</a>
                                            @if ($extension === 'txt')
                                                <a class="dropdown-item" href=""><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                            @endif

                                            <a class="dropdown-item" href="" id="deleteone" onclick="event.preventDefault(); $('#confirmationModal').modal('show');">
                                                <i class="bx bx-trash me-1"></i> Supprimer
                                            </a>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    @else
                        <h3 class="center">Aucun resultat trouvé ! </h3>
                    @endif
                  </table>
    </div>
</div>
<!-- Floating Action Button -->
<div id="fab-container">
    <div id="fab-text">Ajouter</div>
    <button id="fab" class="btn btn-primary rounded-circle">
        <i class="fas fa-plus"></i>
    </button>
</div>

<!-- Formulaire d'ajout de fichier -->
<div id="fileForm" class="file-form">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Ajouter un fichier</h5>
            <form action="{{ route('storefile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label for="project_id">Projet</label>
                    <select name="projet" id="project_id" class="form-control">
                        <option value="">Aucun</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="type" value="{{ 'officiel' }}">
                <div class="input-group mb-3">
                    <input type="file" name="fichier" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Uploader</button>
                <button type="button" id="closeForm" class="btn btn-secondary">Annuler</button>
            </form>
        </div>
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
                Êtes-vous sûr de vouloir supprimer ce fichier ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="form-delete-id{{$file->id}}" action="{{ route('delete', ['table' => 'files','id' => $file->id]) }}" method="POST" >
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
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('fab').addEventListener('click', function() {
            document.getElementById('fileForm').style.display = 'block';
        });

        document.getElementById('closeForm').addEventListener('click', function() {
            document.getElementById('fileForm').style.display = 'none';
        });
    });
</script>


@endsection
<style>
    #fab-container {
        position: fixed;
        bottom: 20px; /* Adjust as needed */
        right: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 1000; /* Ensure it's above other content */
    }

    #fab {
        width: 60px;
        height: 60px;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    }

    #fab-text {
        margin-bottom: 10px;
    }

    #fileForm {
        display: none;
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 300px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
        z-index: 2000;
    }

    .file-form .card {
        margin: 0;
    }
</style>



<style>


    /* Modal personnalisé */
    .modal-content {
        border-radius: 10px; /* Coins arrondis */
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2); /* Ombre légère */
    }


    .modal-header {
        border-radius: 10px 10px 0 0; /* Coins arrondis uniquement en haut */
        border-bottom: none; /* Pas de bordure en bas */
    }

    .modal-footer {
        border-top: none; /* Pas de bordure en haut du footer */
    }

    .bg-danger {
        background-color: #dc3545 !important; /* Couleur de fond du header modal */
    }

    .text-white {
        color: #fff; /* Couleur du texte */
    }

</style>

