@extends('dashboard.acceuil')
@section('contenu')
<section>

    <div class="row">
        <div class="col-lg-8 mb-4 order-0" data-aos="fade-in" data-aos-delay="50">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7" >
                        <div class="card-body">
                            <h5 class="card-title text-primary">Bienvenue, {{ strtoupper(Auth::user()->name) }}! 🎉</h5>
                            @if (auth()->user()->role_id == 1 )
                                <p class="mb-4">
                                    Merci de vous être connecté ! Commençez la sauvegarde de vos fichiers : <span class="fw-bold"></span>
                                </p>

                                <a href="{{ route('viewfile') }}" class="btn btn-sm btn-outline-primary">Commencer </a>
                            @elseif (auth()->user()->role_id == 2)
                            <p class="mb-4">
                                <p class="mb-4">Merci de vous être connecté ! Vous pouvez gérer vos fichiers personnels ainsi que ceux du département. <span class="fw-bold"></span>
                            </p>
                            <a href="{{ route('viewfile') }}" class="btn btn-sm btn-outline-primary">Commencer </a>
                            @else
                            <p class="mb-4">
                                Merci de vous être connecté ! Commençez la sauvegarde de vos fichiers : <span class="fw-bold"></span>
                            </p>
                            <a href="{{ route('viewfile') }}" class="btn btn-sm btn-outline-primary">Commencer </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left" >
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="assets/img/illustrations/man-with-laptop-light.png"
                                 height="140"
                                 alt="View Badge User"
                                 data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                 data-app-light-img="illustrations/man-with-laptop-light.png"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 order-1">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-6 mb-4" data-aos="fade-right" data-aos-delay="100"  >
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="../assets/img/icons/unicons/wallet-info.png"
                                         alt="chart success"
                                         class="rounded"
                                    />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0"
                                            type="button"
                                            id="cardOpt3"
                                            data-bs-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false"
                                    >
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                        <a class="dropdown-item" href="{{route('viewproject')}}">Voir plus</a>
                                    </div>
                                </div>
                            </div>
                            <span>Projets en lice</span>
                            <h3 class="card-title mb-2 ps-4 ms-3">{{ $projects->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-6 mb-4" data-aos="fade-left" data-aos-delay="100" >
                    <div class="card">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="../assets/img/icons/unicons/chart-success.png"
                                         alt="Credit Card"
                                         class="rounded"
                                    />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0"
                                            type="button"
                                            id="cardOpt6"
                                            data-bs-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false"
                                    >
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                                        <a class="dropdown-item" href="{{route('viewfile')}}">Voir plus</a>
                                    </div>
                                </div>
                            </div>
                            <span>Mes fichiers</span>
                            <h3 class="card-title text-nowrap mb-1 ps-4 ms-3">{{ $files->count() }}</h3>
                            <small class="text-success fw-semibold"></i> </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <!-- Nouvelle carte pour consulter un fichier -->
        <div class="col-lg-4 col-md-4 order-2 mb-3" data-aos="fade-up" data-aos-delay="150">
            <div class="card">
                <div class="boss d-flex justify-content-center">
                    <img src="assets/ordi.png" class="card-img-top" style="height: 200px; width: 200px;" alt="Image Placeholder">
                </div>

                <div class="card-body">
                    <h5 class="card-title">Consulter vos fichiers de travail </h5>
                    <p class="card-text">Cliquez sur le bouton ci-dessous pour commencer.</p>
                    <a href="{{route('viewfile')}}" class="btn btn-primary btn-outline-primary"> Consulter <i class="bx bx-right-arrow-alt"></i></a>
                </div>
            </div>
        </div>
        <!-- Carte pour consulter les archives -->
        <div class="col-lg-4 col-md-4 order-3 mb-4" data-aos="fade-up" data-aos-delay="175">
            <div class="card">
                <div class="boss d-flex justify-content-center">
                    <img src="assets/archives.jpg" class="card-img-top" style="height: 200px; width: 200px;" alt="Image Placeholder">
                </div>
                <div class="card-body">
                    <h5 class="card-title">Consulter les archives</h5>
                    <p class="card-text">Cliquez sur le bouton ci-dessous pour voir les archives disponibles.</p>
                    <a href="{{route('archivesfiles')}}" class="btn btn-primary btn-outline-primary"> Archives <i class="bx bx-right-arrow-alt"></i></a>
                </div>
            </div>
        </div>
        <!-- Nouvelle carte pour consulter le brouillon -->
        <div class="col-lg-4 col-md-4 order-4 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card">
                <div class="boss d-flex justify-content-center">
                    <img src="assets/brouillon.webp" class="card-img-top" style="height: 200px; width: 200px;" alt="Image Placeholder">
                </div>
                <div class="card-body">
                    <h5 class="card-title">Consulter le brouillon</h5>
                    <p class="card-text">Cliquez sur le bouton ci-dessous pour voir le brouillon disponible.</p>
                    <a href="{{route('brouillonfiles')}}" class="btn btn-primary btn-outline-primary"> Brouillon <i class="bx bx-right-arrow-alt"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!--<div class="card" style="display: none">
        <div class="card-body">
            <h5 class="card-title"> hello Mervo ♥ How are you ? </h5>
            <p class="card-text" id="longText">
                Isaïe 41:10 <br>
                « Ne crains rien, car je suis avec toi; ne promène pas des regards inquiets, car je suis ton Dieu; je te fortifie, je viens à ton secours, je te soutiens de ma droite triomphante. »
                <br>
                Proverbes 3:5-6 <br>
                « Confie-toi en l'Éternel de tout ton cœur, et ne t'appuie pas sur ta propre intelligence. Reconnais-le dans toutes tes voies, et il aplanira tes sentiers. »
                <br>
                Psaume 46:1-3 <br>
                « Dieu est pour nous un refuge et un appui, un secours qui ne manque jamais dans la détresse. C'est pourquoi nous sommes sans crainte quand la terre est bouleversée, et que les montagnes chancellent au cœur des mers, quand les flots de la mer mugissent, écument, se soulèvent jusqu'à faire trembler les montagnes. »
                <br> Voilà mon message "Approche toi de Dieu et il s'approchera de toi " <br>
                Have a great day ♥
            </p>
            <a href="javascript:void(0);" id="toggleText" class="btn btn-primary btn-outline-primary">
                Voir plus <i class="bx bx-right-arrow-alt"></i>
            </a>
        </div>
    </div>-->
    <!--<div class="card" >
        <div class="card-body">
            <h5 class="card-title"> hello ♥ How are you ? </h5>
            <p class="card-text" id="longText">
                Ceci est un long texte pour Merveille qui sera réduit initialement. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin
                vitae justo nec arcu lacinia pharetra. Donec efficitur, urna a tempus feugiat, odio quam bibendum massa, et placerat
                orci nulla sed metus. Vivamus finibus, nunc at vestibulum vestibulum, enim felis facilisis augue, eu tempus sapien
                lectus eget est. Etiam iaculis, ligula nec ullamcorper auctor, nunc ligula cursus lacus, at tempor lorem magna nec
                lorem.
            </p>
            <a href="javascript:void(0);" id="toggleText" class="btn btn-primary btn-outline-primary">
                Voir plus <i class="bx bx-right-arrow-alt"></i>
            </a>
        </div>
    </div>-->


</section>
@endsection
<style>
    /* CSS pour gérer la réduction du texte */
.card-text {
    overflow: hidden;
    white-space: wrap;
    text-overflow: ellipsis;
}

.card-text.expanded {
    white-space: normal;
}

</style>
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const longText = document.getElementById('longText');
    const toggleText = document.getElementById('toggleText');

    const originalText = longText.innerHTML;
    const shortenedText = originalText.slice(0, 100) + '...';

    // Initial state
    longText.innerHTML = shortenedText;
    let isExpanded = false;

    toggleText.addEventListener('click', function() {
        if (isExpanded) {
            longText.innerHTML = shortenedText;
            toggleText.innerHTML = 'Voir plus <i class="bx bx-right-arrow-alt"></i>';
        } else {
            longText.innerHTML = originalText;
            toggleText.innerHTML = 'Voir moins <i class="bx bx-left-arrow-alt"></i>';
        }
        isExpanded = !isExpanded;
    });
});

</script>
@endsection





































































