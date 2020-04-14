<?php 
/*
   Edits an jobsite
   Author: Jeremy Grift
   Created: April 14, 2020
*/

    // If session is not started, start.
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $authusertype = 'admin';
    require('authenticate.php');

    include('utilities.php');



 

    // The error variables
    $criticalerrormessage = '';
    $errormessage = '';
    $successmessage = '';

    $nameerror = '';
    $descriptionerror = '';
    $activeerror = '';

    if (empty($_GET['jobsiteid']) || filter_input(INPUT_GET, 'jobsiteid', FILTER_VALIDATE_INT) === false) {
        $criticalerrormessage = 'Invalid Jobsite Id.';
        $title = 'Error Editing Jobsite';
    }
    else{
        if (!isset($db)) {
            require('connect.php');
        }
        // Load the jobsite given by get.
        $jobsite = "SELECT JobsiteID, Name, Description, IsActive FROM Jobsites WHERE JobsiteID = :jobsiteid";
        $jobsite = $db->prepare($jobsite);
        $jobsite->bindValue(':jobsiteid', $_GET['jobsiteid']);

        if ($jobsite->execute()) {
            if ($jobsite->rowCount() == 1) {
                // if a jobsite is returned.
                $jobsite = $jobsite->fetch();
                $title = 'edit ' . $jobsite['Name'];
            }
            else {
                $criticalerrormessage = 'Jobsite does not exist.';
            }   
        }
        else {
            $criticalerrormessage = 'Error occurred while loading jobsite.';
        }
    }



    // validate inputs on POST if no errors
    if (isset($_POST['submit']) && empty($errormessage) ) {
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

        // ACTIVE

        if ( !empty($_POST['active'])) {
            $active = true;
        }
        else{
            $active = false;
        }

        // UPDATE

        if ($valid) {
            if (!isset($db)) {
                require('connect.php');
            }

            $insert = "UPDATE Jobsites SET Name = :name, Description = :description, IsActive = :active WHERE JobsiteID = :jobsiteid";

            $insert = $db->prepare($insert);
            $insert->bindValue(':name', filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $insert->bindValue(':description', filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $insert->bindValue(':active', $active);
            $insert->bindValue(':jobsiteid', $jobsite['JobsiteID']);

            if ($insert->execute()) {
                $successmessage = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) . ' successfully updated.';

                // update the jobsites to display the updated values.
                $jobsite = "SELECT JobsiteID, Name, Description, IsActive FROM Jobsites WHERE JobsiteID = :jobsiteid";
                $jobsite = $db->prepare($jobsite);
                $jobsite->bindValue(':jobsiteid', $_GET['jobsiteid']);
                $jobsite->execute();
                $jobsite = $jobsite->fetch();
            }
            else {
                $errormessage = 'An error occurred while editingJobsite.';
            }
        }

    }

?>

   <?php include('header.php') ?>
   <div class="container">
        <?php if($criticalerrormessage): ?>
            <div class="container alert alert-danger" role="alert">
                <h4 class="alert-heading">Error</h4>
                <p><?= $criticalerrormessage ?></p>
                <hr>
                <a href="viewjobsites.php" class="btn btn-primary mb-2">Return to view jobsites</a>
            </div>
        <?php else: ?>
            <form method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                <div class="row">
                    <div id="left_col" class="col-md-6">   
                        <!-- Name -->
                        <div class="form-group row"> 
                            <label for="name" class="col-lg-3 col-form-label">Name:</label>
                            <div class="col-lg-9">
                                <input id="name" name="name" type="text" class="form-control <?php if($nameerror){echo 'is-invalid';} ?>" value="<?= $jobsite['Name'] ?>" required>
                                <div class="invalid-feedback">
                                    <?= $nameerror ?>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group row"> 
                            <label for="description" class="col-lg-3 col-form-label">Description:</label>
                            <div class="col-lg-9">
                                <input id="description" name="description" type="text" class="form-control <?php if($descriptionerror){echo 'is-invalid';} ?>" value="<?= (bool)$jobsite['Description'] ?>">
                                <div class="invalid-feedback">
                                    <?= $descriptionerror ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="right_col" class="form-group col-md-6">   
                        <!-- Active -->
                        <div class="form-group row">
                            <label for="active" class="col-lg-3 col-form-label">Active:</label>
                            <div class="col-lg-9">
                                <input id="active" name="active" type="checkbox" class="h-100 <?php if($activeerror){echo 'is-invalid';} ?>" <?php if($jobsite['IsActive']){echo 'checked';} ?>>
                                <div class="invalid-feedback">
                                    <?= $activeerror ?>
                                </div>
                            </div>                       
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <div class="form-group row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-9">
                                <input class="btn btn-primary w-100 mb-2" type="submit" name="submit" value="Update Jobsite">

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
        <?php endif ?>
    </div>
</body>
</html>