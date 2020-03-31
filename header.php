<!-- 
    The main header of the site. To be included in most pages.
    Author: Jeremy Grift
    Created: March 5, 2020
    Last Updated: March 5, 2020
 -->
 <?php 

    // If session is not started, start.
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $title;
    // if no title is provided then set default
    if (! isset($title)) {
        $title = 'Employee Management Site';
    }

    $heading;
    // if no heading is provided then set default
    if (! isset($heading)) {
        $heading = "Done Right Contracting's Employee Management Site";
    }

    $name = '';
    if (isset($_SESSION['firstname'])){
        $name = $_SESSION['firstname'];
    }


    // if no heading is provided then will be blank
    $secondaryheading;
    if (! isset($secondaryheading)) {
        $secondaryheading = '';
    }
    //print_r($_SESSION['header']['navlinks']);
    // if no navlinks in session (not logged in) then set them to default
    if (empty($_SESSION['header']['navlinks']) ) {
        $_SESSION['header']['navlinks'] = [
            [
                'href' => 'index.php',
                'text' => 'Index'
            ],
            [
                'href' => 'home.php',
                'text' => 'Home'
            ],
            [
                'href' => 'createuser.php',
                'text' => 'Create User'
            ]
        ];
    }

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title><?= $title ?></title>
    <script src="https://cdn.tiny.cloud/1/i0h9nf54rspbajejo31p2w0x620asg4oix99xzypcq1r2ibk/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>tinymce.init({selector:'#comments'});</script>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</head>
<body class="bg-light">
    <div class="jumbotron jumbotron-fluid pt-1 pb-1 pl-5 pr-5 bg-dark text-light">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="collapse navbar-collapse d-flex d-flex justify-content-end">
                <?php if(isset($_SESSION['logged']) && $_SESSION['logged']): ?>
                    <span class="nav-link" id="userlabel">Welcome <?= $name ?></span>
                    <a href="logout.php" class="nav-link" id="navloglink">logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link" id="navloglink">login</a>
                <?php endif ?>           
            </div>
        </nav>
        <h1 class="display-5"><?= $heading ?></h1>
        <p class="lead"><?=$secondaryheading ?></p>
        <nav class="nav justify-content-center">
            <?php foreach($_SESSION['header']['navlinks'] as $link): ?>
                <a href="<?= $link['href'] ?>" class="nav-item nav-link text-secondary"><?= $link['text'] ?></a>
            <?php endforeach ?>
        </nav>
    </div>


