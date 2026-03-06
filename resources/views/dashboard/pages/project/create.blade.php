@extends('dashboard.acceuil')

@section('contenu')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Formulaire/</span> Création de projet</h4>
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
            <h5 class="mb-0">Veillez à remplir les champs requis</h5>
            <small class="text-muted float-end"></small>
          </div>
          <div class="card-body">
            <form action="{{route('storeproject')}}" method="post" enctype="multipart/form-data">
                @csrf
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname ">Nom du projet</label>
                <div class="col-sm-10">
                  <div class="input-group input-group-merge">
                    <span id="basic-icon-default-fullname2" class="input-group-text">
                      <i class="bx bx-file"></i> <!-- Icône de fichier pour le nom du projet -->
                    </span>
                    <input
                        maxlength="80"
                      type="text"
                      class="form-control"
                      id="basic-icon-default-fullname"
                      placeholder="affiche publicitaire"
                      aria-label="Nom du projet"
                      aria-describedby="basic-icon-default-fullname2"
                      name="nom"
                    />
                  </div>
                </div>
              </div>
              @if (auth()->user()->role_id == 2)
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">Département</label>
                <div class="col-sm-10">
                  <div class="input-group input-group-merge">
                    <span id="basic-icon-default-fullname2" class="input-group-text">
                      <i class="bx bx-file"></i> <!-- Icône de fichier pour le nom du projet -->
                    </span>
                    <input
                      type="text"
                      class="form-control"
                      id="basic-icon-default-fullname"
                      placeholder="{{$sections}}"
                      aria-label=""
                      aria-describedby="basic-icon-default-fullname2"
                      name="department"
                      disabled
                    />
                    <input type="hidden" name="department" value="{{auth()->user()->department_id}}">
                  </div>
                </div>
              </div>
              @elseif (auth()->user()->role_id == 3)
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
                      checked
                    />
                    <label class="form-check-label" for="general">
                       <!-- Icône de monde pour projet général -->
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
                    />
                    <label class="form-check-label" for="specific">
                       <!-- Icône de catégorie pour projet spécifique -->
                      Projet spécifique
                    </label>
                  </div>
                </div>
              </div>

              <!-- Champ de Sélection pour les Projets Spécifiques -->
              <div class="row mb-3" id="specific-project-type-group" style="display: none;">
                <div class="offset-sm-2 col-sm-10"> <!-- Décalage pour aligner -->
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-list-ul"></i></span> <!-- Icône de liste pour type spécifique -->
                    <select
                      id="specific-project-type"
                      class="form-control"
                      aria-label="Type de projet spécifique"
                      name="department"
                      disabled
                      required
                    >
                    <option value="" disabled selected>Choisissez un département</option>
                    @foreach ($sections as $department)
                        <option value="{{$department->id}}">{{ $department->name }} </option>
                    @endforeach


                    </select>
                  </div>
                </div>
              </div>
              @endif
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-icon-default-email">Image ou pièce jointe <small>(Optionnel)</small></label>
                <div class="col-sm-10">
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-paperclip"></i></span> <!-- Icône de trombone pour image/pièce jointe -->
                    <input
                      type="file"
                      id="basic-icon-default-email"
                      class="form-control"
                      aria-label="Image ou pièce jointe"
                      aria-describedby="basic-icon-default-email2"
                      name="fichier"
                    />
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 form-label" for="basic-icon-default-message">Description</label>
                <div class="col-sm-10">
                  <div class="input-group input-group-merge">
                    <span id="basic-icon-default-message2" class="input-group-text">
                      <i class="bx bx-comment"></i> <!-- Icône de commentaire pour description -->
                    </span>
                    <textarea
                      id="basic-icon-default-message"
                      class="form-control"
                      placeholder="Décrivez votre projet"
                      aria-label="Décrivez votre projet"
                      aria-describedby="basic-icon-default-message2"
                      name="description"
                      required
                    ></textarea>
                  </div>
                </div>
              </div>
              <div class="row justify-content-end">
                <div class="col-sm-10">
                  <button type="submit" class="btn btn-primary">Envoyer</button>
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
