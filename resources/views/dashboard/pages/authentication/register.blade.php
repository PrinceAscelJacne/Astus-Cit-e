@extends('dashboard.acceuil')

@section('contenu')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Formulaire/</span> Ajouter un utilisateur</h4>
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
    <!-- Form Layout -->
    <div class="row">
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Ajouter Un Utilisateur</h5>
                    <small class="text-muted float-end">Formulaire d'enregistrement</small>
                </div>
                <div class="card-body">


                    <form method="POST" action="{{route('storeauth')}}" id="userForm">
                        @csrf

                        <!-- Nom -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="name">Nom</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-user"></i></span>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Nom" maxlength="35" required>
                                </div>
                            </div>
                        </div>

                        <!-- Prénom -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="firstname">Prénom</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-user"></i></span>
                                    <input type="text" name="firstname" id="firstname" class="form-control" placeholder="Prénom" maxlength="35" required>
                                </div>
                            </div>
                        </div>

                        <!-- Numéro de téléphone -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="phone">Numéro de téléphone</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                    <input type="text" name="phone" id="phone" class="form-control" placeholder="Numéro de téléphone" required pattern="\d{8}" title="Le numéro de téléphone doit comporter 8 chiffres.">
                                </div>
                            </div>
                        </div>

                        <!-- Rôle utilisateur -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Rôle utilisateur</label>
                            <div class="col-sm-10 d-flex align-items-center">
                                <div class="form-check me-3">
                                    <input type="radio" name="role" id="role_user" value="{{1}}" class="form-check-input" checked>
                                    <label for="role_user" class="form-check-label">Employé</label>
                                </div>
                                @if (auth()->user()->role_id == 3)
                                <div class="form-check me-3">
                                    <input type="radio" name="role" id="role_admin" value="{{2}}" class="form-check-input">
                                    <label for="role_admin" class="form-check-label">Chef département</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="role" id="role_boss" value="{{3}}" class="form-check-input">
                                    <label for="role_boss" class="form-check-label">Boss</label>
                                </div>
                                @endif

                            </div>
                        </div>

                        <!-- Département -->
                        @if (auth()->user()->role_id == 2)
                        <input type="hidden" value="{{auth()->user()->department_id}}" name="department_id">
                        @elseif (auth()->user()->role_id == 3)
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="department_id">Département</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-building"></i></span>
                                    <select name="department_id" id="department_id" class="form-select" required >
                                        <option value="" selected >Sélectionnez un département</option>
                                        <!-- Remplacez ceci par une boucle pour afficher les départements depuis votre base de données -->
                                        @foreach ($dep as $dep)
                                        <option value="{{$dep->id}}">{{$dep->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- Email -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="email">Email</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" maxlength="100" required>
                                </div>
                            </div>
                        </div>

                        <!-- Mot de passe (généré automatiquement) -->
                        @php
                            $generatedPassword = Str::random(8); // Génère un mot de passe aléatoire de 8 caractères
                        @endphp
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="password">Mot de passe</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-lock"></i></span>
                                    <input type="text" name="password" id="password" value="{{ $generatedPassword }}" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
/* Ajoutez ce CSS à votre fichier CSS */
.card {
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    background-color: #007bff;
    color: white;
    padding: 20px;
    text-align: center;
    border-bottom: 5px solid #0056b3;
}

.btn-primary {
    background-color: #007bff;
    border: none;
    transition: background-color 0.3s;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.form-control, .form-select {
    border-radius: 5px;
}

.form-label {
    font-weight: bold;
    color: #333;
}

.mb-3 {
    margin-bottom: 1.5rem;
}

.d-grid {
    margin-top: 20px;
}

.input-group-text {
    background-color: #f0f0f0;
    border: none;
}

.input-group {
    box-shadow: 0 0 2px rgba(0,0,0,0.1);
}

.form-check-label {
    font-weight: bold;
}

.form-check-input {
    margin-top: 0.3rem;
    margin-right: 0.5rem;
}
</style>

@section('scripts')
<script>
// Validation JavaScript et désactivation du champ département si Boss est sélectionné
document.getElementById('userForm').addEventListener('submit', function(event) {
    const phoneInput = document.getElementById('phone');
    const phonePattern = /^\d{8}$/;
    if (!phonePattern.test(phoneInput.value)) {
        event.preventDefault();
        alert('Le numéro de téléphone doit comporter 8 chiffres.');
    }

    const emailInput = document.getElementById('email');
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(emailInput.value)) {
        event.preventDefault();
        alert('Veuillez entrer une adresse email valide.');
    }
});

document.querySelectorAll('input[name="role"]').forEach(roleInput => {
    roleInput.addEventListener('change', function() {
        const departmentSelect = document.getElementById('department_id');
        if (this.value === '3') {
            departmentSelect.disabled = true;
            departmentSelect.value = ''; // Clear the selected value
        } else {
            departmentSelect.disabled = false;
        }
    });
});

// Initial check on page load
document.addEventListener('DOMContentLoaded', function() {
    const selectedRole = document.querySelector('input[name="role"]:checked').value;
    const departmentSelect = document.getElementById('department_id');
    if (selectedRole === '3') {
        departmentSelect.disabled = true;
    } else {
        departmentSelect.disabled = false;
    }
});
</script>
@endsection
