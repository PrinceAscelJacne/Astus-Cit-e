@extends('dashboard.acceuil')

@section('contenu')
<h1>Modifier un fichier</h1>

<!-- Modal pour la modification de fichier -->
<div class="modal fade" id="editFileModal" tabindex="-1" aria-labelledby="editFileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFileModalLabel">Modifier un fichier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editFileForm" method="POST" action="{{ route('updatefile') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="file_id" value="{{ $file->id }}"> <!-- ID du fichier -->
                    <input type="hidden" name="type" value="officiel">
                    <div class="form-group mb-3">
                        <label for="edit_project_id">Projet</label>
                        <select name="project_id" id="edit_project_id" class="form-control">
                            <!-- Options de projet -->
                            <option value="">Aucun</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ $project->id == $file->project_id ? 'selected' : '' }}>{{ $project->name }}</option>
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
@endsection

@section('scripts')
<script>
    @section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const editFileModal = new bootstrap.Modal(document.getElementById('editFileModal'));

    // Affiche le modal dès que la page est chargée
    editFileModal.show();

    // Assurez-vous que $file->path est bien défini et n'est pas nul
    @if(isset($file->path))
        const filePath = "{{ $file->filename }}"; // Utilisation du nom du fichier pour construire l'URL
        console.log("Chemin du fichier:", filePath);

        // Charger le contenu du fichier via AJAX lorsque le modal est affiché
        editFileModal._element.addEventListener('shown.bs.modal', function () {
            fetch(`/get-file-content/${encodeURIComponent(filePath)}`)
                .then(response => response.json())
                .then(data => {
                    if(data.content) {
                        // Injecter le contenu HTML dans le champ TinyMCE
                        tinymce.get('mytextarea').setContent(data.content);
                    } else {
                        console.error('Erreur:', data.error);
                    }
                })
                .catch(error => console.error('Erreur:', error));
        });
    @else
        console.error("Le chemin du fichier n'est pas défini.");
    @endif
});

</script>
@endsection

</script>
@endsection
