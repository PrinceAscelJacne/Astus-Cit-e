<header class="as-entete">

    <button type="button" class="as-burger" id="as-burger" aria-label="Ouvrir le menu">
        <x-icon name="menu" style="width:22px;height:22px" />
    </button>

    <h1 class="as-entete__titre">@yield('titre', 'Tableau de bord')</h1>

    <a href="{{ route('accueil') }}" target="_blank" rel="noopener" class="as-entete__action">
        <x-icon name="lien-externe" style="width:17px;height:17px" />
        <span class="d-none d-sm-inline">Voir le site</span>
    </a>

</header>
