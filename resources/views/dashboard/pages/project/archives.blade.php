

@extends('dashboard.acceuil')

@section('contenu')

<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Projets/</span> Projets archivés</h4>
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
              <!-- Examples -->
              <div class="row mb-5">
                @if ($projects->isNotEmpty())
                @foreach ($projects as $project )
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                      <img class="card-img-top" src="../assets/img/elements/2.jpg" alt="Card image cap" />
                      <div class="card-body">
                        <h5 class="card-title">{{$project->name}}</h5>
                        <p class="card-text">
                          {{$project->description}}
                        </p>
                        <h4>Archivé le ......</h4>

                      </div>
                    </div>
                  </div>
                @endforeach
                @else
                <div class="text-center">
                    <p>Aucun projet n'a encore été archivé !!</p>
                    <img src="https://media.giphy.com/media/jAYUbVXgESSti/giphy.gif" alt="Aucun fichier trouvé">
                </div>

                @endif



              </div>

@endsection


