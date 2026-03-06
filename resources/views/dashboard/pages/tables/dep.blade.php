
@extends('dashboard.acceuil')

@section('contenu')

<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Tables /</span> Table Département</h4>
        @if(session('success'))
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

      <!-- Basic Bootstrap Table -->
      <div class="card">
        <h5 class="card-header">Table Departments</h5>
        <div class="table-responsive text-nowrap">
          <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @foreach($deps as $dep)
                <tr>
                    <td>{{ $dep->id }}</td>
                    <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{ $dep->name }}</strong></td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="" onclick="event.preventDefault();" id="modifier"><i class="bx bx-edit-alt me-1" ></i> Modifier</a>
                                <a class="dropdown-item" href="{{ route('delete', ['table' => 'departments','id' => $dep->id]) }}" onclick="event.preventDefault(); document.getElementById('delete-project-form-{{ $dep->id }}').submit();">
                                    <i class="bx bx-trash me-1"></i> Supprimer
                                </a>
                                <form id="delete-project-form-{{ $dep->id }}" action="{{ route('delete', ['table' => 'departments','id' => $dep->id]) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>

                    </td>
                </tr>
                @endforeach
            </tbody>
          </table>
        </div>
      </div>

<!-- Floating Action Button avec texte -->
<div id="fab-container">
    <span id="fab-text" style="font-size: 20px">Ajouter </span>
    <button id="fab" class="btn btn-primary rounded-circle">
        <i class="fas fa-plus"></i>
    </button>
</div>
<!-- Formulaire de modification de departement -->
<div id="modifform" class="file-form" style="display: none; position:fixed; z-index: 3000;">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Modifier un département</h5>
            <form action="" method="POST" >
                @csrf
                <label for="nom" class=" input-group mb-1">Nom</label>
                <input type="text" name="nom" id="nom" class="form-control input-group mb-3" maxlength="40" value="{{$dep->name}}">
                <button type="submit" class="btn btn-primary">Modifier</button>
                <button type="button" id="closeForm" class="btn btn-secondary">Annuler</button>
            </form>
        </div>
    </div>
</div>
<!-- Formulaire d'ajout de departement -->
<div id="fileForm" class="file-form">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Ajouter un département</h5>
            <form action="{{route('createdep')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="nom" class=" input-group mb-1">Nom</label>
                <input type="text" name="nom" id="nom" class="form-control input-group mb-3" maxlength="40">
                <button type="submit" class="btn btn-primary">Uploader</button>
                <button type="button" id="closeForm" class="btn btn-secondary">Annuler</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('fab').addEventListener('click', function() {
            document.getElementById('fileForm').style.display = 'block';
        });

        document.getElementById('closeForm').addEventListener('click', function() {
            document.getElementById('fileForm').style.display = 'none';
        });
        document.getElementById('modifier').addEventListener('click', function() {
            document.getElementById('modifform').style.display = 'block';
        });

        document.getElementById('closeForm').addEventListener('click', function() {
            document.getElementById('modifform').style.display = 'none';
        });
    });
</script>
@endsection

<style>
    #fab-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    #fab-text {
        margin-bottom: 8px;
        font-size: 14px;
        color: #000;
    }


    #fab {
        width: 60px;
        height: 60px;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    }
    #fileForm {
        display: none;
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 300px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    }

    .file-form .card {
        margin: 0;
    }
</style>

