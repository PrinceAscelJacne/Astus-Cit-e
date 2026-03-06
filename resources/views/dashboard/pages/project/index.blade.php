@extends('dashboard.acceuil')

@section('contenu')

<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Projets/</span> Projets en cours</h4>
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Formulaire de recherche et de tri -->
<form method="GET" action="{{ route('viewproject') }}" class="mb-4">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Rechercher un projet" value="{{ request('search') }}">
        <select name="sort_by_date" class="form-select">
            <option value="">Trier par date de création</option>
            <option value="asc" {{ request('sort_by_date') == 'asc' ? 'selected' : '' }}>Date croissante</option>
            <option value="desc" {{ request('sort_by_date') == 'desc' ? 'selected' : '' }}>Date décroissante</option>
        </select>
        <button type="submit" class="btn btn-warning" >Rechercher</button>
    </div>
</form>

<!-- Affichage des projets -->
<div class="accordion " id="projectsAccordion">
    @if ($groupedProjects->isNotEmpty())
        @foreach ($groupedProjects as $dateGroup => $projects)
            <div class="accordio-item" style="background-color: transparent; " >
                <h2 class="accordion-header" id="heading{{ Str::slug($dateGroup) }}" >
                    <button class="accordion-button"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ Str::slug($dateGroup) }}" aria-expanded="true" aria-controls="collapse{{ Str::slug($dateGroup) }}">
                        {{ $dateGroup }}
                    </button>
                </h2>
                <div style="height: 20px">

                </div>
                <div id="collapse{{ Str::slug($dateGroup) }}" class="accordion-collapse collapse show" aria-labelledby="heading{{ Str::slug($dateGroup) }}" data-bs-parent="#projectsAccordion">
                    <div class="accordion-body" >
                        <div class="row">
                            @foreach ($projects as $project)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <img class="card-img-top" src="../assets/img/elements/2.jpg" alt="Card image cap" />
                                        <div class="card-body">
                                            @if ($project->department)
                                            <h6>Secteur {{$project->department->name}}</h6>
                                            @else
                                            <h6>Projet général</h6>
                                            @endif

                                            <h5 class="card-title">{{$project->name}}</h5>
                                            <p class="card-text">
                                                {{$project->description}}
                                            </p>

                                            @if (auth()->user()->id == $project->user_id || auth()->user()->role_id == 3 )
                                            <a href="index.html" onclick="event.preventDefault(); document.getElementById('archiverprojet{{$project->id}}').submit();" class="btn btn-outline-primary">Archiver le projet</a>
                                            <form id="archiverprojet{{$project->id}}" action="{{ route('archiverproject', ['id' => $project->id]) }}" method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center">
            <p>Vous n'avez aucun projet officiel uploadé et en cours ! Appuyez sur le bouton plus situé dans le coin inférieur droit pour en ajouter.</p>
            <img src="https://media.giphy.com/media/jAYUbVXgESSti/giphy.gif" alt="Aucun projet trouvé" style="width: 400px;">
        </div>
    @endif
</div>

@endsection


<style>


    .card-img-top {
        height: 250px; /* Augmenter la hauteur de l'image */
        object-fit: cover;
    }
    .card {
        transition: transform 0.3s ease;
        min-height: 400px; /* Définir une hauteur minimale pour les cartes */
    }
    .card:hover {
        transform: scale(1.05);
    }
    .input-group {
        max-width: 600px;
        margin: 0 auto;
    }
    .accordion-button,
    .accordion-body {
        background-color: transparent; /* Fond totalement transparent */
    }
</style>
