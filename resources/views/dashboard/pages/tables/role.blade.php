
@extends('dashboard.acceuil')

@section('contenu')

<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Tables /</span> Table Role</h4>
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
              <!-- Basic Bootstrap Table -->
              <div class="card">
                <h5 class="card-header">Table Role</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nom</th>



                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{ $role->name }}</strong></td>
                            
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
@endsection

