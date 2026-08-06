<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Réseaux sociaux
    |--------------------------------------------------------------------------
    |
    | Renseigner une adresse ici suffit à faire apparaître l'icône
    | correspondante sur le site. Tant qu'une valeur reste vide, l'icône est
    | affichée sans lien : elle ne recharge donc pas la page, ce que faisaient
    | les anciens href="" répétés à cinq endroits du template.
    |
    */

    'reseaux' => [
        'twitter' => env('ASTUSCITE_TWITTER', ''),
        'facebook' => env('ASTUSCITE_FACEBOOK', ''),
        'instagram' => env('ASTUSCITE_INSTAGRAM', ''),
        'linkedin' => env('ASTUSCITE_LINKEDIN', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pages légales
    |--------------------------------------------------------------------------
    |
    | À remplir lorsque les pages existeront. Les entrées vides ne sont pas
    | affichées plutôt que de pointer dans le vide.
    |
    */

    'pages_legales' => [
        'conditions' => env('ASTUSCITE_URL_CONDITIONS', ''),
        'confidentialite' => env('ASTUSCITE_URL_CONFIDENTIALITE', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Localisation
    |--------------------------------------------------------------------------
    |
    | Adresse affichée et lien cartographique associé.
    |
    */

    'adresse' => [
        'libelle' => env('ASTUSCITE_ADRESSE', 'Cotonou, Sikècodji 535022'),
        'carte' => env('ASTUSCITE_URL_CARTE', 'https://www.google.com/maps/search/?api=1&query=Sik%C3%A8codji%2C+Cotonou%2C+B%C3%A9nin'),
    ],

];
