@extends('dashboard.acceuil')

@section('contenu')
<h2 class="fw-bold py-3 mb-4 ms-4"><span class="text-muted fw-light">Mes fichiers/</span> Brouillon</h2>
<div class="container mt-4">
    @if(session('success'))
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Formulaire de recherche et de tri -->
    <form method="GET" action="{{ route('viewfile') }}" class="mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <input type="text" name="search" class="form-control" placeholder="Rechercher un fichier" value="{{ request('search') }}">
            </div>
            <div class="col-md-3 mb-3">
                <select name="sort_by_date" class="form-select">
                    <option value="">Trier par date de création</option>
                    <option value="asc" {{ request('sort_by_date') == 'asc' ? 'selected' : '' }}>Date croissante</option>
                    <option value="desc" {{ request('sort_by_date') == 'desc' ? 'selected' : '' }}>Date décroissante</option>
                </select>
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Rechercher</button>
            </div>

        </div>
    </form>

    <!-- Affichage des fichiers groupés -->
    @foreach($groupedFiles as $group => $files)
    <div class="row mb-4">
        <div class="col-12">
            <h3>{{ $group }}</h3>
        </div>
        @foreach($files as $file)
        <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
            <!-- Card de fichier -->
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas {{ \App\Helpers\FileIconHelper::getIcon(pathinfo($file->filename, PATHINFO_EXTENSION)) }}"></i> {{ $file->filename }}
                    </h5>
                    @if (isset($file->project->name))
                    <p class="card-text">Projet: {{ optional($file->project)->name }}</p>
                    @else
                    <p class="card-text">Fichier autonome</p>
                    @endif
                    <p class="card-text">Soumis le : {{ $file->created_at->format('d/m/Y') }}</p>
                </div>
                <div class="card-footer d-flex justify-content-evenly ps-2 pe-2 ">
                    @if (pathinfo($file->filename, PATHINFO_EXTENSION) === 'txt')
                    <a href="" > <button class="btn btn-sm btn-outline-secondary  ">Lire/Modifier</button> </a>

                    @endif

                    <a href="{{ route('downloadfile', $file->id) }}" > <button class="btn btn-sm btn-outline-secondary ">Télécharger</button> </a>
                    @if(optional($file->project)->status === 'archive')
                    <button class="btn btn-sm btn-outline-danger" disabled>Supprimer</button>
                    @else
                    <form action="{{ route('deletefile', $file->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger " onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fichier ?')">Supprimer</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach

    <!-- Aucun fichier trouvé -->
    @if($groupedFiles->isEmpty())
    <div class="text-center">
        <p>Aucun fichier brouillon uploadé et en cours ! Appuyez sur le bouton plus situé dans le coin inférieur droit pour en ajouter.</p>
        <img src="https://media.giphy.com/media/jAYUbVXgESSti/giphy.gif" alt="Aucun fichier trouvé" style="width: 300px">
    </div>
    @endif
</div>

<!-- Floating Action Button avec texte -->
<div id="fab-container">
    <!--<span id="fab-text">Ajouter un fichier</span>-->
    <button id="fab" class="btn btn-primary rounded-circle">
        <i class="fas fa-plus"></i>
    </button>
</div>

<!-- Floating Action Button avec texte -->
<div class="container mt-4">
    <!-- ... Contenu existant ... -->

    <!-- Floating Action Button (FAB) pour la suppression -->
    <div id="deleteFabContainer" class="position-fixed bottom-0 end-0 mb-4 me-4">

        <button id="deleteFab" class="btn btn-danger rounded-circle">
            <i class="fas fa-trash"></i>
        </button>
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
                    Êtes-vous sûr de vouloir supprimer tous les fichiers du brouillon ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form action="{{ route('deletebrouillonfiles') }}" method="post">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ... Autres éléments existants ... -->
</div>




<!-- Formulaire d'ajout de fichier -->
<div id="fileForm" class="file-form">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Ajouter un fichier</h5>
            <form action="{{ route('storefile') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="type" value="brouillon">
                <div class="input-group mb-3">
                    <input type="file" name="fichier" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Uploader</button>
                <button type="button" id="closeForm" class="btn btn-secondary">Annuler</button>
            </form>
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
-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#deleteFab').click(function() {
            $('#confirmationModal').modal('show'); // Afficher le modal de confirmation
        });
    });
</script>

@endsection
<style>
    #fab-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    #fab-text {
        margin-bottom: 8px;
        font-size: 14px;
        color: #000;
    }


    #fab {
        width: 60px;
        height: 60px;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    }

    #fileForm {
        display: none;
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 300px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    }

    .file-form .card {
        margin: 0;
    }
</style>


<style>
    /* Style personnalisé pour le FAB */

#deleteFab {
    position: fixed;
    bottom: 110px;
    right: 20px;
    width: 60px;
    height: 60px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
}

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
