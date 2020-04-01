<?php
session_start([
    'cookie_lifetime' => 0,
    'use_cookies' => 'On',
    'use_only_cookies' => 'On',
    'use_strict_mode' => 'On',
    'cookie_httponly' => 'On',
    'cache_limiter' => 'nocache'
]);

// charge l'autoloader fourni par composer
require_once(__DIR__.'/vendor/autoload.php');

// charge la classe Customer
require_once('Customer.php');

// charge la configuration de la BDD depuis le fichier .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// déclare les actions autorisées à passer en GET
$actions = ['page', 'buy', 'login', 'logout', 'register'];

// déclare les pages autorisées à passer en GET
$pages = ['home', 'gallery', 'about'];

// détermine l'action demandée par l'utilisateur, page par défaut
$action = $_GET['action'] ?? 'page';

// détermine la page à afficher à l'utilisateur, home par défaut
$page = $_GET['page'] ?? 'home';

// envoit le code d'erreur HTTP 403 et stoppe le script
function httpForbidden() {
    header('HTTP/1.1 403 Forbidden');
    echo '<h1>403 - Interdit</h1>';
    exit('Le serveur a compris la requête, mais refuse de l\'exécuter.');
}

// vérifie que l'URL est bien formée
if (!in_array($action, $actions) || !in_array($page, $pages)){
    httpForbidden();
}

// si on reçois des données provenant d'un formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    // les seules actions tolérées en POST sont "login" et "register"
    if (!in_array($action, ['login', 'register'])){
        httpForbidden();
    }

    try {
        // crée une instance de la classe Customer
        $customer = new Customer();

        // récupère les paramètres passés par le formulaire
        $email    = $_POST['email'];
        $password = $_POST['password'];
        
        // vérifie que les paramètres POST ont été définis correctement
        if (empty($email) || empty($password)){
            // sinon lève une exception avec un message d'erreur
            throw new Exception('L\'adresse email et le mot de passe doivent être renseignés.');
        }
        // si il s'agit d'une demande d'inscription d'un consommateur
        if ($action === 'register'){
            $password2 = $_POST['password2'];
            if ($password !== $password2){
                throw new Exception('La vérification du mot de passe a échouée.');
            }
            if ($customer->exists($email)){
                throw new Exception('Un utilisateur utilisant cette adresse email est déjà enregitré.');
            }
            $customer->register($email, $password);
        }
        // on assume une demande d'ouverture de session (utile pour auto-login après inscription)
        $logged = $customer->login($email, $password);
        if (!$logged){ throw new Exception('La connexion a échouée.'); }
            
    } catch (Exception $e){
        // TODO afficher un message d'erreur
    }

}
// traitement des requêtes GET
else {
    // demande de fermeture de session
    if ($action === 'logout'){
        // crée une instance de la classe Customer
        $customer = new Customer();
        // utilise la méthode logout pour clore la session
        $customer->logout();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap.min.css">

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="index.php">A World of Faces</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">Accueil <span class="sr-only">(actuel)</span></a>
            </li>
<?php
if (isset($_SESSION['logged'])){
    echo '<li class="nav-item"><a class="nav-link" href="#">Commandes</a></li>';
}
?>
            <li class="nav-item">
                <a class="nav-link" href="views/aboutme.html">A propos de moi</a>
            </li>
            </ul>
            <form class="form-inline my-2 my-lg-0">        
<?php
if (isset($_SESSION['logged'])){
    echo '<a href="?action=logout&page='.$page.'" class="btn btn-primary btn-lg active">Déconnexion</a>';
} else {
    echo '<a href="#" class="btn btn-primary btn-lg active" data-toggle="modal" data-target="#modalLRForm">Connexion</a>';
}
?>
            </form>
        </div>
</nav>
<!--/ Navbar -->

<!--Modal: Login / Register Form-->
<div class="modal fade" id="modalLRForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog cascading-modal" role="document">
      <!--Content-->
      <div class="modal-content">
  
        <!--Modal cascading tabs-->
        <div class="modal-c-tabs">
  
          <!-- Nav tabs -->
          <ul class="nav nav-tabs md-tabs tabs-2 light-blue darken-3" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" data-toggle="tab" href="#panel7" role="tab"><i class="fas fa-user mr-1"></i>
                Se connecter</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#panel8" role="tab"><i class="fas fa-user-plus mr-1"></i>
                Créer un compte</a>
            </li>
          </ul>
  
          <!-- Tab panels -->
          <div class="tab-content">
            <!--Panel 7-->
            <div class="tab-pane fade in show active" id="panel7" role="tabpanel">
  
              <!--Body-->
              <div class="modal-body mb-1">
                <form method="POST" action="?action=login&page=<?= $page ?>">
                    <div class="md-form form-sm mb-2">
                        <i class="fas fa-envelope prefix"></i>
                        <input type="email" name="email" id="modalLRInput10" class="form-control form-control-sm validate">
                        <label data-error="wrong" data-success="right" for="modalLRInput10">Email</label>
                    </div>
    
                    <div class="md-form form-sm mb-2">
                        <i class="fas fa-lock prefix"></i>
                        <input type="password" name="password" id="modalLRInput11" class="form-control form-control-sm validate">
                        <label data-error="wrong" data-success="right" for="modalLRInput11">Mot de passe</label>
                    </div>
                    <div class="text-center mt-2">
                        <button class="btn btn-info">Connexion<i class="fas fa-sign-in ml-1"></i></button>
                    </div>
                </form>
              </div>
              <!--Footer-->
              <div class="modal-footer">
                <div class="options text-center text-md-right mt-1">
                  <p>Pas membre? <a href="#" class="blue-text">Créer un compte</a></p>
                  <p><a href="#" class="blue-text">Mot de passe oublié?</a></p>
                </div>
                <button type="button" class="btn btn-outline-info waves-effect ml-auto" data-dismiss="modal">Fermer</button>
              </div>
  
            </div>
            <!--/.Panel 7-->
  
            <!--Panel 8-->
            <div class="tab-pane fade" id="panel8" role="tabpanel">
  
              <!--Body-->
              
                <div class="modal-body">
                    <form method="POST" action="?action=register&page=<?= $page ?>">
                        <div class="md-form form-sm mb-2">
                            <i class="fas fa-envelope prefix"></i>
                            <input type="email" name="email" id="modalLRInput12" class="form-control form-control-sm validate">
                            <label data-error="wrong" data-success="right" for="modalLRInput12">Email</label>
                        </div>
                        <div class="md-form form-sm mb-2">
                            <i class="fas fa-lock prefix"></i>
                            <input type="password" name="password" id="modalLRInput13" class="form-control form-control-sm validate">
                            <label data-error="wrong" data-success="right" for="modalLRInput13">Mot de passe</label>
                        </div>
                        <div class="md-form form-sm mb-2">
                            <i class="fas fa-lock prefix"></i>
                            <input type="password" name="password2" id="modalLRInput14" class="form-control form-control-sm validate">
                            <label data-error="wrong" data-success="right" for="modalLRInput14">Retaper le mot de passe</label>
                        </div>
                        <div class="text-center form-sm mt-2">
                            <button class="btn btn-info">Inscription<i class="fas fa-sign-in ml-1"></i></button>
                        </div>
                    </form>
                </div>
                <!--Footer-->
                <div class="modal-footer">
                    <div class="options text-right">
                      <p class="pt-1">Déjà un compte? <a href="?action=login&page=home#panel8" class="blue-text">Se connecter</a></p>
                    </div>
                    <button type="button" class="btn btn-outline-info waves-effect ml-auto" data-dismiss="modal">Fermer</button>
                </div>
            </div>
            <!--/.Panel 8-->
          </div>
  
        </div>
      </div>
      <!--/.Content-->
    </div>
</div>
<!--Modal: Login / Register Form-->

<!-- Container -->
<div class="container my-4">

    <h2 class="my-5 h2">Jean-Michel Hinicker<br><small class="lead">A World of Faces</small></h2> 

    <!-- Carousel Wrapper -->
    <div id="carousel-example-2" class="carousel slide carousel-fade z-depth-1-half" data-ride="carousel">

        <!--Indicators-->
        <ol class="carousel-indicators">
            <li data-target="#carousel-example-2" data-slide-to="0" class="active"></li>
            <li data-target="#carousel-example-2" data-slide-to="1"></li>
            <li data-target="#carousel-example-2" data-slide-to="2"></li>
        </ol>
        <!--/Indicators-->

        <!--Slides-->
        <div class="carousel-inner" role="listbox">
            <div class="carousel-item active">
                <div class="view">
                    <img class="d-block w-100" src="photos/public-maroc.jpg" alt="Visage d'une jeune fille originaire du Maroc">
                    <div class="mask rgba-black-light"></div>
                </div>
                <div class="carousel-caption">
                    <h3 class="h3-responsive">Australie</h3>
                    <p>First text</p>
                </div>
            </div>
            <div class="carousel-item">
                <!--Mask color-->
                <div class="view">
                    <img class="d-block w-100" src="photos/public-germany.jpg" alt="Visage d'un vieil homme barbu originaire d'Allemagne">
                    <div class="mask rgba-black-slight"></div>
                </div>
                <div class="carousel-caption">
                    <h3 class="h3-responsive">Allemagne</h3>
                    <p>Third text</p>
                </div>
            </div>
            <div class="carousel-item">
                <!--Mask color-->
                <div class="view">
                    <img class="d-block w-100" src="photos/public-italy.jpg" alt="Visage d'une jeune fille sous la neige du Canada">
                    <div class="mask rgba-black-strong"></div>
                </div>
                <div class="carousel-caption">
                    <h3 class="h3-responsive">Italie</h3>
                    <p>Secondary text</p>
                </div>
            </div>

        </div>
        <!--/Slides-->

        <!--Controls-->
        <a class="carousel-control-prev" href="#carousel-example-2" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carousel-example-2" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>

        <!--/.Controls-->
        </div>

    <!-- /Carousel Wrapper -->

</div>
<!--/Container-->

<!-- Footer -->
<footer class="page-footer font-small blue">

    <!-- Copyright -->
    <div class="footer-copyright text-center py-3">© 2020 Copyright:
        <a href="#">MDBootstrap.com</a>
    </div>
    <!-- Copyright -->

</footer>
<!-- Footer -->

</html>