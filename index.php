<?php

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

    // les actions "page" et "buy" ne sont pas autorisées avec la méthode POST
    if ($action === 'page' || $action === 'buy'){
        httpForbidden();
    }

    // charge la classe Customer
    require_once('Customer.php');

    try {
        // crée une instance de la classe Customer
        $customer = new Customer();

        // demande de fermeture de session
        if ($action === 'logout'){
            $customer->logout();
        }
        // autres actions nécessitant des paramètres POST (login, register)
        else {
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
                if ($customer->exists($email)){
                    throw new Exception('Un utilisateur utilisant cette adresse email est déjà enregitré.');
                }
                $customer->register($email, $password);
            }
            // on assume une demande d'ouverture de session (utile pour auto-login après inscription)
            $customer->login($email, $password);
        }
    } catch (Exception $e){
        // TODO afficher un message d'erreur
    }

}
// traitement des requêtes GET
else {

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
    <a class="navbar-brand" href="#">A World of Faces</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Order</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">About me</a>
            </li>
            </ul>
            <form class="form-inline my-2 my-lg-0">
            <a href="views/register.html" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Register</a>
            <a href="views/login.html" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Log In</a>
            </form>
        </div>
</nav>
<!--/ Navbar -->

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