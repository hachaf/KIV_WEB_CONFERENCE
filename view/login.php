<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Crypto-currency conference</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="https://fonts.googleapis.com/css?family=Saira+Extra+Condensed:100,200,300,400,500,600,700,800,900" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="vendor/devicons/css/devicons.min.css" rel="stylesheet">
    <link href="vendor/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="content/css/resume.min.css" rel="stylesheet">

</head>

<body id="page-top">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top" id="sideNav">
    <a class="navbar-brand js-scroll-trigger" href="#page-top">
        <span class="d-block d-lg-none">Start Bootstrap</span>
        <span class="d-none d-lg-block">
          <img class="img-fluid img-profile mx-auto mb-2" src="content/img/profile.jpg" alt="">
        </span>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link js-scroll-trigger" href="#about">About</a>
            </li>
            <li class="nav-item">
                <a class="nav-link js-scroll-trigger" href="#login">Login</a>
            </li>
            <li class="nav-item">
                <a class="nav-link js-scroll-trigger" href="#register">Register</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container-fluid p-0">

    <section class="resume-section p-3 p-lg-5 d-flex d-column" id="about">
        <div class="my-auto">
            <h1 class="mb-0">Crypto-currency
                <span class="text-primary">conference</span>
            </h1>
            <div class="subheading mb-5">Fakulta aplikovaných věd
                Západočeská univerzita v Plzni
                Univerzitní 8, 301 00 Plzeň
                <a href="mailto:name@email.com">hachaf@students.zcu.cz</a>
            </div>
            <p class="mb-5">Moderated conference on the current topic of virtual (crypto) currencies</p>
        </div>
    </section>

    <section class="resume-section p-3 p-lg-5 d-flex flex-column" id="login">
        <div class="my-auto">
            <h2 class="mb-5">Login</h2>
            <div class="resume-item d-flex flex-column flex-md-row mb-5">
                    <form action="test.php" method="post">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username">
                        </div>
                        <div class="form-group">
                            <label for="pwd">Password:</label>
                            <input type="password" class="form-control" id="pwd" name="pwd">
                        </div>
                        <button type="submit" class="btn btn-default">Sign in</button>
                    </form>
            </div>
        </div>
    </section>

    <section class="resume-section p-3 p-lg-5 d-flex flex-column" id="register">
        <div class="my-auto">
            <h2 class="mb-5">Register</h2>
            <div class="resume-item d-flex flex-column flex-md-row mb-5">
                <form action="register.php" method="post">
                    <div class="form-group">
                        <label for="reg-username">Username:</label>
                        <input type="text" class="form-control" id="reg-username" name="reg-username">
                    </div>
                    <div class="form-group">
                        <label for="reg-pwd">Password:</label>
                        <input type="password" class="form-control" id="reg-pwd" name="reg-pwd">
                    </div>
                    <button type="submit" class="btn btn-default">Sign in</button>
                </form>
            </div>
        </div>
    </section>

</div>

<!-- Bootstrap core JavaScript -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Plugin JavaScript -->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for this template -->
<script src="content/js/resume.min.js"></script>

</body>

</html>
