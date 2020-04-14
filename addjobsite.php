<?php 
/*
   Adds an jobsite to the database
   Author: Jeremy Grift
   Created: April 14, 2020
*/

    // If session is not started, start.
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $title = 'Add Jobsite';

    // The error variables
    $errormessage = '';
    $successmessage = '';

    $nameerror = '';
    $descriptionerror = '';
    $addresserror = '';



    // validate inputs on POST
    if (isset($_POST['submit'])) {
        $valid = true;

        // NAME

        if (empty($_POST['name'])) {
            $valid = false;
            $nameerror = '*required';
        }
        elseif (filter_input(INPUT_POST, 'name', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9]+(([',. -_][a-zA-Z ])?[a-zA-Z0-9]*)*$/"))) === false){
            $valid = false;
            $nameerror = "*Only letters, numbers and ',.-_ allowed";
        }
        elseif (strlen($_POST['name']) > 30) {
            $valid = false;
            $nameerror = "*Must be 30 characters or less";
        }

        // DESCRIPTION

        if (!empty($_POST['description']) && filter_input(INPUT_POST, 'description', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9]+(([',. -_][a-zA-Z ])?[',. -_a-zA-Z0-9]*)*$/"))) === false) {
            $valid = false;
            $descriptionerror = "*Only letters, numbers and ',.-_ allowed";
        }

        // INSERT

        if ($valid) {
            if (!isset($db)) {
                require('connect.php');
            }

            $insert = "INSERT INTO Jobsites (Name, Description) VALUES (:name, :description)";

            $insert = $db->prepare($insert);
            $insert->bindValue(':name', filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $insert->bindValue(':description', filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

            if ($insert->execute()) {
                $successmessage = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) . ' successfully created.';
            }
            else {
                $errormessage = 'An error occurred while adding Jobsite.';
            }
        }

    }

?>
 
   <?php include('header.php') ?>
   <div class="container">
        <form method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
            <div class="row">
                <div id="left_col" class="col-md-6">   
                    <div class="form-group row"> 
                        <label for="name" class="col-lg-3 col-form-label">Name:</label>
                        <div class="col-lg-9">
                            <input id="name" name="name" type="text" class="form-control <?php if($nameerror){echo 'is-invalid';} ?>" required>
                            <div class="invalid-feedback">
                                <?= $nameerror ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="right_col" class="form-group col-md-6">   
                    <div class="form-group row"> 
                        <label for="description" class="col-lg-3 col-form-label">Description:</label>
                        <div class="col-lg-9">
                            <input id="description" name="description" type="text" class="form-control <?php if($descriptionerror){echo 'is-invalid';} ?>">
                            <div class="invalid-feedback">
                                <?= $descriptionerror ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-6">
                    <div class="form-group row">
                        <div class="col-lg-3"></div>
                        <div class="col-lg-9">
                            <input class="btn btn-primary w-100 mb-2" type="submit" name="submit" value="Add Jobsite">

                            <?php if($errormessage): ?>
                                <p class="text-danger"><?= $errormessage ?></p>
                            <?php endif ?>

                            <?php if($successmessage): ?>
                                <p class="text-success"><?= $successmessage ?></p>
                            <?php endif ?>
                        </div>
                    </div>
                    
                </div>
                
            </div>
        </form>


    </div>
</body>
</html>