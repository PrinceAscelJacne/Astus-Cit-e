@extends('dashboard.acceuil')

@section('contenu')
<h2 class="fw-bold py-3 mb-4 ms-4"><span class="text-muted fw-light">Mes fichiers/</span> Fichiers officiels</h2>
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
            <div class="col-md-3 mb-3">
                <select name="project_id" class="form-select">
                    <option value="">Filtrer par projet</option>
                    @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
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
            <h5>{{ $group }}</h5>
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
                    @if (pathinfo($file->filename, PATHINFO_EXTENSION) === 'txt' || pathinfo($file->filename, PATHINFO_EXTENSION) === 'docx'  )
                    <a href="{{route('modifyfile', $file->id)}}" > <button class="btn btn-sm btn-outline-secondary  ">Modifier</button> </a>
                    @endif
                    <a href="{{ route('downloadfile', $file->id) }}" > <button class="btn btn-sm btn-outline-secondary ">Télécharger</button> </a>
                    @if(optional($file->project)->status === 'archive')
                    <button class="btn btn-sm btn-outline-danger"  disabled>Supprimer</button>
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
        <p>Vous n'avez aucun fichier officiel uploadé et en cours ! Appuyez sur le bouton plus situé dans le coin inférieur droit pour en ajouter.</p>
        <img src="https://media.giphy.com/media/jAYUbVXgESSti/giphy.gif" alt="Aucun fichier trouvé" style="width: 400px">
    </div>
    @endif
</div>





<!-- Modal pour les erreurs -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Erreur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                La taille du fichier est trop grande. La taille limite autorisée est de 100 Mo.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>






<!-- Floating Action Buttons -->
<div id="fab-container">
    <!-- Edit FAB -->
        <div  id="fab-text-edit">Créer un fichier</div>
        <button id="fab-edit" class="btn btn-secondary rounded-circle">
            <i class="fas fa-edit"></i>
        </button>

    <!-- Add FAB -->
    <div id="fab-text">Ajouter un fichier</div>
    <button id="fab-add" class="btn btn-primary rounded-circle">
        <i class="fas fa-plus"></i>
    </button>
</div>




<!-- Modal pour la creation de fichier de fichier -->
<div class="modal fade" id="editFileModal" tabindex="-1" aria-labelledby="editFileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFileModalLabel">Créer un fichier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editFileForm" method="POST" action="{{route('storefile')}}" enctype="multipart/form-data">
                    @csrf
                    <!--<input type="hidden" name="file_id" id="edit-file-id">-->
                    <input type="hidden" name="type" value="officiel">
                    <div class="form-group mb-3">
                        <label for="edit_project_id">Projet</label>
                        <select name="project_id" id="edit_project_id" class="form-control">
                            <option value="">Aucun</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="file_content">Contenu du fichier</label>
                        <textarea name="file_content" id="mytextarea" class="form-control" rows="5" placeholder="Entrez le contenu du fichier ici..."></textarea>
                    </div>
                    <div class="d-flex justify-content-start mt-3">
                        <button type="submit" id="editFileButton" class="btn btn-primary me-4">Enregistrer</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Formulaire d'ajout de fichier -->
<div id="fileForm" class="file-form">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Ajouter un fichier</h5>
            <form action="{{ route('storefile') }}" id="uploadForm" method="POST" enctype="multipart/form-data">
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
                <input type="hidden" name="type" value="officiel">
                <div class="input-group mb-3">
                    <input type="file" name="fichier" id="fichier" class="form-control" accept=".jpg,.jpeg,.png,.mp4,.mp3,.zip,.pdf,.docx,.txt,.csv,.json,.md" required>
                </div>
                <div class="progress" id="progress" style="display: none;">
                    <div class="progress-bar" id="progressBar" style="width: 0%">0%</div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" id="uploadButton" class="btn btn-primary ">Uploader</button>
                    <button type="button" id="closeForm" class="btn btn-secondary">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', function() {




    const fabAdd = document.getElementById('fab-add');
    const fileForm = document.getElementById('fileForm');
    const closeForm = document.getElementById('closeForm');
    const uploadButton = document.getElementById('uploadButton');
    const progressBar = document.getElementById('progressBar');
    const progress = document.getElementById('progress');
    let xhr; // Déclarer xhr dans un scope plus large pour pouvoir l'annuler

    fabAdd.addEventListener('click', function() {
        fileForm.style.display = 'block';
    });

    closeForm.addEventListener('click', function() {
        fileForm.style.display = 'none';
        const form = document.getElementById('uploadForm');
        form.reset(); // Réinitialiser les champs du formulaire
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';
        progress.style.display = 'none';
        localStorage.removeItem('uploadProgress');

        // Annuler l'upload en cours si xhr est défini
        if (xhr) {
            xhr.abort();
            uploadButton.disabled = false; // Réactiver le bouton uploader
        }
    });

    // Récupération de la progression stockée dans le storage local
    const storedProgress = localStorage.getItem('uploadProgress');
    if (storedProgress) {
        progressBar.style.width = storedProgress + '%';
        progressBar.textContent = Math.floor(storedProgress) + '%';
        progress.style.display = 'block';
    }

    uploadButton.addEventListener('click', function(e) {
        e.preventDefault(); // Empêcher le formulaire de se soumettre normalement

        const fileInput = document.getElementById('fichier');
        const fileSize = fileInput.files[0]?.size / (1024 * 1024); // Taille en Mo
        const maxSize = 100; // Taille limite en Mo

        if (fileSize > maxSize) {
            // Afficher le modal d'erreur si le fichier est trop grand
            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
            return;
        }

        const formData = new FormData(document.getElementById('uploadForm'));
        xhr = new XMLHttpRequest(); // Créer une nouvelle instance de XMLHttpRequest

        xhr.open('POST', "{{ route('storefile') }}", true);
        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = percentComplete + '%';
                progressBar.textContent = Math.floor(percentComplete) + '%';

                // Stocker la progression dans le stockage local
                localStorage.setItem('uploadProgress', percentComplete);
                progress.style.display = 'block';
            }
        });

        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                localStorage.removeItem('uploadProgress');
                progressBar.style.width = '0%';
                progressBar.textContent = '0%';
                progress.style.display = 'none';
                fileForm.style.display = 'none';
                uploadButton.disabled = false; // Réactiver le bouton uploader
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    window.location.reload();
                } else {
                    alert('Erreur lors de l\'upload du fichier.');
                }
            } else {
                alert('Erreur lors de l\'upload du fichier.');
            }
        });

        xhr.addEventListener('error', function() {
            alert('Erreur lors de l\'upload du fichier.');
            uploadButton.disabled = false; // Réactiver le bouton uploader en cas d'erreur
        });

        // Désactiver le bouton uploader
        uploadButton.disabled = true;

        xhr.send(formData);
    });
});



</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const fabAdd = document.getElementById('fab-add');
    const fabEdit = document.getElementById('fab-edit');
    const fileForm = document.getElementById('fileForm');
    const editFileModal = new bootstrap.Modal(document.getElementById('editFileModal'));

    // Show file upload form
    fabAdd.addEventListener('click', function() {
        fileForm.style.display = 'block';
    });

    // Show edit file modal
    fabEdit.addEventListener('click', function() {
        editFileModal.show();
    });

    // Handle form submission
    document.getElementById('editFileFor').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('{{ route('storefile') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  alert('Fichier modifié avec succès.');
                  editFileModal.hide();
                  window.location.reload();
              } else {
                  alert('Erreur lors de la modification du fichier.');
              }
          }).catch(() => {
              alert('Erreur lors de la modification du fichier.');
          });
    });
});

</script>
<style>





#fab-container {
    position: fixed;
    bottom: 20px; /* Distance from the bottom of the viewport */
    right: 20px;  /* Distance from the right of the viewport */
    display: flex;
    flex-direction: column; /* Stack FABs vertically */
    align-items: center;
}

.fab-wrapper {
    display: flex;
    align-items: center;
    margin-bottom: 10px; /* Space between the FABs */
}

.fab-text {
    margin-right: 10px; /* Space between text and FAB */
    font-weight: bold;
    white-space: nowrap; /* Prevent text wrapping */
}

#fab-edit {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    z-index: 1050; /* Higher z-index to appear above other FAB */
    background-color: #6c757d; /* Custom color for edit FAB */
    color: #fff; /* Text color inside FAB */
}

#fab-add {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    z-index: 1040; /* Lower z-index to appear below edit FAB */
    background-color: #007bff; /* Custom color for add FAB */
    color: #fff; /* Text color inside FAB */
}

.file-form {
    display: none;
    position: fixed;
    bottom: 100px;
    right: 20px;
    width: 300px;
    z-index: 1050; /* Higher z-index to appear above other elements */
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
}



</style>
@endsection
