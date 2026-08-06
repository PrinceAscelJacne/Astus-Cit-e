<!DOCTYPE html>
<html lang="fr" class="light-style" dir="ltr" data-theme="theme-default" data-assets-path="../assets/">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <base href="/public">
    <title>@yield('titre', 'Tableau de bord') — Astuscité</title>

    <link rel="icon" type="image/x-icon" href="assets/astuslogo.png" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    {{-- Thème Bootstrap : conservé car les vues de contenu (cartes, tableaux,
         fenêtres modales, menus déroulants) en dépendent encore. --}}
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
    <link href="frontend/assets/vendor/aos/aos.css" rel="stylesheet">

    {{-- Feuilles Astuscité, chargées en dernier pour ne pas être neutralisées
         par le thème. La coquille (barre latérale, en-tête) est écrite en CSS
         natif : le reset de Tailwind entrerait en conflit avec Bootstrap. --}}
    <link rel="stylesheet" href="../assets/css/astuscite-dashboard.css" />
    <link rel="stylesheet" href="../assets/css/astuscite-shell.css" />

    <script src="../assets/js/config.js"></script>
    <script src="https://cdn.tiny.cloud/1/7k09x2zuopjg2tg9jq79gaj42pl04w582x9xw0pg451sxhh3/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
  </head>

  <body class="as-app">

    <div class="as-shell" id="as-shell">

      @include('dashboard.parties.sidebar')

      <div class="as-voile" id="as-voile"></div>

      <div class="as-main">

        @include('dashboard.parties.topbar')

        <div class="as-contenu">
          @yield('contenu')
        </div>

        @include('dashboard.parties.footer')

      </div>
    </div>

    @yield('scripts')

    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="frontend/assets/vendor/aos/aos.js"></script>

    <script>
      AOS.init();

      // Ouverture de la barre latérale sur mobile.
      (function () {
        var shell = document.getElementById('as-shell');
        var burger = document.getElementById('as-burger');
        var voile = document.getElementById('as-voile');
        if (!shell || !burger) return;

        burger.addEventListener('click', function () {
          shell.classList.toggle('as-ouvert');
        });
        if (voile) {
          voile.addEventListener('click', function () {
            shell.classList.remove('as-ouvert');
          });
        }
        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') shell.classList.remove('as-ouvert');
        });
      })();

      // Éditeur enrichi des fichiers.
      document.addEventListener('DOMContentLoaded', function () {
        if (window.tinymce) {
          tinymce.init({
            selector: 'textarea#mytextarea',
            plugins: 'link image',
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | code',
            height: 300,
            menubar: true
          });
        }
      });
    </script>

  </body>
</html>
