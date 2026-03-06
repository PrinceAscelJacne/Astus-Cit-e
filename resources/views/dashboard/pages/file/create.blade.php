@extends('dashboard.acceuil')

@section('contenu')

<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Formulaire/</span> Stockage de fichiers</h4>
<div class="col-xl">
    <div class="card mb-4">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Stockez ici vos fichiers de travail officiels.</h5>
            <small class="text-muted float-end"></small>
        </div>
        <div class="card-body">
            <form method="post" action="{{route('storefile')}}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="projet">Sélectionnez le projet de travail :</label>
                    <select class="form-select" id="projet" name="projet" >
                        <option value="">Aucun <div class=""></div></option>
                        @foreach ($projects as $projet )
                        <option value="{{$projet->id}}">{{$projet->name}}</option>
                        @endforeach


                        <!-- Ajoutez d'autres projets si nécessaire -->
                    </select>
                </div>
                <input type="hidden" name="type" value="{{'officiel'}}">
                <div class="mb-3">
                    <label class="form-label" for="fichier">Soumettez votre fichier :</label>
                    <input type="file" class="form-control" id="fichier" name="fichier" required />
                </div>
                <button type="submit" class="btn btn-primary">Soumettre</button>
            </form>
        </div>
    </div>
</div>
@endsection
