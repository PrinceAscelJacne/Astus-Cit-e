<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Astuscité</title>
  <meta content="" name="description">
  <meta content="" name="keywords">


  <!-- Favicons -->
  <link href="frontend/assets/img/astuslogo.png" rel="icon">
  <link href="frontend/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="frontend/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="frontend/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="frontend/assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="frontend/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="frontend/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="frontend/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
    <style>
      section.map .info-item {
        text-align: center;
        padding: 40px 30px;
        border-radius: 5px;
        background-color: #fff;
        box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        margin-top: -60px;
        margin-left: 15px;
        margin-right: 15px;
        position: relative;
        z-index: 2;
        transition: all .5s;
      }

      section.map .info-item:hover {
        margin-top: -70px;
      }

      section.map .info-item i {
        font-size: 36px;
        color: #43ba7f;
        margin-bottom: 25px;
      }

      section.map .info-item h4 {
        font-size: 18px;
        color: #212741;
        font-weight: 600;
        margin-bottom: 12px;
      }

      section.map .info-item span {
        font-size: 15px;
        font-weight: 600;
        color: #43ba7f;
        transition: all 0.3s;
      }

      section.map .info-item a:hover {
        color: #ff511a;
      }

      #contact1 input {
        width: 100%;
        height: 50px;
        background-color: #f7f7f7;
        color: #212741;
        border-radius: 5px;
        border: none;
        padding: 0px 15px;
        font-size: 14px;
        outline: none;

      }

      #contact1 textarea {
        width: 100%;
        height: 180px;
        max-height: 24px;
        min-height: 150px;
        background-color: #f7f7f7;
        color: #212741;
        border-radius: 5px;
        border: none;
        padding: 15px 15px;
        font-size: 14px;
        outline: none;
        margin-bottom: 30px;
      }

      #contact1 input::placeholder,
      #contact1 textarea::placeholder {
        color: #212741;
      }
      #contact1 button.orange-button {
        font-size: 14px;
        color: #fff;
        background-color: #ff511a;
        padding: 12px 30px;
        display: inline-block;
        border-radius: 5px;
        font-weight: 500;
        text-transform: capitalize;
        letter-spacing: 0.5px;
        border: none;
        transition: all .3s;
      }

      #contact1 button.orange-button:hover {
        background-color: #43ba7f;
      }

      @media(max-width: 992px){
        section.map .info-item {
          margin-top: 30px;
        }
      }
    </style>
    <style>
        /* Style pour le preloader */
        #preloader1 {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        }

        /* Style pour le spinner */
        .spinner {
        border: 16px solid #f3f3f3; /* Light grey */
        border-top: 16px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
        }

        /* Animation du spinner */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }



    </style>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="frontend/assets/css/main.css" rel="stylesheet">
  <!-- =======================================================
  * Template Name: Medilab
  * Template URL: https://bootstrapmade.com/medilab-free-medical-bootstrap-theme/
  * Updated: Jun 14 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

  <header id="header" class="header sticky-top">


    <div class="branding d-flex align-items-center">

      <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="index.html" class="logo d-flex align-items-center me-auto">
          <!-- Uncomment the line below if you also wish to use an image logo -->
          <!-- <img src="assets/img/logo.png" alt="">
          <h1 class="sitename">Astuscite</h1>-->
          <img src="frontend/assets/astuslog.png" alt="" style="max-width: 400px; ;">
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="#hero" class="active">Accueil<br></a></li>
            <li><a href="#about">A propos</a></li>
            <li><a href="#services">Services</a></li>


            <li><a href="#contact">Contactez-nous</a></li>
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <a class="cta-btn d-none d-sm-block" href="#appointment">Commencer</a>

      </div>

    </div>

  </header>

  <main class="main">
    <!-- Hero Section -->
    <section id="hero" class="hero section">

      <img src="frontend/assets/img/bg26.jpg" alt="" data-aos="fade-in">

      <div class="container position-relative">

        <div class="welcome position-relative" id="welcome" data-aos="fade-down" data-aos-delay="100">
          <h2 data-aos="fade-right" data-aos-delay="50">BIENVENUE CHEZ ASTUSCITE</h2><br>
          <p data-aos="fade-left" data-aos-delay="50" class="col-sm-12" >

            Nous sommes une entreprise de communication avec une équipe de designers talentueux créant des
            affiches, des panneaux publicitaires, des livres et des solutions de communication visuelles attrayantes.

          </p>
        </div><!-- End Welcome -->

        <div class="content row gy-4">
          <div class="col-lg-4 d-flex align-items-stretch">
            <div class="why-box" data-aos="zoom-out" data-aos-delay="200">
              <h3>Pourquoi nous choisir ?</h3>
              <p >
                Nous croyons en la puissance du design pour transformer votre message en impact visuel. Notre équipe dédiée combine créativité et expertise pour offrir des solutions sur mesure adaptées à vos besoins.
              </p>
              <div class="text-center">
                <a href="#about" class="more-btn"><span>En savoir plus</span> <i class="bi bi-chevron-right"></i></a>
              </div>
            </div>
          </div><!-- End Why Box -->

          <div class="col-lg-8 d-flex align-items-stretch">
            <div class="d-flex flex-column justify-content-center">
              <div class="row gy-4">

                <div class="col-xl-4 d-flex align-items-stretch">
                  <div class="icon-box" data-aos="zoom-out" data-aos-delay="300">
                    <i class="fa-solid fa-paint-brush"></i>
                    <h4>Création de contenu original</h4>
                    <p>Que ce soit des affiches, des brochures ou des livres, nous créons du contenu unique qui reflète votre identité.</p>
                  </div>
                </div><!-- End Icon Box -->

                <div class="col-xl-4 d-flex align-items-stretch">
                  <div class="icon-box" data-aos="zoom-out" data-aos-delay="400">
                    <i class="fa-solid fa-pencil-ruler"></i>
                    <h4>Conception graphique de qualité</h4>
                    <p>Nous nous engageons à fournir des designs exceptionnels qui captivent et inspirent votre audience.</p>
                  </div>
                </div><!-- End Icon Box -->

                <div class="col-xl-4 d-flex align-items-stretch">
                  <div class="icon-box" data-aos="zoom-out" data-aos-delay="500">
                    <i class="fa-solid fa-bullhorn"></i>
                    <h4>Solutions personnalisées</h4>
                    <p>Nos services sont conçus pour répondre spécifiquement à vos besoins en communication et design.</p>
                  </div>
                </div><!-- End Icon Box -->

              </div>
            </div>
          </div>
        </div><!-- End  Content-->

      </div>

    </section><!-- /Hero Section -->


    <!-- About Section -->
    <section id="about" class="about section">

      <div class="container">

        <div class="row gy-4 gx-5">

          <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up" data-aos-delay="200">
            <img src="frontend/assets/img/bg3.jpg" class="img-fluid" alt="" style="height: 500px; width: 500px">
            <a href="frontend/assets/first.mp4" class="glightbox play-btn"></a>
          </div>
          

          <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
            <h3>À propos de nous</h3>
            <p>
              Nous sommes passionnés par le design et la communication. Notre mission
              est de vous aider à raconter votre histoire de manière visuelle et impactante.
              Avec une équipe diversifiée et expérimentée, nous offrons des solutions
              innovantes et efficaces.
            </p>
            <ul>
              <li>
                <i class="fa-solid fa-palette"></i>
                <div>
                  <h5>Des designs créatifs et uniques</h5>
                  <p>Nous créons des affiches, des flyers, des brochures et des livres qui captivent et inspirent votre audience.</p>
                </div>
              </li>
              <li>
                <i class="fa-solid fa-book"></i>
                <div>
                  <h5>Solutions personnalisées pour chaque projet</h5>
                  <p>Nos services sont conçus pour répondre spécifiquement à vos besoins en communication et design, en assurant des résultats personnalisés et efficaces.</p>
                </div>
              </li>
              <li>
                <i class="fa-solid fa-pen-nib"></i>
                <div>
                  <h5>Une équipe expérimentée et diversifiée</h5>
                  <p>Nos designers et communicateurs sont experts dans leur domaine, offrant des solutions innovantes pour tous vos projets.</p>
                </div>
              </li>
            </ul>
          </div>

        </div>

      </div>

    </section><!-- /About Section -->

      <!-- Section des Statistiques -->
      <section id="stats" class="stats section">

        <div class="container" data-aos="fade-up" data-aos-delay="100">

          <div class="row gy-4">

            <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
              <i class="fa-solid fa-users"></i>
              <div class="stats-item">
                <span data-purecounter-start="0" data-purecounter-end="45" data-purecounter-duration="1" class="purecounter"></span>
                <p>Designers</p>
              </div>
            </div><!-- Fin de l'élément de statistiques -->

            <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
              <i class="fa-solid fa-paint-brush"></i>
              <div class="stats-item">
                <span data-purecounter-start="0" data-purecounter-end="30" data-purecounter-duration="1" class="purecounter"></span>
                <p>Projets Réalisés</p>
              </div>
            </div><!-- Fin de l'élément de statistiques -->

            <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
              <i class="fa-solid fa-lightbulb"></i>
              <div class="stats-item">
                <span data-purecounter-start="0" data-purecounter-end="15" data-purecounter-duration="1" class="purecounter"></span>
                <p>Idées Générées</p>
              </div>
            </div><!-- Fin de l'élément de statistiques -->

            <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
              <i class="fa-solid fa-handshake"></i>
              <div class="stats-item">
                <span data-purecounter-start="0" data-purecounter-end="250" data-purecounter-duration="1" class="purecounter"></span>
                <p>Clients Servis</p>
              </div>
            </div><!-- Fin de l'élément de statistiques -->

          </div>

        </div>

      </section><!-- /Section des Statistiques -->


    <!-- Services Section -->
    <section id="services" class="services section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Nos services</h2>
        <p>Nous proposons une gamme complète de services pour répondre à tous vos besoins en communication :</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4 col-md-6 " data-aos="fade-up" data-aos-delay="100" >
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-paint-brush"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Affiches et Flyers</h3>
              </a>
              <p>Des conceptions attrayantes pour promouvoir vos événements, produits ou services.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-pencil-ruler"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Design Graphique</h3>
              </a>
              <p>Création de logos, identités visuelles, et supports marketing.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-book"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Livres et Brochures</h3>
              </a>
              <p>De la conception à l'impression, nous créons des livres et brochures qui marquent les esprits.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-ad"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Supports Marketing</h3>
              </a>
              <p>Conception de brochures, catalogues, et autres supports de communication visuelle.</p>
              <a href="#" class="stretched-link"></a>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-palette"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Illustrations Personnalisées</h3>
              </a>
              <p>Création d'illustrations uniques pour vos projets spéciaux.</p>
              <a href="#" class="stretched-link"></a>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-id-badge"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Identité Visuelle</h3>
              </a>
              <p>Développement de votre charte graphique complète pour assurer une cohérence visuelle.</p>
              <a href="#" class="stretched-link"></a>
            </div>
          </div><!-- End Service Item -->

        </div>

      </div>

    </section><!-- /Services Section -->

    <!-- rendez-vous Section -->
    <section id="appointment" class="appointment section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Prendre rendez-vous</h2>
        <p>Pour discuter de vos projets ou pour toute autre demande, veuillez remplir le formulaire ci-dessous.</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <form action="{{route('storerdv')}}" method="post"  class="">
            @csrf
          <div class="row">
            <div class="col-md-6 form-group">
              <input type="text" name="name1" class="form-control rounded-2 @error('name1') is-invalid @enderror" id="name" placeholder="Votre Nom" required="">
              @error('name1')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 form-group mt-3 mt-md-0">
              <input type="email" class="form-control rounded-2 @error('email1') is-invalid @enderror" name="email1" id="email" placeholder="Votre Email" required="">
              @error('email1')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 form-group mt-3 ">
              <input type="tel" class="form-control rounded-2 @error('phone1') is-invalid @enderror" name="phone1" id="phone" placeholder="Votre Téléphone" required="" number >
              @error('phone1')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 form-group mt-3">
              <input type="datetime-local" name="date1" class="form-control rounded-2 datepicker @error('date1') is-invalid @enderror" id="date" placeholder="Date et heure" required="">
              @error('date1')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 form-group mt-3">
              <!--<select name="department" id="department" class="form-select rounded-2" required="">
                <option value="">Selectionnez un service</option>
                <option value="Department 1">Department 1</option>
                <option value="Department 2">Department 2</option>
                <option value="Department 3">Department 3</option>
              </select>-->
            </div>
            <div class="col-md-4 form-group mt-3">
              <!--<select name="doctor" id="doctor" class="form-select rounded-2" required="">
                <option value="">Select Doctor</option>
                <option value="Doctor 1">Doctor 1</option>
                <option value="Doctor 2">Doctor 2</option>
                <option value="Doctor 3">Doctor 3</option>
              </select>-->
            </div>
          </div>

          <div class="form-group mt-3">
            <textarea class="form-control rounded-3 " name="message1" rows="5" placeholder="Message (Optional)"></textarea>
          </div>
          <!--<div class="mb-3">
            <div class="loading">Loading</div>
            <div class="error-message"></div>
            <div class="sent-message">Votre demande de rendez-vous a été envoyé avec succès. Merci!</div>
          </div>-->
          <div class="text-center"><button type="submit" class="btn btn-primary mt-4 rounded-4">Prendre rendez-vous</button></div>
        </form>

      </div>

    </section><!-- /rendez-vous Section -->


    <!-- Section des Départements -->
    <section id="departments" class="departments section">

      <!-- Titre de la section -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Nos Domaines d'Expertise</h2>
        <p>Découvrez nos différents domaines de spécialisation en communication visuelle.</p>
      </div><!-- Fin du titre de la section -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row">
          <div class="col-lg-3">
            <ul class="nav nav-tabs flex-column">
              <li class="nav-item">
                <a class="nav-link active show" data-bs-toggle="tab" href="#departments-tab-1">Conception Graphique</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#departments-tab-2">Stratégie de Marque</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#departments-tab-3">Publicité et Promotion</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#departments-tab-4">Design d'Interface Utilisateur</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#departments-tab-5">Marketing Digital</a>
              </li>
            </ul>
          </div>
          <div class="col-lg-9 mt-4 mt-lg-0">
            <div class="tab-content">
              <div class="tab-pane active show" id="departments-tab-1">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3>Conception Graphique</h3>
                    <p class="fst-italic">Découvrez nos créations uniques et percutantes qui captent l'essence de votre message.</p>
                    <p>Nous combinons créativité et expertise pour donner vie à votre identité visuelle à travers des designs innovants et esthétiques.</p>
                  </div>
                  <div class="col-lg-4 text-center order-1 order-lg-2">
                    <img src="frontend/assets/img/conception first.jpg" alt="" class="img-fluid">
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="departments-tab-2">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3>Stratégie de Marque</h3>
                    <p class="fst-italic">Développez une stratégie de marque distinctive et mémorable pour atteindre vos objectifs commerciaux.</p>
                    <p>Nous vous aidons à définir et à renforcer votre positionnement sur le marché à travers une identité de marque cohérente et stratégique.</p>
                  </div>
                  <div class="col-lg-4 text-center order-1 order-lg-2">
                    <img src="frontend/assets/img/mardig.jpg" alt="" class="img-fluid">
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="departments-tab-3">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3>Publicité et Promotion</h3>
                    <p class="fst-italic">Augmentez la visibilité de votre marque et attirez votre public cible grâce à nos stratégies publicitaires efficaces.</p>
                    <p>Nous élaborons des campagnes publicitaires percutantes qui maximisent votre retour sur investissement et renforcent votre présence sur tous les canaux.</p>
                  </div>
                  <div class="col-lg-4 text-center order-1 order-lg-2">
                    <img src="frontend/assets/img/publicite.jpg" alt="" class="img-fluid">
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="departments-tab-4">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3>Design d'Interface Utilisateur</h3>
                    <p class="fst-italic">Optimisez l'expérience utilisateur à travers des interfaces intuitives et ergonomiques.</p>
                    <p>Nous concevons des interfaces utilisateur attractives et fonctionnelles qui facilitent l'interaction avec vos produits et services.</p>
                  </div>
                  <div class="col-lg-4 text-center order-1 order-lg-2">
                    <img src="frontend/assets/img/conception2.jpg" alt="" class="img-fluid">
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="departments-tab-5">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3>Marketing Digital</h3>
                    <p class="fst-italic">Explorez de nouvelles opportunités et engagez votre audience à travers des stratégies de marketing digital innovantes.</p>
                    <p>Nous vous accompagnons dans la mise en œuvre de campagnes digitales efficaces pour augmenter votre visibilité et générer des leads qualifiés.</p>
                  </div>
                  <div class="col-lg-4 text-center order-1 order-lg-2">
                    <img src="frontend/assets/img/marketingdigital.jpg" alt="" class="img-fluid">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </section><!-- /Section des Départements -->


    <!-- equipe Section -->
    <section id="doctors" class="doctors section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Notre Equipe</h2>
        <p>Découvrez les membres clés de notre équipe de talent.</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <div class="team-member d-flex align-items-start">
                    <!--<div class="pic"><img src="frontend/assets/img/doctors/doctors-1.jpg" class="img-fluid" alt="600*400"></div>-->
              <div class="member-info">
                <h4>Ascel DJIBODE</h4>
                <span> Développeur Web et Mobile</span>
                <p>Passionnée par la conception visuelle et la narration à travers le design.</p>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""> <i class="bi bi-linkedin"></i> </a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <div class="team-member d-flex align-items-start">
              <!--<div class="pic"><img src="frontend/assets/img/doctors/doctors-2.jpg" class="img-fluid" alt=""></div>-->
              <div class="member-info">
                <h4>M. Bachir</h4>
                <span>Concepteur principal</span>
                <p>Expert en création de designs innovants qui captivent et inspirent.</p>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""> <i class="bi bi-linkedin"></i> </a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
            <div class="team-member d-flex align-items-start">
              <!--<div class="pic"><img src="frontend/assets/img/doctors/doctors-3.jpg" class="img-fluid" alt=""></div>-->
              <div class="member-info">
                <h4>Esai KOTCHONI</h4>
                <span>Consultant en stratégie</span>
                <p>Spécialiste en stratégies de communication visuelle adaptées aux besoins spécifiques de chaque client.</p>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""> <i class="bi bi-linkedin"></i> </a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
            <div class="team-member d-flex align-items-start">
              <!--<div class="pic"><img src="frontend/assets/img/doctors/doctors-4.jpg" class="img-fluid" alt=""></div>-->
              <div class="member-info">
                <h4>Ignace DOSSA</h4>
                <span>Directeur de projet </span>
                <p>Expérience éprouvée dans la gestion de projets de communication visuelle complexes.</p>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""> <i class="bi bi-linkedin"></i> </a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->

        </div>

      </div>

    </section><!-- /Equipe Section -->


        <!-- Faq Section -->
    <section id="faq" class="faq section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Questions Fréquentes</h2>
        <p>Trouvez des réponses à vos questions les plus courantes sur nos services de communication et de design.</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row justify-content-center">

          <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">

            <div class="faq-container">

              <div class="faq-item faq-active">
                <h3>Quels types de projets de design graphique réalisez-vous ?</h3>
                <div class="faq-content">
                  <p>Nous réalisons une variété de projets de design graphique, y compris des affiches, des flyers, des logos, des identités visuelles, et bien plus encore. Nous nous adaptons à vos besoins spécifiques pour créer des designs uniques et percutants.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>Comment se déroule le processus de création d'une affiche ?</h3>
                <div class="faq-content">
                  <p>Le processus commence par une discussion approfondie pour comprendre vos besoins et objectifs. Ensuite, nous élaborons un concept de design, que nous ajustons en fonction de vos retours. Une fois approuvé, nous finalisons le design et vous livrons le produit fini.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>Proposez-vous des services d'impression pour les livres et brochures ?</h3>
                <div class="faq-content">
                  <p>Oui, nous offrons des services complets allant de la conception à l'impression de livres et brochures. Nous travaillons avec des imprimeurs de qualité pour garantir des résultats professionnels et durables.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>Pouvez-vous créer une identité visuelle complète pour mon entreprise ?</h3>
                <div class="faq-content">
                  <p>Absolument. Nous développons des identités visuelles complètes, y compris des logos, des palettes de couleurs, des typographies et des guides de style pour assurer une cohérence visuelle sur tous vos supports de communication.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>Quels sont les délais pour la réalisation de projets ?</h3>
                <div class="faq-content">
                  <p>Les délais varient en fonction de la complexité et de l'ampleur du projet. En général, nous fournissons une estimation du délai après avoir discuté de vos besoins et établi un calendrier de projet.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>Comment puis-je demander un devis pour un projet ?</h3>
                <div class="faq-content">
                  <p>Pour demander un devis, vous pouvez nous contacter via notre formulaire de contact, par téléphone ou par email. Fournissez-nous autant de détails que possible sur votre projet, et nous vous enverrons un devis personnalisé dans les plus brefs délais.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

            </div>

          </div><!-- End Faq Column-->

        </div>

      </div>

    </section><!-- /Faq Section -->

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section">

      <div class="container">

        <div class="row align-items-center">

          <div class="col-lg-5 info" data-aos="fade-up" data-aos-delay="100">
            <h3>Témoignages</h3>
            <p>
              Découvrez ce que nos clients disent de nous :
            </p>
          </div>

          <div class="col-lg-7" data-aos="fade-up" data-aos-delay="200">

            <div class="swiper init-swiper">
              <script type="application/json" class="swiper-config">
                {
                  "loop": true,
                  "speed": 600,
                  "autoplay": {
                    "delay": 5000
                  },
                  "slidesPerView": "auto",
                  "pagination": {
                    "el": ".swiper-pagination",
                    "type": "bullets",
                    "clickable": true
                  }
                }
              </script>
              <div class="swiper-wrapper">

                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <div class="d-flex">
                      <img src="frontend/assets/img/testimonials/testimonials-1.jpg" class="testimonial-img flex-shrink-0" alt="">
                      <div>
                        <h3>Saul Goodman</h3>
                        <h4>PDG &amp; Fondateur</h4>
                        <div class="stars">
                          <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                      </div>
                    </div>
                    <p>
                      <i class="bi bi-quote quote-icon-left"></i>
                      <span>"Une équipe professionnelle et créative qui a su transformer notre vision en réalité."</span>
                      <i class="bi bi-quote quote-icon-right"></i>
                    </p>
                  </div>
                </div><!-- End testimonial item -->

                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <div class="d-flex">
                      <img src="frontend/assets/img/testimonials/testimonials-2.jpg" class="testimonial-img flex-shrink-0" alt="">
                      <div>
                        <h3>Sara Wilsson</h3>
                        <h4>Designer</h4>
                        <div class="stars">
                          <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                      </div>
                    </div>
                    <p>
                      <i class="bi bi-quote quote-icon-left"></i>
                      <span>"Leur travail de conception graphique est exceptionnel et a vraiment aidé notre entreprise à se démarquer."</span>
                      <i class="bi bi-quote quote-icon-right"></i>
                    </p>
                  </div>
                </div><!-- End testimonial item -->

                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <div class="d-flex">
                      <img src="frontend/assets/img/testimonials/testimonials-3.jpg" class="testimonial-img flex-shrink-0" alt="">
                      <div>
                        <h3>Jena Karlis</h3>
                        <h4>Propriétaire de magasin</h4>
                        <div class="stars">
                          <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                      </div>
                    </div>
                    <p>
                      <i class="bi bi-quote quote-icon-left"></i>
                      <span>"Des services de haute qualité et une équipe à l'écoute de nos besoins."</span>
                      <i class="bi bi-quote quote-icon-right"></i>
                    </p>
                  </div>
                </div><!-- End testimonial item -->



              </div>
              <div class="swiper-pagination"></div>
            </div>

          </div>

        </div>

      </div>

    </section><!-- /Testimonials Section -->

    <!-- Gallery Section -->
    <section id="gallery" class="gallery section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Galerie</h2>
        <p>Découvrez quelques-unes de nos réalisations :</p>
      </div><!-- End Section Title -->

      <div class="container-fluid" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-0">

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="frontend/assets/img/galler1.jpg" class="glightbox" data-gallery="images-gallery">
                <img src="frontend/assets/img/gallery/gal1.jpg" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="frontend/assets/img/gallery/gal2.jpg" class="glightbox" data-gallery="images-gallery">
                <img src="frontend/assets/img/gallery/gal2.jpg" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="frontend/assets/img/gallery/gal3.jpg" class="glightbox" data-gallery="images-gallery">
                <img src="frontend/assets/img/gallery/gal3.jpg" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="frontend/assets/img/gallery/gal4.jpg" class="glightbox" data-gallery="images-gallery">
                <img src="frontend/assets/img/gallery/gal4.jpg" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="frontend/assets/img/gallery/gal5.jpg" class="glightbox" data-gallery="images-gallery">
                <img src="frontend/assets/img/gallery/gal5.jpg" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="frontend/assets/img/gallery/gal6.jpg" class="glightbox" data-gallery="images-gallery">
                <img src="frontend/assets/img/gallery/gal6.jpg" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="frontend/assets/img/gallery/gal7.jpg" class="glightbox" data-gallery="images-gallery">
                <img src="frontend/assets/img/gallery/gal7.jpg" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="frontend/assets/img/gallery/gal8.jpg" class="glightbox" data-gallery="images-gallery">
                <img src="frontend/assets/img/gallery/gal8.jpg" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

        </div>

      </div>

    </section><!-- /Gallery Section -->

    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Contactez-nous</h2>
        <p>Pour plus d'informations ou pour commencer un projet, n'hésitez pas à nous contacter :</p>
      </div><!-- End Section Title -->


    </section><!-- /Contact Section -->

    <!-- Maping Section -->
    <section class="map " data-aos="fade-up" data-aos-delay="100">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div id="map">
              <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d8097770.46965312!2d-1.9501826100585795!3d7.684275705846736!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sfr!2sbj!4v1719242699097!5m2!1sfr!2sbj" width="100%" height="450px" frameborder="0" style="border:0; border-radius: 5px; position: relative; z-index: 2;" allowfullscreen=""></iframe>
            </div>
          </div>
          <div class="col-lg-10 offset-lg-1">
            <div class="row">

              <a href="mailto:djibodeprinceascel@gmail.com" class="col-lg-4" data-aos="fade-right" data-aos-delay="100">
                <div >
                  <div class="info-item">
                    <i class="fa fa-envelope"></i>
                    <h4>Email</h4>
                    <span>djibodeprinceascel@gmail.com</span>
                  </div>
                </div>
              </a>
              <a href="tel:+22963012539" class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div >
                  <div class="info-item">
                    <i class="fa fa-phone"></i>
                    <h4>Téléphone</h4>
                    <span>+229 63012539</span>
                  </div>
                </div>
              </a>
              <a href="#" class="col-lg-4" data-aos="fade-left" data-aos-delay="100">
                <div >
                  <div class="info-item">
                    <i class="fa fa-map-marked-alt"></i>
                    <h4>Addresse</h4>
                    <span>Cotonou Sikècodji,535022</span>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Maping form section Section -->
    <section class="contact-us-form">
      <div class="container">
        <div class="row">
          <div class="col-lg-6 offset-lg-3" data-aos="fade-down" data-aos-delay="150">
            <div class="section-heading">

              <h4>Soyez libre de nous laisser un message</h4><br>
            </div>
          </div>
          <div class="col-lg-10 offset-lg-1" data-aos="fade-up" data-aos-delay="200">
            <form id="contact1" action="{{route('sendmail')}}" method="POST">
                @csrf
              <div class="row">
                <div class="col-lg-6">
                  <fieldset>
                    <div class="col-md-12 form-group mt-3 ">
                    <input type="name" class="form-control @error('name') is-invalid @enderror" name="name" id="name" placeholder="Votre Nom" autocomplete="on" maxlength="60" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                  </fieldset>
                </div>
                <div class="col-lg-6">
                  <fieldset>
                    <div class="col-md-12 form-group mt-3 ">
                    <input type="phone" class=" form-control former @error('phone') is-invalid @enderror" name="phone" id="phone" placeholder="Votre Téléphone" autocomplete="on" required maxlength="60">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                </fieldset>
                </div>
                <div class="col-lg-6">
                  <fieldset>
                    <div class="col-md-12 form-group mt-3 ">
                    <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" id="email" pattern="[^ @]*@[^ @]*" placeholder="Votre E-mail..." required="" maxlength="100">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                </fieldset>
                </div>
                <div class="col-lg-6">
                  <fieldset>
                    <div class="col-md-12 form-group mt-3 ">
                    <input type="subject" class="form-control @error('subject') is-invalid @enderror" name="subject" id="subject" placeholder="Sujet" autocomplete="on" maxlength="110" required>
                    @error('subject')
                    <div class="invalid-feedback center">   {{' '. $message }}    </div>
                    @enderror
                    </div>
                </fieldset>
                </div>
                <div class="col-lg-12 form-group mt-3">
                  <fieldset>
                    <textarea name="message" id="message" placeholder="Message(optionnel)"></textarea>
                  </fieldset>
                </div>
                <div class="col-lg-12 form-group mt-3">
                  <fieldset>
                    <button type="submit" id="form-submit" class="orange-button">Envoyer</button>
                  </fieldset>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

    </section>


  </main>

  <footer id="footer" class="footer">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.html" class="logo d-flex align-items-center">
            <span class="sitename">Astuscite</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Bénin</p>
            <p>Cotonou, Sikècodji NY 022</p>
            <p class="mt-3"><strong>Téléphone:</strong> <span>+229 63 01 25 39</span></p>
            <p><strong>Email:</strong> <span>djibodeprinceascel@gmail.com</span></p>
          </div>

        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Liens utiles</h4>
          <ul>
            <li><a href="#hero">Accueil</a></li>
            <li><a href="#about">A propos de nous </a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="">onditions d'utilisations</a></li>
            <li><a href="">Police de confidentialité</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-3 footer-links">
          <h4>Nos Services</h4>
          <ul>
            <li><p >Conception d'affiches</p></li>
            <li><p >Design graphique</p></li>
            <li><p >Création de livres et menus resto</p></li>
            <li><p >Supports Marketing</p></li>
            <li><p >Illustrations personnalisées</p></li>
            <li><p >Identité visuelle</p></li>
          </ul>
        </div>

      </div>
      <div class="social-links d-flex mt-4" id="sociallinks2">
        <a href=""><i class="bi bi-twitter-x"></i></a>
        <a href=""><i class="bi bi-facebook"></i></a>
        <a href=""><i class="bi bi-instagram"></i></a>
        <a href=""><i class="bi bi-linkedin"></i></a>
      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Astuscite</strong> <span>Tous droits réservés</span></p>
      <div class="credits">
        <!-- Footer links-->

    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();

            setTimeout(function() {
                successModal.hide();
            }, 3000); // Ferme le modal après 2 secondes
            });
        </script>
    @endif
    <!-- Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Succès</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                {{session('success')}}
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

  <!-- Vendor JS Files -->
  <script src="frontend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="frontend/assets/vendor/php-email-form/validate.js"></script>
  <script src="frontend/assets/vendor/aos/aos.js"></script>
  <script src="frontend/assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="frontend/assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="frontend/assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS frontend/File -->
  <script src="frontend/assets/js/main.js"></script>
  <script>
    // Fonction pour définir le focus sur le premier champ en erreur
    document.addEventListener('DOMContentLoaded', function() {

    const firstInvalidField = document.querySelector('.is-invalid');
        if (firstInvalidField) {
            firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalidField.focus();

        }

    });

  </script>
  <script>
    const preloader = document.querySelector('#preloader');
    if (preloader) {
      window.addEventListener('load', () => {
        preloader.style.display = 'none' ;
      });
    };


  </script>

</body>

</html>
