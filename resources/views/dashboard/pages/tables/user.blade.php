
@extends('dashboard.acceuil')

@section('contenu')

<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Tables /</span> Table Utilisateurs</h4>
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
              <!-- Basic Bootstrap Table -->
              <div class="card">
                <h5 class="card-header">Table Utilisateurs</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Numéro</th>
                            <th>email</th>
                            <th>Departement</th>
                            <th>Role</th>
                            <th>Actions</th>

                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{ $user->name }}</strong></td>
                            <td><i class=" fa-lg text-danger me-3"></i> <strong>{{ $user->surname }}</strong></td>
                            <td><i class=" fa-lg text-danger me-3"></i> <strong>{{ $user->phone }}</strong></td>
                            <td><i class=" fa-lg text-danger me-3"></i> <strong>{{ $user->email }}</strong></td>
                            @if ($user->department_id != null)
                                <td>{{ $user->department->name }}</td>
                            @else
                                <td>Aucun</td>
                            @endif

                            <td><i class=" fa-lg text-danger me-3"></i> <strong>{{ $user->role->name }}</strong></td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{route('modifyuser', ['id' => $user->id])}}"><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        <a class="dropdown-item" href="{{ route('delete', ['table' => 'user','id' => $user->id]) }}" onclick="event.preventDefault(); document.getElementById('delete-project-form-{{ $user->id }}').submit();">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </a>
                                        <form id="delete-project-form-{{ $user->id }}" action="{{ route('delete', ['table' => 'users','id' => $user->id]) }}" method="POST" style="display: none;">
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
@endsection

