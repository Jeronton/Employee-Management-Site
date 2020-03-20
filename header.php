<!-- 
    The main header of the site. To be included in most pages.
    Author: Jeremy Grift
    Created: March 5, 2020
    Last Updated: March 5, 2020
 -->
 <?php 
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

    // $navlinks;
    // // if no navlinks provided then set default
    // if (! isset($navlinks) ) {
        $navlinks = [
            [
                'href' => 'index.php',
                'text' => 'Home'
            ],
            [
                'href' => '#',
                'text' => 'Somewhere'
            ],
            [
                'href' => '#',
                'text' => 'Something'
            ]
        ];
    //}

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title><?= $title ?></title>
</head>
<body class="bg-light">
    <div class="jumbotron jumbotron-fluid pt-1 pb-1 pl-5 pr-5 bg-dark text-light">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="collapse navbar-collapse d-flex d-flex justify-content-end">
                <span class="nav-link" id="userlabel">Welcome <?= $name ?></span>
                <a href="login.php" class="nav-link" id="navloglink">login</a>
                <a href="logout.php" class="nav-link" id="navloglink">logout</a>
            </div>
        </nav>
        <h1 class="display-5">Done Right Contracting's Employee Management Site</h1>
        <p class="lead">some information</p>
        <nav class="nav nav-pills justify-content-center">
            <?php foreach($navlinks as $link): ?>
                <a href="<?= $link['href'] ?>" class="nav-item nav-link text-secondary"><?= $link['text'] ?></a>
            <?php endforeach ?>
        </nav>
    </div>


