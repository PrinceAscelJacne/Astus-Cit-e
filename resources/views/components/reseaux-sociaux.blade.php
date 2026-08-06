@props(['class' => 'social'])

{{--
  Icônes de réseaux sociaux.

  Une adresse renseignée dans config/astuscite.php produit un vrai lien, ouvert
  dans un nouvel onglet. Une adresse vide produit une icône inerte : sans cela,
  un href="" rechargeait la page à chaque clic.
--}}
<div {{ $attributes->merge(['class' => $class]) }}>
    @foreach ([
        'twitter' => 'bi-twitter-x',
        'facebook' => 'bi-facebook',
        'instagram' => 'bi-instagram',
        'linkedin' => 'bi-linkedin',
    ] as $reseau => $icone)
        @php($url = config("astuscite.reseaux.$reseau"))

        @if ($url)
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
               aria-label="{{ ucfirst($reseau) }}">
                <i class="bi {{ $icone }}"></i>
            </a>
        @else
            <span class="social-inactif" aria-hidden="true" title="{{ ucfirst($reseau) }} — à venir">
                <i class="bi {{ $icone }}"></i>
            </span>
        @endif
    @endforeach
</div>
