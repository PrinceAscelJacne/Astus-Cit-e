@extends('dashboard.acceuil')

@section('contenu')
<h2 class="fw-bold py-3 mb-4 ms-4"><span class="text-muted fw-light">Archives/</span> Fichiers archivés</h2>
<div class="container mt-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="row">
        @if ($files->isNotEmpty())
        @foreach($files as $file)
            @php
                $extension = pathinfo($file->filename, PATHINFO_EXTENSION);
                $iconClass = \App\Helpers\FileIconHelper::getIcon($extension);
            @endphp
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">

                        <h5 class="card-title">
                            <i class="fas {{ $iconClass }}"></i> {{ $file->filename }}
                        </h5>
                        @if (isset($file->project->name))
                            <p class="card-text">Projet: {{ optional($file->project)->name  }}</p>
                        @else
                            <p class="card-text">Fichier autonome</p>
                        @endif


                        <p class="card-text">Archivé le : {{ $file->updated_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="card-footer d-flex justify-content-between ps-3">
                        @if ($extension === 'txt')
                            <a href="" class="btn btn-sm btn-outline-primary me-1">Lire/Modifier</a>
                        @endif
                        <a href="{{route('downloadfile', $file->id)}}" class="btn btn-sm btn-outline-secondary me-1">Télécharger</a>


                    </div>
                </div>
            </div>
        @endforeach
        @else
            <div class="text-center">
                <p>Aucun de vos fichiers dans les achives !! </p>
                <img src="https://media.giphy.com/media/jAYUbVXgESSti/giphy.gif" alt="Aucun fichier trouvé">
            </div>

        @endif
    </div>
</div>

<!-- Floating Action Button -->


<!-- Formulaire d'ajout de fichier -->


@endsection
