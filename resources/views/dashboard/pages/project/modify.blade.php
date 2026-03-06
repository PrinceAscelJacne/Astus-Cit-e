
@extends('dashboard.acceuil')

@section('contenu')
@extends('dashboard.acceuil')

@section('contenu')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Formulaire/</span> Modification de projet</h4>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Basic Layout & Basic with Icons -->
    <div class="row">
      <!-- Basic with Icons -->
      <div class="col-xxl">
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Remplissez les champs avec les nouvelles valeurs</h5>
            <small class="text-muted float-end"></small>
          </div>
          <div class="card-body">
            <form action="{{ route('updateproject', ['id' => $project->id]) }}" method="post">
                @csrf
                
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">Nom du projet</label>
                <div class="col-sm-10">
                  <div class="input-group input-group-merge">
                    <span id="basic-icon-default-fullname2" class="input-group-text">
                      <i class="bx bx-file"></i>
                    </span>
                    <input
                      type="text"
                      class="form-control"
                      id="basic-icon-default-fullname"
                      value="{{$project->name}}"
                      aria-label="Nom du projet"
                      aria-describedby="basic-icon-default-fullname2"
                      name="nom"

                    />
                  </div>
                </div>
              </div>

              <!-- Boutons Radio pour le Type de Projet -->
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="project-type">Type de projet</label>
                <div class="col-sm-10">
                  <div class="form-check form-check-inline">
                    <input
                      class="form-check-input"
                      type="radio"
                      name="projectType"
                      id="general"
                      value="general"
                      {{ $project->projectType == 'general' ? 'checked' : '' }}
                    />
                    <label class="form-check-label" for="general">
                      Projet général
                    </label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input
                      class="form-check-input"
                      type="radio"
                      name="projectType"
                      id="specific"
                      value="specific"
                      {{ $project->projectType == 'specific' ? 'checked' : '' }}
                    />
                    <label class="form-check-label" for="specific">
                      Projet spécifique
                    </label>
                  </div>
                </div>
              </div>

              <!-- Champ de Sélection pour les Projets Spécifiques -->
              <div class="row mb-3" id="specific-project-type-group" style="display: none;">
                <div class="offset-sm-2 col-sm-10">
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-list-ul"></i></span>
                    <select
                      id="specific-project-type"
                      class="form-control"
                      aria-label="Type de projet spécifique"
                      name="department"
                      {{ $project->projectType == 'specific' ? '' : 'disabled' }}
                    >
                    <option value="" disabled>Choisissez un type</option>
                    @foreach ($sections as $department)
                        <option value="{{ $department->id }}" {{ $project->department_id == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                    @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-icon-default-email">Image ou pièce jointe</label>
                <div class="col-sm-10">
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-paperclip"></i></span>
                    <input
                      type="file"
                      id="basic-icon-default-email"
                      class="form-control"
                      aria-label="Image ou pièce jointe"
                      aria-describedby="basic-icon-default-email2"
                      name="fichier"
                      value="{{$project->file}}"
                    />
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 form-label" for="basic-icon-default-message">Description</label>
                <div class="col-sm-10">
                  <div class="input-group input-group-merge">
                    <span id="basic-icon-default-message2" class="input-group-text">
                      <i class="bx bx-comment"></i>
                    </span>
                    <textarea
                      id="basic-icon-default-message"
                      class="form-control"
                      placeholder="Décrivez votre projet"
                      aria-label="Décrivez votre projet"
                      aria-describedby="basic-icon-default-message2"
                      name="description"
                      required
                    >{{ $project->description }}</textarea>
                  </div>
                </div>
              </div>
              <div class="row justify-content-end">
                <div class="col-sm-10">
                  <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- JavaScript pour gérer la visibilité du champ de sélection -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const projectTypeRadios = document.querySelectorAll('input[name="projectType"]');
  const specificProjectTypeGroup = document.getElementById('specific-project-type-group');
  const specificProjectTypeSelect = document.getElementById('specific-project-type');

  function toggleSpecificProjectType() {
    if (document.getElementById('specific').checked) {
      specificProjectTypeGroup.style.display = 'block';
      specificProjectTypeSelect.removeAttribute('disabled');
    } else {
      specificProjectTypeGroup.style.display = 'none';
      specificProjectTypeSelect.setAttribute('disabled', true);
      specificProjectTypeSelect.value = ''; // Réinitialiser la valeur du select
    }
  }

  // Ajouter un écouteur d'événements à chaque bouton radio
  projectTypeRadios.forEach(radio => {
    radio.addEventListener('change', toggleSpecificProjectType);
  });

  // Appeler la fonction au chargement de la page pour définir l'état initial
  toggleSpecificProjectType();
});
</script>

@endsection


