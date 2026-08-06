@extends('dashboard.acceuil')

@section('contenu')

<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Site vitrine /</span> Témoignages
</h4>

@if(session('success'))
<div class="alert alert-primary alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- File d'attente : rien n'est visible sur le site tant qu'un témoignage
     n'a pas été explicitement publié depuis cette page. --}}
<div class="card mb-4">
  <h5 class="card-header d-flex align-items-center justify-content-between">
    <span><i class="bx bx-time-five me-1"></i> En attente de validation</span>
    <span class="badge bg-label-warning">{{ $enAttente->count() }}</span>
  </h5>

  <div class="card-body">
    @forelse ($enAttente as $temoignage)
    <div class="border rounded p-3 mb-3">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <strong>{{ $temoignage->nom }}</strong>
          @if ($temoignage->entreprise)
          <span class="text-muted">— {{ $temoignage->entreprise }}</span>
          @endif
          <div class="text-muted small">
            {{ $temoignage->service }} ·
            {{ $temoignage->created_at->diffForHumans() }}
          </div>
        </div>
        <div class="text-warning">
          @for ($i = 1; $i <= 5; $i++)
          <i class="bx {{ $i <= $temoignage->note ? 'bxs-star' : 'bx-star' }}"></i>
          @endfor
        </div>
      </div>

      <p class="mt-3 mb-3">{{ $temoignage->message }}</p>

      <div class="d-flex gap-2">
        <form method="POST" action="{{ route('temoignages.publier', $temoignage) }}">
          @csrf
          <button class="btn btn-sm btn-success">
            <i class="bx bx-check me-1"></i>Publier
          </button>
        </form>
        <form method="POST" action="{{ route('temoignages.refuser', $temoignage) }}">
          @csrf
          <button class="btn btn-sm btn-outline-secondary">
            <i class="bx bx-x me-1"></i>Refuser
          </button>
        </form>
        <form method="POST" action="{{ route('temoignages.destroy', $temoignage) }}"
              onsubmit="return confirm('Supprimer définitivement ce témoignage ?');">
          @csrf
          @method('DELETE')
          <button class="btn btn-sm btn-outline-danger">
            <i class="bx bx-trash me-1"></i>Supprimer
          </button>
        </form>
      </div>
    </div>
    @empty
    <p class="text-muted mb-0">Aucun témoignage en attente.</p>
    @endforelse
  </div>
</div>

<div class="card">
  <h5 class="card-header"><i class="bx bx-history me-1"></i> Témoignages traités</h5>

  <div class="table-responsive text-nowrap">
    <table class="table">
      <thead>
        <tr>
          <th>Auteur</th>
          <th>Service</th>
          <th>Note</th>
          <th>Statut</th>
          <th>Reçu</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($traites as $temoignage)
        <tr>
          <td>
            <strong>{{ $temoignage->nom }}</strong>
            @if ($temoignage->entreprise)
            <div class="text-muted small">{{ $temoignage->entreprise }}</div>
            @endif
          </td>
          <td>{{ $temoignage->service }}</td>
          <td>{{ $temoignage->note }}/5</td>
          <td>
            @if ($temoignage->statut === \App\Models\Temoignage::PUBLIE)
            <span class="badge bg-label-success">Publié</span>
            @else
            <span class="badge bg-label-secondary">Refusé</span>
            @endif
          </td>
          <td>{{ $temoignage->created_at->format('d/m/Y') }}</td>
          <td>
            <div class="d-flex gap-1">
              @if ($temoignage->statut === \App\Models\Temoignage::PUBLIE)
              <form method="POST" action="{{ route('temoignages.refuser', $temoignage) }}">
                @csrf
                <button class="btn btn-sm btn-outline-secondary">Retirer du site</button>
              </form>
              @else
              <form method="POST" action="{{ route('temoignages.publier', $temoignage) }}">
                @csrf
                <button class="btn btn-sm btn-outline-success">Publier</button>
              </form>
              @endif
              <form method="POST" action="{{ route('temoignages.destroy', $temoignage) }}"
                    onsubmit="return confirm('Supprimer définitivement ce témoignage ?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-muted">Aucun témoignage traité pour le moment.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="p-3">
    {{ $traites->links() }}
  </div>
</div>

@endsection
