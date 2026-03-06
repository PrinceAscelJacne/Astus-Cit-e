@extends('dashboard.acceuil')

@section('contenu')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Profil/</span> Sessions de navigateur</h4>

    @if(session('status'))
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Sessions de navigateur</h5>
        </div>
        <div class="card-body">
            @if($sessions && count($sessions) > 0)
                <ul>
                    @foreach ($sessions as $session)
                        <li>
                            @if ($session->isMobile())
                                <i class="fa fa-mobile"></i>
                            @else
                                <i class="fa fa-desktop"></i>
                            @endif
                            {{ $session->ip_address }} - {{ $session->user_agent }} - {{ \Carbon\Carbon::parse($session->last_activity)->diffForHumans() }}
                            @if ($session->id === session()->getId())
                                <span>(This device)</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p>Aucune session active trouvée.</p>
            @endif
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h5 class="mb-0">Déconnexion des autres sessions</h5>
        </div>
        <div class="card-body">
            <form method="post" action="{{ route('profile.sessions.logoutOthers') }}">
                @csrf
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe actuel</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-dark">Se déconnecter des autres sessions</button>
            </form>
        </div>
    </div>


    @if(session('error1'))
        <div id="error1Alert" class="alert alert-danger alert-dismissible fade show " role="alert">
            {{ session('error1') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="mt-5 mx">
        <div class="row">
            <div class="col-12 ">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Supprimer votre compte</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-danger">
                            <strong>Attention :</strong> Cette action est irréversible. Toutes vos données seront définitivement supprimées.
                        </p>
                        <p>
                            Veuillez confirmer la suppression de votre compte en cliquant sur le bouton ci-dessous
                        </p>

                        {{-- Bouton pour afficher le modal de confirmation --}}
                        <button type="button" class="btn btn-danger" id="showDeleteModal">
                            Supprimer mon compte
                        </button>

                        {{-- Modal de confirmation --}}
                        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('delete-account') }}" method="POST" id="deleteAccountForm">
                                        @csrf

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmation de suppression</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.</p>
                                            <p>Entrez votre mot de passe pour confirmer :</p>
                                            <input type="password" class="form-control @error('password1') is-invalid @enderror" id="confirmPassword" name="password1" required>
                                            @error('password1')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var showDeleteModalBtn = document.getElementById('showDeleteModal');
            var confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));

            showDeleteModalBtn.addEventListener('click', function() {
                confirmDeleteModal.show(); // Afficher le modal de confirmation
            });

            // Optionnel : Pour cacher le modal si l'utilisateur annule
            var cancelBtn = document.querySelector('#confirmDeleteModal button[data-bs-dismiss="modal"]');
            cancelBtn.addEventListener('click', function() {
                confirmDeleteModal.hide();
            });

            var error1Alert = document.getElementById('error1Alert');
            if(error1Alert)
            {
                error1Alert.scrollIntoView({behavior: 'smooth', block: 'start'});
            }
        });
    </script>







@endsection
