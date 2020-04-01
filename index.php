<!doctype html>
<html lang="en">
<head>
<title>Home</title>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="bootstrap.min.css" crossorigin="anonymous">

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body>

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

<div class="container my-4">

<p class="font-weight-bold">Bootstrap image slider is responsive and interactive slideshow which is created for
presenting content, especially images and videos.</p>

<p><strong>Detailed documentation and more examples about Bootstrap image slider you can find in our <a href="https://mdbootstrap.com/docs/jquery/javascript/carousel/" target="_blank">Bootstrap Carousel Docs</a></strong></p>



<h2 class="my-5 h2">A World of Faces</h2>

<!--Carousel Wrapper-->
<div id="carousel-example-2" class="carousel slide carousel-fade z-depth-1-half" data-ride="carousel">
<!--Indicators-->
<ol class="carousel-indicators">
<li data-target="#carousel-example-2" data-slide-to="0" class="active"></li>
<li data-target="#carousel-example-2" data-slide-to="1"></li>
<li data-target="#carousel-example-2" data-slide-to="2"></li>
</ol>
<!--/.Indicators-->
<!--Slides-->
<div class="carousel-inner" role="listbox">
<div class="carousel-item active">
    <div class="view">
    <img class="d-block w-100" src="https://mdbootstrap.com/img/Photos/Slides/img%20(68).jpg" alt="First slide">
    <div class="mask rgba-black-light"></div>
    </div>
    <div class="carousel-caption">
    <h3 class="h3-responsive">Light mask</h3>
    <p>First text</p>
    </div>
</div>
<div class="carousel-item">
    <!--Mask color-->
    <div class="view">
    <img class="d-block w-100" src="https://mdbootstrap.com/img/Photos/Slides/img%20(6).jpg" alt="Second slide">
    <div class="mask rgba-black-strong"></div>
    </div>
    <div class="carousel-caption">
    <h3 class="h3-responsive">Strong mask</h3>
    <p>Secondary text</p>
    </div>
</div>
<div class="carousel-item">
    <!--Mask color-->
    <div class="view">
    <img class="d-block w-100" src="https://mdbootstrap.com/img/Photos/Slides/img%20(9).jpg" alt="Third slide">
    <div class="mask rgba-black-slight"></div>
    </div>
    <div class="carousel-caption">
    <h3 class="h3-responsive">Slight mask</h3>
    <p>Third text</p>
    </div>
</div>

</div>
<!--/.Slides-->
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
<!--/.Carousel Wrapper-->

</div>

<!-- Footer -->
<footer class="page-footer font-small blue">

<!-- Copyright -->
<div class="footer-copyright text-center py-3">© 2020 Copyright:
<a href="#"> MDBootstrap.com</a>
</div>
<!-- Copyright -->

</footer>
<!-- Footer -->
</html>