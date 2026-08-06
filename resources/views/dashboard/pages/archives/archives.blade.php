@extends('dashboard.acceuil')

@section('titre', 'Projets archivés')

@section('contenu')

@if(session('success'))
<div class="alert alert-primary alert-dismissible fade show">{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<p class="text-muted">
  Les projets terminés et rangés. Rien n'a été supprimé : ouvrez-en un pour
  consulter ses livrables, ses échanges et son historique, le reprendre, ou le
  présenter à un client.
</p>

<div class="row g-3">
  @forelse($projets as $projet)
  <div class="col-lg-4 col-md-6">
    <div class="card h-100">
      <div class="card-body d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start gap-2">
          <h5 class="card-title mb-1">{{ $projet->name }}</h5>
          <span class="badge bg-label-secondary">Archivé</span>
        </div>

        <p class="text-muted small mb-2">
          {{ $projet->department?->name ?? 'Sans département' }}
          @if($projet->createur)
            · {{ $projet->createur->surname }} {{ $projet->createur->name }}
          @endif
        </p>

        @if($projet->description)
          <p class="card-text">{{ Str::limit($projet->description, 110) }}</p>
        @endif

        <div class="text-muted small mb-3">
          {{ $projet->files_count }} livrable{{ $projet->files_count > 1 ? 's' : '' }}
          · rangé {{ $projet->updated_at->diffForHumans() }}
        </div>

        <a href="{{ route('projet.fiche', $projet) }}" class="btn btn-sm btn-primary mt-auto">
          Ouvrir le projet
        </a>
      </div>
    </div>
  </div>
  @empty
  <div class="col-12">
    <div class="card">
      <div class="card-body text-center py-5">
        <p class="text-muted mb-0">
          Aucun projet archivé pour le moment.<br>
          Un projet terminé se range depuis sa fiche, bouton « Archiver le projet ».
        </p>
      </div>
    </div>
  </div>
  @endforelse
</div>

<div class="mt-4">
  {{ $projets->links() }}
</div>

@endsection
