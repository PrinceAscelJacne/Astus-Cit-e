@extends('dashboard.acceuil')

@section('contenu')
<h1 class="text-center mb-4">Tableau de Bord</h1>
<div class="container mt-4" id="boss">
    <br><br>

    <ul class="nav nav-tabs justify-content-center mb-4" id="myTab" role="tablist">
        <li class="nav-item " role="presentation" >
            <button class="nav-link active" id="rendezvous-tab" style="border-top-left-radius: 12px" data-bs-toggle="tab" data-bs-target="#rendezvous" type="button" role="tab" aria-controls="rendezvous" aria-selected="true">Rendez-vous</button>
        </li>
        <li class="nav-item " role="presentation"  >
            <button class="nav-link" style=" border-bottom-right-radius:12px" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab" aria-controls="messages" aria-selected="false">Messages</button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent" style="border-top-color: red">
        <!-- Onglet Rendez-vous -->
        <div class="tab-pane fade show active" id="rendezvous" role="tabpanel" aria-labelledby="rendezvous-tab">
            <div class="d-flex justify-content-end mb-3">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Trier par
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('dashboard', ['sort' => 'asc']) }}">Date à venir (plus proche en premier)</a></li>
                        <li><a class="dropdown-item" href="{{ route('dashboard', ['sort' => 'desc']) }}">Date à venir (plus lointaine en premier)</a></li>
                    </ul>
                </div>
            </div>

            <div>
                <h2 class="mb-3">Rendez-vous</h2>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rendezvous as $key => $group)
                                @foreach ($group as $rdv)
                                    <tr>
                                        <td>{{ $rdv->name }}</td>
                                        <td>{{ $rdv->email }}</td>
                                        <td>{{ $rdv->date }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary">Confirmer</a>
                                            <a href="#" class="btn btn-sm btn-danger">Supprimer</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Onglet Messages -->
        <div class="tab-pane fade" id="messages" role="tabpanel" aria-labelledby="messages-tab">
            <div>
                <h2 class="mb-3">Messages</h2>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Sujet</th>
                                <th>Message</th>
                                <th>Email</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($messages as $message)
                                <tr>
                                    <td>{{ $message->name }}</td>
                                    <td>{{ $message->subject }}</td>
                                    <td>{{ $message->message }}</td>
                                    <td>{{ $message->email }}</td>
                                    <td>{{ $message->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


<style>
    #boss{
        background-color: #ffffff; /* Fond blanc */
        border-radius: 17px;
    }

    .nav-tabs {
        border-bottom: 2px solid #007bff; /* Bordure basse bleue */
    }

    .nav-link {
        color: #007bff !important; /* Couleur du lien bleue */
    }

    .nav-link.active {
        color: white !important; /* Couleur du lien actif */
        background-color: #007bff !important; /* Fond bleu pour le lien actif */
        border-color: #007bff !important; /* Bordure bleue pour le lien actif */
    }

    .nav-link:hover {
        color: #0056b3 !important; /* Couleur du lien au survol */
    }

    .table th, .table td {
        vertical-align: middle; /* Centrer verticalement le contenu des cellules */
    }
</style>

