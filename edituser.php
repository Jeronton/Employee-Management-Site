<!-- 
    Description
    Author: Jeremy Grift
    Created: March 24, 2020
    Last Updated: March 24, 2020
 -->
 <?php 
    
    // If session is not started, start.
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $authusertype = 'admin';
    require('authenticate.php');
    require('connect.php');
    require('utilities.php');
    include 'php-image-resize-master/lib/ImageResize.php' ;
	use \Gumlet\ImageResize;

    $title = 'Edit User';

    // If id is valid, get user to fill the form with its existing data.
    $invalidmessage = '';
    if (empty($_GET['id'])) {
        $invalidmessage = 'No user is specified.';
    }
    elseif (filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) === false) {
        $invalidmessage = 'Non-numeric user id entered.';
    }
    else{
        $user = getUser(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT), $invalidmessage);
        
        if (isset($user)) {
            $title = "Edit {$user['Username']}";
        }    
    }

   // load jobsites
   $jobsites = GetJobsites();


   // ValidClass used to enforce form validation in case javascript fails
   $fnamevalidclass = '';
   $lnamevalidclass = '';
   $usernamevalidclass = '';
   $passwordvalidclass = '';
   $passconfirmvalidclass = '';
   $usertypevalidclass = '';
   $jobsitevalidclass = '';
   $profileimagevalidclass = '';
   $emailvalidclass = '';


   // stores the error to be displayed when an input is invalid.
   $fnameerror = '*Required';
   $lnameerror = '*Required';
   $usernameerror = '*Required';
   $passworderror = '*Required';
   $passconfirmerror = '*Required';
   $usertypeerror = '';
   $jobsiteerror = '';
   $profileimageerror = '';
   $emailerror = '*Required';

   // Displays an errormessage.
   $errormessage = '';

   // Displays success message.
   $successmessage = '';
   

   // If a POST, attempt to create user. 
   if (isset($_POST['update'])) {

       $valid = true;

       // FIRST NAME
       
       // if no first name entered
       if (empty($_POST['firstname'])) {
           $fnamevalidclass = 'is-invalid';
           $valid = false;
       }
       // only allows ,.- and space
       elseif (! filter_input(INPUT_POST, 'firstname', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/")))) {
           $fnameerror = "*Contains forbidden character(s)";
           $fnamevalidclass = 'is-invalid';
           $valid = false;
       }

       // LAST NAME

       // if no last name entered
       if (empty($_POST['lastname'])) {
           $lnamevalidclass = 'is-invalid';
           $valid = false;
       }
       // only allows ,.- and space
       elseif (! filter_input(INPUT_POST, 'lastname', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/")))) {
           $lnameerror = "*Contains forbidden character(s)";
           $lnamevalidclass = 'is-invalid';
           $valid = false;
       }

       // EMAIL

       if (empty($_POST['email'])) {
           $emailvalidclass = 'is-invalid';
           $valid = false;
       }
       elseif (filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) == false) {
           $emailvalidclass = 'is-invalid';
           $emailerror = '*invalid email address';
           $valid = false;
       }

       // USERNAME

       // if no last name entered
       if (empty($_POST['username'])) {
           $usernamevalidclass = 'is-invalid';
           $valid = false;
       }
       // only allows lowercase letters and numbers.
       elseif (! filter_input(INPUT_POST, 'username', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9]*$/")))) {
           $usernameerror = "*Only letters and numbers are permitted";
           $usernamevalidclass = 'is-invalid';
           $valid = false;
       }
       // Username must be unique or unchanged
       elseif ($_POST['username'] != $user['Username'] && !UniqueUsername($_POST['username'])) {
           $usernameerror = '*That username already exists, try a different username';
           $usernamevalidclass = 'is-invalid';
           $valid = false;
       }

       // ACCOUNT TYPE

       // checks that the options are not messed with.
       if (empty($_POST['usertype']) || ($_POST['usertype'] != 'employee' 
       && $_POST['usertype'] != 'accountant' && $_POST['usertype'] != 'admin')) {
           $usertypevalidclass = 'is-invalid';
           $usertypeerror = '*Invalid account type';
           $valid = false;
       }

       // JOBSITE

       if (! JobsiteExists(filter_input(INPUT_POST, 'jobsite', FILTER_VALIDATE_INT))){
           $jobsiteerror = "That jobsite doesn't exist";
           $jobsitevalidclass = 'is-invalid';
           $valid = false;
       }

       // PROFILE IMAGE

       

       // The variable to add to the database, defaults to the existing path
       $profileimagepath = $user['ProfilePicture'];
       // only validate if provided, default to no image.
       if (isset($_FILES['profileimage']) && $_FILES['profileimage']['error'] === 0) {
           $temppath = $_FILES['profileimage']['tmp_name'];
           $extension = pathinfo($_FILES['profileimage']['name'], PATHINFO_EXTENSION);
           
           if (validateImage($temppath, $extension)){
               // if image is valid, and everything else is valid, meaning user will be entered, save the image
               if ($valid) {
                   $newpath = buildUploadPath("{$_POST['username']}_Profile", $extension);
                   $imageresize = new ImageResize($temppath);
                   $imageresize->crop(512,512);
                   // save it to the new path
                   $imageresize->save($newpath);
                   //move_uploaded_file($temppath, $newpath);
                   $profileimagepath = $newpath;
               }           
           }
           else{
               // upload is not an image, so error
               $valid = false;
               $profileimageerror = 'File must be an image. (jpg, png, gif)';
               $profileimagevalidclass = 'is-invalid';
           }
        }
        // else{
        //     // is set, but an error occurred.
        //     $valid = false;
        //     $profileimageerror = 'An error occurred. Error code:' .  $_FILES['profileimage']['error'];
        //     $profileimagevalidclass = 'is-invalid';
        // }

        // if delete profile image is checked then set path to null and delete the image.
        if (isset($_POST['profileimagedelete']) && $_POST['profileimagedelete'] === 'on') {
            $profileimagepath = null;
            unlink($user['ProfilePicture']);
        }


       // if all inputs are valid update account
       if ($valid) {
            require('connect.php');

            $query = "UPDATE Users SET FirstName = :firstname, LastName = :lastname, Username = :username,
                    UserType = :usertype, ProfilePicture = :profilepicture, CurrentJobsite = :jobsite, Email = :email 
                    WHERE UserID = :id";

            $create = $db->prepare($query);

            // All inputs are validated at this point
            $create->bindValue(':firstname', filter_input(INPUT_POST, 'firstname', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/"))));
            $create->bindValue(':lastname', filter_input(INPUT_POST, 'lastname', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/"))));
            $create->bindValue(':username', strtolower(filter_input(INPUT_POST, 'username', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9]*$/")))));
            $create->bindValue(':usertype', filter_input(INPUT_POST, 'usertype', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $create->bindValue(':profilepicture', $profileimagepath);
            $create->bindValue(':jobsite', filter_input(INPUT_POST, 'jobsite', FILTER_SANITIZE_NUMBER_INT));
            $create->bindValue(':email', filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
            $create->bindValue(':id', filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));

            if ($create->execute()) {
                $successmessage = "{$_POST['username']} successfully updated.";

                // update the user to reflect the changes to the user.
                $user = getUser(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT), $invalidmessage);
                if ($_SESSION['userid'] == $user['UserID']) {
                    $_SESSION['profilepicture'] = $user['ProfilePicture'];
                }
                
            }
            else {
                $errormessage = 'An error occurred when updating user, please try again.';
            }
       }


   }

   
?>

<?php include('header.php'); ?>

   <div class="container">
        <?php if($invalidmessage): ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Error</h4>
                <p><?= $invalidmessage ?></p>
                <hr>
                <a href="viewusers.php" class="btn btn-primary mb-2">Return to users view</a>
            </div>
        <?php else: ?>
            <form method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                <div class="row">
                    <div id="left_col" class="col-md-6">   
                        <div class="form-group row"> 
                            <label for="firstname" class="col-lg-3 col-form-label">First Name:</label>
                            <div class="col-lg-9">
                                <input id="firstname" name="firstname" type="text" class="form-control <?= $fnamevalidclass ?>" value="<?= $user['FirstName'] ?>" required>
                                <div class="invalid-feedback">
                                    <?= $fnameerror ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="lastname" class="col-lg-3 col-form-label">Last Name:</label>
                            <div class="col-lg-9">
                                <input id="lastname" name="lastname" type="text" class="form-control <?= $lnamevalidclass ?>" value="<?= $user['LastName'] ?>" required>
                                <div class="invalid-feedback">
                                    <?= $lnameerror ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-lg-3 col-form-label">Email:</label>
                            <div class="col-lg-9">
                                <input id="email" name="email" type="email" class="form-control <?= $emailvalidclass ?>" value="<?= $user['Email'] ?>" required>
                                <div class="invalid-feedback">
                                    <?= $emailerror ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="username" class="col-lg-3 col-form-label">Username:</label>
                            <div class="col-lg-9">
                                <input id="username" name="username" type="text" class="form-control  <?= $usernamevalidclass ?>" value="<?= $user['Username'] ?>" required>
                                <div class="invalid-feedback">
                                    <?= $usernameerror ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="usertype" class="col-lg-3 col-form-label">Account Type:</label>
                            <div class="col-lg-9">
                                <select name="usertype" id="usertype" class="custom-select <?= $usertypevalidclass ?>">
                                    <option value="employee" <?php if($user['UserType'] == 'employee'){echo 'selected';} ?>>Employee</option>
                                    <option value="accountant" <?php if($user['UserType'] == 'accountant'){echo 'selected';} ?>>Accountant</option>
                                    <option value="admin" <?php if($user['UserType'] == 'admin'){echo 'selected';} ?>>Admin</option>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $usertypeerror ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="jobsite" class="col-lg-3 col-form-label">Jobsite:</label>
                            <div class="col-lg-9">
                                <select name="jobsite" id="jobsite" class="custom-select <?= $jobsitevalidclass ?>">
                                    <?php foreach ($jobsites as $jobsite): ?>
                                        <option value="<?= $jobsite['JobsiteID'] ?>" <?php if($user['CurrentJobsite'] == $jobsite['JobsiteID']){echo 'selected';} ?>><?= $jobsite['Name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $jobsiteerror ?>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div id="right_col" class="form-group col-md-6">   

                        <div class="form-group row">
                            <label for="profileimage" class="col-lg-4 col-form-label">Profile Image:</label>
                            <div class="col-lg-8">
                                <input id="profileimage" name="profileimage" type="file" class="form-control <?= $profileimagevalidclass ?>" accept="image/png, .jpeg, .jpg, image/gif">
                                <div class="invalid-feedback">
                                    <?= $profileimageerror ?>
                                </div>
                            </div>                       
                        </div>
                        <?php if(!empty($user['ProfilePicture'])): ?>
                            <div class="form-group row">
                                <label for="profileimagedelete" class="col-lg-4 col-form-label">Delete Profile Image:</label>
                                <div class="col-lg-8">
                                    <input id="profileimagedelete" name="profileimagedelete" type="checkbox" class="h-100">
                                </div>                       
                            </div>
                        <?php endif ?>

                        <div class="d-flex justify-content-center">
                            <div class="card w-50">
                                <img src="<?php if($user['ProfilePicture'] != null){echo $user['ProfilePicture'];}else{echo 'images\BlankProfile.jpg';} ?>" alt="Profile picture." class="card-img-top">
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <div class="form-group row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-9">
                                <input class="btn btn-primary w-100 mb-2" type="submit" name="update" value="Update User">

                                <?php if($errormessage): ?>
                                    <p class="text-danger"><?= $errormessage ?></p>
                                <?php endif ?>

                                <?php if($successmessage): ?>
                                    <p class="text-success"><?= $successmessage ?> <a href="viewusers.php" >Return to users view</a></p>
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
