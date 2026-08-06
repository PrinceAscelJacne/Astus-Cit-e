@extends('dashboard.acceuil')

@section('titre', $projet->name)

@section('contenu')

@if(session('success'))
<div class="alert alert-primary alert-dismissible fade show">{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if($errors->any())
<div class="alert alert-danger"><ul class="mb-0 ps-3">
  @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
</ul></div>
@endif

{{-- En-tête du projet --}}
<div class="card mb-4">
  <div class="card-body">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <h4 class="mb-0">{{ $projet->name }}</h4>
          @if($projet->estArchive())
            <span class="badge bg-label-secondary">Archivé</span>
          @endif
          @if($projet->estEnRetard())
            <span class="badge bg-danger">En retard</span>
          @endif
        </div>
        <p class="text-muted mb-0">
          {{ $projet->department?->name ?? 'Sans département' }} ·
          créé par {{ $projet->createur?->surname }} {{ $projet->createur?->name }}
          @if($projet->echeance) · échéance {{ $projet->echeance->format('d/m/Y') }} @endif
        </p>
        @if($projet->description)
          <p class="mt-2 mb-0">{{ $projet->description }}</p>
        @endif
      </div>

      @if($peutGerer)
      <form method="POST" action="{{ $projet->estArchive() ? route('projet.desarchiver', $projet) : route('projet.archiver', $projet) }}">
        @csrf
        <button class="btn btn-sm {{ $projet->estArchive() ? 'btn-primary' : 'btn-outline-primary' }}">
          {{ $projet->estArchive() ? 'Ressortir des archives' : 'Archiver le projet' }}
        </button>
      </form>
      @endif
    </div>

    {{-- Avancement --}}
    <div class="mt-4">
      <form method="POST" action="{{ route('projet.avancement', $projet) }}" class="row g-2 align-items-end">
        @csrf
        <div class="col-sm-4">
          <label class="form-label" for="avancement">Avancement</label>
          <select name="avancement" id="avancement" class="form-select form-select-sm">
            @foreach(\App\Models\Project::AVANCEMENTS as $cle => $libelle)
              <option value="{{ $cle }}" @selected($projet->avancement === $cle)>{{ $libelle }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-sm-4">
          <label class="form-label" for="echeance">Échéance</label>
          <input type="date" name="echeance" id="echeance" class="form-control form-control-sm"
                 value="{{ $projet->echeance?->format('Y-m-d') }}">
        </div>
        <div class="col-sm-4">
          <button class="btn btn-sm btn-primary">Mettre à jour</button>
        </div>
      </form>

      <div class="progress mt-3" style="height:8px">
        <div class="progress-bar" style="width: {{ $projet->avancementPourcent() }}%"></div>
      </div>
      <small class="text-muted">
        {{ $projet->avancementLisible() }}
        @if(!is_null($projet->partTachesFaites()))
          · {{ $projet->partTachesFaites() }}% des tâches faites
        @endif
      </small>
    </div>
  </div>
</div>

{{-- Onglets --}}
<ul class="nav nav-tabs" role="tablist">
  @foreach([
    'livrables' => 'Livrables (' . $livrables->count() . ')',
    'echanges' => 'Échanges (' . $discussions->count() . ')',
    'taches' => 'Tâches (' . $taches->where('statut','!=','fait')->count() . ')',
    'equipe' => 'Équipe (' . $projet->membres->count() . ')',
    'journal' => 'Journal',
  ] as $cle => $libelle)
  <li class="nav-item">
    <button type="button" class="nav-link @if($loop->first) active @endif"
            data-bs-toggle="tab" data-bs-target="#onglet-{{ $cle }}">{{ $libelle }}</button>
  </li>
  @endforeach
</ul>

<div class="tab-content">

  {{-- Livrables --}}
  <div class="tab-pane fade show active" id="onglet-livrables">
    @forelse($livrables as $fichier)
    <div class="d-flex flex-wrap align-items-center gap-3 border-bottom py-3">
      <div class="flex-grow-1" style="min-width:220px">
        <strong>{{ $fichier->filename }}</strong>
        <div class="text-muted small">
          {{ ucfirst($fichier->famille()) }} · {{ $fichier->tailleLisible() }} ·
          déposé par {{ $fichier->user?->surname }} {{ $fichier->user?->name }}
          {{ $fichier->created_at->diffForHumans() }}
          @if($fichier->version > 1) · version {{ $fichier->version }} @endif
        </div>
        @if($fichier->description)<div class="small">{{ $fichier->description }}</div>@endif
      </div>

      <form method="POST" action="{{ route('projet.fichier.visibilite', [$projet, $fichier]) }}"
            class="d-flex align-items-center gap-2">
        @csrf
        <select name="visibilite" class="form-select form-select-sm" style="width:auto"
                onchange="this.form.submit()">
          @foreach(\App\Models\File::VISIBILITES as $cle => $libelle)
            <option value="{{ $cle }}" @selected($fichier->visibilite === $cle)>{{ $libelle }}</option>
          @endforeach
        </select>
        @if($fichier->visibilite_imposee_par)
          <span class="badge bg-label-secondary" title="Visibilité fixée par la direction">imposée</span>
        @endif
      </form>

      <a href="{{ route('downloadfile', $fichier->id) }}" class="btn btn-sm btn-outline-primary">Télécharger</a>
    </div>
    @empty
    <p class="text-muted pt-4 mb-0">Aucun livrable visible pour vous sur ce projet.</p>
    @endforelse

    {{-- Dépôt rattaché au projet : contrairement au formulaire général de la
         page « Fichiers », le projet ne peut pas être oublié ici. --}}
    <form method="POST" action="{{ route('projet.livrable', $projet) }}"
          enctype="multipart/form-data" class="row g-2 mt-4">
      @csrf
      <div class="col-md-4">
        <label class="form-label" for="fichier">Déposer un livrable</label>
        <input type="file" name="fichier" id="fichier" class="form-control form-control-sm" required>
      </div>
      <div class="col-md-3">
        <label class="form-label" for="visibilite">Visible par</label>
        <select name="visibilite" id="visibilite" class="form-select form-select-sm">
          @foreach(\App\Models\File::VISIBILITES as $cle => $libelle)
            <option value="{{ $cle }}" @selected($cle === 'equipe')>{{ $libelle }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label" for="description">Description</label>
        <input type="text" name="description" id="description" class="form-control form-control-sm"
               placeholder="Facultatif" maxlength="255">
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-sm btn-primary w-100">Déposer</button>
      </div>
      <div class="col-12">
        <small class="text-muted">
          Images, vidéos, maquettes (psd, ai, indd), documents, archives — jusqu'à 512 Mo.
        </small>
      </div>
    </form>
  </div>

  {{-- Échanges --}}
  <div class="tab-pane fade" id="onglet-echanges">
    <div class="py-3">
      @forelse($discussions as $message)
      <div class="d-flex gap-3 mb-3">
        <span class="as-profil__pastille" style="background:var(--as-accent-fort)">
          {{ mb_strtoupper(mb_substr($message->auteur_nom ?: '?', 0, 1)) }}
        </span>
        <div>
          <div class="small text-muted">
            <strong class="text-body">{{ $message->auteur_nom ?: 'Compte supprimé' }}</strong>
            · {{ $message->created_at->diffForHumans() }}
            @if($message->file) · à propos de <em>{{ $message->file->filename }}</em> @endif
          </div>
          <div>{{ $message->corps }}</div>
        </div>
      </div>
      @empty
      <p class="text-muted mb-3">Rien n'a encore été dit sur ce projet.</p>
      @endforelse

      <form method="POST" action="{{ route('projet.message', $projet) }}" class="mt-3">
        @csrf
        <div class="mb-2">
          <label class="form-label" for="corps">Écrire un message</label>
          <textarea name="corps" id="corps" rows="3" class="form-control"
                    placeholder="Une consigne, une décision, une remarque…" required></textarea>
        </div>
        <select name="file_id" class="form-select form-select-sm mb-2" style="max-width:320px">
          <option value="">Sur le projet en général</option>
          @foreach($livrables as $fichier)
            <option value="{{ $fichier->id }}">À propos de {{ $fichier->filename }}</option>
          @endforeach
        </select>
        <button class="btn btn-sm btn-primary">Publier</button>
      </form>
    </div>
  </div>

  {{-- Tâches --}}
  <div class="tab-pane fade" id="onglet-taches">
    <div class="py-3">
      @forelse($taches as $tache)
      <div class="d-flex align-items-start gap-3 border-bottom py-2">
        <form method="POST" action="{{ route('projet.tache.bascule', [$projet, $tache]) }}">
          @csrf
          <button class="btn btn-sm {{ $tache->statut === 'fait' ? 'btn-primary' : 'btn-outline-primary' }}"
                  title="{{ $tache->statut === 'fait' ? 'Rouvrir' : 'Marquer comme faite' }}">
            <i class="bx {{ $tache->statut === 'fait' ? 'bx-check' : 'bx-square' }}"></i>
          </button>
        </form>
        <div class="flex-grow-1">
          <div @class(['text-decoration-line-through text-muted' => $tache->statut === 'fait'])>
            {{ $tache->titre }}
          </div>
          <div class="small text-muted">
            {{ $tache->statutLisible() }}
            @if($tache->assignee) · {{ $tache->assignee->surname }} {{ $tache->assignee->name }} @endif
            @if($tache->echeance) · pour le {{ $tache->echeance->format('d/m/Y') }} @endif
            @if($tache->estEnRetard()) <span class="text-danger">· en retard</span> @endif
            @if($tache->faite_le) · terminée {{ $tache->faite_le->diffForHumans() }} @endif
          </div>
        </div>
      </div>
      @empty
      <p class="text-muted mb-3">Aucune tâche pour l'instant.</p>
      @endforelse

      <form method="POST" action="{{ route('projet.tache', $projet) }}" class="row g-2 mt-3">
        @csrf
        <div class="col-md-5">
          <input type="text" name="titre" class="form-control form-control-sm"
                 placeholder="Ce qu'il y a à faire" required>
        </div>
        <div class="col-md-3">
          <select name="assignee_id" class="form-select form-select-sm">
            <option value="">Personne en particulier</option>
            @foreach($projet->membres as $membre)
              <option value="{{ $membre->id }}">{{ $membre->surname }} {{ $membre->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <input type="date" name="echeance" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
          <button class="btn btn-sm btn-primary w-100">Ajouter</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Équipe --}}
  <div class="tab-pane fade" id="onglet-equipe">
    <div class="py-3">
      @forelse($projet->membres as $membre)
      <div class="d-flex align-items-center gap-3 border-bottom py-2">
        <span class="as-profil__pastille" style="background:var(--as-accent-fort)">
          {{ mb_strtoupper(mb_substr($membre->surname ?: $membre->name, 0, 1)) }}
        </span>
        <div class="flex-grow-1">
          <strong>{{ $membre->surname }} {{ $membre->name }}</strong>
          <div class="small text-muted">
            {{ $membre->pivot->role_projet === 'responsable' ? 'Responsable' : 'Contributeur' }}
            · {{ $membre->email }}
          </div>
        </div>
        @if($peutGerer)
        <form method="POST" action="{{ route('projet.membre.retirer', [$projet, $membre]) }}"
              onsubmit="return confirm('Retirer cette personne du projet ?');">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-outline-danger">Retirer</button>
        </form>
        @endif
      </div>
      @empty
      <p class="text-muted mb-3">Personne n'est encore affecté à ce projet.</p>
      @endforelse

      @if($peutGerer)
      <form method="POST" action="{{ route('projet.membre', $projet) }}" class="row g-2 mt-3">
        @csrf
        <div class="col-md-6">
          <select name="user_id" class="form-select form-select-sm" required>
            <option value="">Choisir une personne</option>
            @foreach($candidats as $candidat)
              <option value="{{ $candidat->id }}">{{ $candidat->surname }} {{ $candidat->name }} — {{ $candidat->email }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="role_projet" class="form-select form-select-sm">
            <option value="contributeur">Contributeur</option>
            <option value="responsable">Responsable</option>
          </select>
        </div>
        <div class="col-md-3">
          <button class="btn btn-sm btn-primary w-100">Ajouter au projet</button>
        </div>
      </form>
      @endif
    </div>
  </div>

  {{-- Journal --}}
  <div class="tab-pane fade" id="onglet-journal">
    <div class="py-3">
      @forelse($journal as $entree)
      <div class="border-bottom py-2">
        <div>{{ $entree->resume() }}</div>
        <div class="small text-muted">{{ $entree->created_at->diffForHumans() }}</div>
      </div>
      @empty
      <p class="text-muted mb-0">Aucune activité enregistrée.</p>
      @endforelse
    </div>
  </div>

</div>

@endsection
