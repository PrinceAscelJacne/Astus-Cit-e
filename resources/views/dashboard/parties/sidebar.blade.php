@php
    /**
     * Navigation du back-office.
     *
     * Chaque entrée porte sa condition d'affichage, alignée sur le middleware
     * de route : un lien visible mène toujours quelque part. Auparavant,
     * certaines entrées s'affichaient pour tous les rôles et renvoyaient une
     * erreur 403 au clic.
     */
    $utilisateur = auth()->user();

    $enAttente = $utilisateur->isBoss()
        ? \App\Models\Temoignage::enAttente()->count()
        : 0;

    $messagesNonTraites = $utilisateur->isBoss()
        ? \App\Models\Frontmessage::where('status', 'Encours')->count()
        : 0;

    $sections = [
        [
            'titre' => null,
            'liens' => [
                ['route' => 'dashboard', 'actif' => 'dashboard', 'icone' => 'dashboard', 'libelle' => 'Tableau de bord', 'voir' => true],
            ],
        ],
        [
            'titre' => 'Espace de travail',
            'liens' => [
                ['route' => 'viewproject', 'actif' => 'viewproject', 'icone' => 'dossier', 'libelle' => 'Projets', 'voir' => true],
                ['route' => 'createproject', 'actif' => 'createproject', 'icone' => 'plus', 'libelle' => 'Créer un projet', 'voir' => ! $utilisateur->isEmploye()],
                ['route' => 'viewfile', 'actif' => 'viewfile', 'icone' => 'fichier', 'libelle' => 'Fichiers', 'voir' => true],
                ['route' => 'brouillonfiles', 'actif' => 'brouillonfiles', 'icone' => 'modifier', 'libelle' => 'Brouillons', 'voir' => true],
                ['route' => 'archivesfiles', 'actif' => 'archivesfiles', 'icone' => 'archive', 'libelle' => 'Archives', 'voir' => true],
            ],
        ],
        [
            'titre' => 'Administration',
            'liens' => [
                ['route' => 'utilisateurs', 'actif' => 'utilisateurs', 'icone' => 'utilisateurs', 'libelle' => 'Utilisateurs', 'voir' => $utilisateur->isBoss()],
                ['route' => 'registerauth', 'actif' => 'registerauth', 'icone' => 'cadenas', 'libelle' => 'Inscrire un utilisateur', 'voir' => ! $utilisateur->isEmploye()],
                ['route' => 'projects', 'actif' => 'projects', 'icone' => 'tableau', 'libelle' => 'Table des projets', 'voir' => $utilisateur->isBoss()],
                ['route' => 'files', 'actif' => 'files', 'icone' => 'tableau', 'libelle' => 'Table des fichiers', 'voir' => $utilisateur->isBoss()],
                ['route' => 'roles', 'actif' => 'roles', 'icone' => 'reglages', 'libelle' => 'Rôles', 'voir' => $utilisateur->isBoss()],
                ['route' => 'departements', 'actif' => 'departements', 'icone' => 'globe', 'libelle' => 'Départements', 'voir' => $utilisateur->isBoss()],
            ],
        ],
        [
            'titre' => 'Site vitrine',
            'liens' => [
                ['route' => 'frontend-dashboard', 'actif' => 'frontend-dashboard', 'icone' => 'cloche', 'libelle' => 'Messages & rendez-vous', 'voir' => $utilisateur->isBoss(), 'badge' => $messagesNonTraites],
                ['route' => 'temoignages', 'actif' => 'temoignages', 'icone' => 'etoile', 'libelle' => 'Témoignages', 'voir' => $utilisateur->isBoss(), 'badge' => $enAttente],
            ],
        ],
        [
            'titre' => 'Compte',
            'liens' => [
                ['route' => 'profileshower', 'actif' => 'profileshower', 'icone' => 'reglages', 'libelle' => 'Mon profil', 'voir' => true],
                ['route' => 'profilesessions', 'actif' => 'profilesessions', 'icone' => 'cadenas', 'libelle' => 'Mes sessions', 'voir' => true],
                ['route' => 'nouveautes', 'actif' => 'nouveautes', 'icone' => 'etincelle', 'libelle' => 'Nouveautés', 'voir' => true],
                ['route' => 'aide', 'actif' => 'aide', 'icone' => 'aide', 'libelle' => 'Aide', 'voir' => true],
            ],
        ],
    ];
@endphp

<aside class="as-barre" id="as-barre">

    <a href="{{ route('dashboard') }}" class="as-barre__marque">
        <img src="../assets/astuslog.png" alt="Astuscité">
    </a>

    <nav class="as-barre__nav">
        @foreach ($sections as $section)
            @php($visibles = array_filter($section['liens'], fn ($l) => $l['voir']))
            @continue(empty($visibles))

            @if ($section['titre'])
                <p class="as-barre__titre">{{ $section['titre'] }}</p>
            @endif

            @foreach ($visibles as $lien)
                <a href="{{ route($lien['route']) }}"
                   class="as-lien {{ request()->routeIs($lien['actif']) ? 'as-lien--actif' : '' }}">
                    <x-icon :name="$lien['icone']" />
                    <span class="as-lien__texte">{{ $lien['libelle'] }}</span>
                    @if (! empty($lien['badge']) && $lien['badge'] > 0)
                        <span class="as-badge">{{ $lien['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        @endforeach

        <p class="as-barre__titre">Le site</p>
        <a href="{{ route('accueil') }}" target="_blank" rel="noopener" class="as-lien">
            <x-icon name="lien-externe" />
            <span class="as-lien__texte">Voir le site</span>
        </a>
    </nav>

    <div class="as-barre__pied">
        <div class="as-profil">
            <span class="as-profil__pastille">
                {{ mb_strtoupper(mb_substr($utilisateur->surname ?? $utilisateur->name, 0, 1) . mb_substr($utilisateur->name, 0, 1)) }}
            </span>
            <div style="min-width:0;flex:1">
                <p class="as-profil__nom">{{ $utilisateur->surname }} {{ $utilisateur->name }}</p>
                <p class="as-profil__role">{{ \App\Models\Role::LIBELLES[$utilisateur->role_id] ?? 'Utilisateur' }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="as-deconnexion">
                <x-icon name="deconnexion" style="width:18px;height:18px" />
                Déconnexion
            </button>
        </form>
    </div>

</aside>
