<?php
// démarre une session sécurisée
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
$pages = ['home', 'order', 'aboutme'];

// détermine l'action demandée par l'utilisateur, page par défaut
$action = $_GET['action'] ?? 'page';

// détermine la page à afficher à l'utilisateur, home par défaut
$page = $_GET['page'] ?? 'home';

// variables utilisées pour afficher les notifications toast
$alert   = false;
$message = false;

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
            // prépare le message de confirmation
            $alert = 'Confirmation';
            $message = 'Inscription réussie.';
        }
        // on assume une demande d'ouverture de session (utile pour auto-login après inscription)
        $logged = $customer->login($email, $password);
        if (!$logged){ throw new Exception('La connexion a échouée.'); }
        if (!$alert){
            $alert = 'Confirmation';
            $message = 'Connexion réussie.';
        }
            
    } catch (Exception $e){
        // prepare le message d'erreur
        $alert = 'Erreur';
        $message = $e->getMessage();
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
        // prépare le message de confirmation
        $alert = 'Confirmation';
        $message = 'Vous êtes déconnecté.';
    }
    elseif ($action !== 'page'){
        echo 'ICI';
        httpForbidden();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>A World of Faces</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.10.0/baguetteBox.min.css">
    <link rel="stylesheet" href="cards-gallery.css">
    <style type="text/css">
.carousel-inner {
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.15);
    border-radius: 0.25rem;
}
.cards-gallery {
    padding: 0;
}
.toast {
    position: fixed;
    right: 0;
    bottom: 0;
    z-index: 1000;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.15);
}
    </style>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="?page=home">A World of Faces</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav mr-auto">

<?php if ($page === 'home'): ?>
    <li class="nav-item active">
        <a class="nav-link" href="?page=home">Accueil <span class="sr-only">(actuel)</span></a>
    </li>
<?php else: ?>
    <li class="nav-item">
        <a class="nav-link" href="?page=home">Accueil</a>
    </li>
<?php endif; ?>

<?php if (isset($_SESSION['logged'])): ?>
    <?php if ($page === 'order'): ?>
    <li class="nav-item active">
        <a class="nav-link" href="?page=order">Commandes <span class="sr-only">(actuel)</span></a>
    </li>
    <?php else: ?>
    <li class="nav-item">
        <a class="nav-link" href="?page=order">Commandes</a>
    </li>
    <?php endif; ?>
<?php endif; ?>

<?php if ($page === 'aboutme'): ?>
    <li class="nav-item active">
        <a class="nav-link" href="?page=aboutme">A propos de moi <span class="sr-only">(actuel)</span></a>
    </li>
<?php else: ?>
    <li class="nav-item">
        <a class="nav-link" href="?page=aboutme">A propos de moi</a>
    </li>
<?php endif; ?>

            </ul>
            <form class="form-inline my-2 my-lg-0">   

<?php if (isset($_SESSION['logged'])): ?>
    <a href="?action=logout" class="btn btn-primary btn-lg active">Déconnexion</a>
<?php else: ?>
    <a href="#" class="btn btn-primary btn-lg active" data-toggle="modal" data-target="#modalLRForm">Connexion</a>
<?php endif; ?>

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
                  <p>Pas membre? <a href="#" id="tab-register" class="blue-text">Créer un compte</a></p>
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
                      <p class="pt-1">Déjà un compte? <a href="#" id="tab-login" class="blue-text">Se connecter</a></p>
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
<!--/Modal: Login / Register Form-->

<!--Toast-->
<?php if ($alert): ?>

<div class="toast" role="alert" aria-live="assertive" aria-atomic="true"
    data-delay="3000"
>
  <div class="toast-header">
    <!-- <img src="..." class="rounded mr-2" alt="..."> -->
    <strong class="mr-auto"><?= $alert ?></strong>
    <!-- <small>11 mins ago</small> -->
    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Fermer">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="toast-body">
    <?= $message ?>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    // affiche la notification serveur
    $('.toast').toast('show');
});
</script>
<?php endif; ?>
<!--/Toast-->

<script type="text/javascript">
$(document).ready(function(){
    // active le changement d'onglet par les liens en bas de formulaire
    $('#tab-login').click(function (event){
        event.stopPropagation();
        $('a[href="#panel7').tab('show');
    });
    $('#tab-register').click(function (event){
        event.stopPropagation();
        $('a[href="#panel8').tab('show');
    });
});
</script>

<!-- Container -->
<div class="container my-4">

    <h2 class="my-5 h2">Jean-Michel Hinicker<br><small class="lead">A World of Faces</small></h2> 

<?php

switch ($page){
    case 'order':
        include('views/order.html');
        break;
    case 'aboutme':
        include('views/aboutme.html');
        break;
    case 'home':
        include('views/home.html');
        break;
}

?>

</div>
<!--/Container-->

<!-- Footer -->
<footer class="page-footer font-small blue">

    <!-- Copyright -->
    <div class="footer-copyright text-center py-3">
        <a href="#"><img src="icons/facebook.png" alt="Facebook"></a>
        &nbsp;
        <a href="#"><img src="icons/instagram.png" alt="Instagram"></a>
        &nbsp;
        <a href="#"><img src="icons/pinterest.png" alt="Pinterest"></a>
        &nbsp;
        <a href="#"><img src="icons/twitter.png" alt="Twitter"></a>
        <br>
        &copy; 2020 Jean-Michel Hinicker
    </div>
    <!-- Copyright -->

</footer>
<!-- Footer -->

</html>