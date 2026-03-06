@extends('dashboard.acceuil')

@section('contenu')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Profil/</span> Informations</h4>
    <div class="col-xxl">
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
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Informations de profil</h5>
                <small class="text-muted float-end"></small>
            </div>

            <div class="card-body">
                <form method="post" action="{{ route('updateinfos') }}" id="form1">
                    @csrf

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nom</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Prénom</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="surname" value="{{ $user->surname }}" required/>
                            @error('surname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-default-email">Email</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ $user->email }}" required/>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Téléphone</label>
                        <div class="col-sm-10">
                            <input type="tel" name="phone" class="form-control phone-mask @error('phone') is-invalid @enderror" value="{{ $user->phone }}"/>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-dark">Modifier</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br /><br />
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Modifier votre mot de passe</h5>
                <small class="text-muted float-end">Assurez-vous d'utiliser un mot de passe relativement long pour une bonne sécurité de votre compte</small>
            </div>
            <div class="card-body">
                <form method="post" action="{{route('updatepassword')}}" id="form2">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Mot de passe actuel</label>
                        <div class="col-sm-10">
                            <input
                                type="password"
                                class="form-control @error('currentpass') is-invalid @enderror"
                                name="currentpass"
                                required
                            />
                            @error('currentpass')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nouveau mot de passe</label>
                        <div class="col-sm-10">
                            <input
                                type="password"
                                class="form-control @error('newpass') is-invalid @enderror"
                                name="newpass"
                                required
                            />
                            @error('newpass')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Confirmer nouveau mot de passe</label>
                        <div class="col-sm-10">
                            <input
                                type="password"
                                class="form-control @error('newpass_confirmation') is-invalid @enderror"
                                name="newpass_confirmation"
                                required
                            />
                            @error('newpass_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        </div>

                    </div>

                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-dark">Modifier</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // Fonction pour définir le focus sur le premier champ en erreur
        document.addEventListener('DOMContentLoaded', function() {

            const firstInvalidField = document.querySelector('.is-invalid');
            if (firstInvalidField) {
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
@endsection
