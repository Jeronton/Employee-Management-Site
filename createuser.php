<!-- 
    
    Author: Jeremy Grift
    Created: March 17, 2020
    Last Updated: March 17, 2020
 -->

 <?php 
    session_start();
    
    $authusertype = 'admin';
    require('authenticate.php');

    require('connect.php');
    require('utilities.php');
    include 'php-image-resize-master/lib/ImageResize.php' ;
	use \Gumlet\ImageResize;
    

    $title = 'Create User';


    // load jobsites
    $jobsites = "SELECT JobsiteID, Name FROM Jobsites WHERE IsActive = true";
    $jobsites = $db->prepare($jobsites);
    $jobsites->execute();


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
    if (isset($_POST['create'])) {

        $valid = true;

        // FISRT NAME
        
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
        // only allows letters and numbers (will auto lowercase username).
        elseif (! filter_input(INPUT_POST, 'username', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9]*$/")))) {
            $usernameerror = "*Only letters and numbers are permitted";
            $usernamevalidclass = 'is-invalid';
            $valid = false;
        }
        // Username must be unique
        elseif (! UniqueUsername($_POST['username'])) {
            $usernameerror = '*That username already exists, try a different username';
            $usernamevalidclass = 'is-invalid';
            $valid = false;
        }

        // PASSWORD

        // if no password entered
        if (empty($_POST['password'])) {
            $passwordvalidclass = 'is-invalid';
            $valid = false;
        }
        if (empty($_POST['confirmpassword'])) {
            $passconfirmvalidclass = 'is-invalid';
            $valid = false;
        }
        // 
        elseif ($_POST['confirmpassword'] != $_POST['password']) {
            $passconfirmerror = '*Passwords do not match';
            $passconfirmvalidclass = 'is-invalid';
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

        // The variable to add to the database
        $profileimagepath = null;
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





        // if all inputs are valid create account
        if ($valid) {
            if (!isset($db)) {
                require('connect.php');
            }
            $query = "INSERT INTO Users (FirstName, LastName, Username, Password, UserType, ProfilePicture, CurrentJobsite, Email) 
                    VALUES (:firstname, :lastname, :username, :password, :usertype, :profilepicture, :jobsite, :email)";

            $create = $db->prepare($query);

            $create->bindValue(':firstname', filter_input(INPUT_POST, 'firstname', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/"))));
            $create->bindValue(':lastname', filter_input(INPUT_POST, 'lastname', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/"))));
            $create->bindValue(':username', strtolower(filter_input(INPUT_POST, 'username', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9]*$/")))));
            $create->bindValue(':password', password_hash($_POST['password'], PASSWORD_DEFAULT ));
            $create->bindValue(':usertype', filter_input(INPUT_POST, 'usertype', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $create->bindValue(':profilepicture', $profileimagepath);
            $create->bindValue(':jobsite', filter_input(INPUT_POST, 'jobsite', FILTER_SANITIZE_NUMBER_INT));
            $create->bindValue(':email', filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

            if ($create->execute()) {
                $successmessage = "{$_POST['username']} successfully created.";
            }
            else {
                $errormessage = 'Unable to create user, please try again.';
            }
        }


    }
 ?>

 <?php include('header.php'); ?>
    <div class="container">
        <form method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
            <div class="row">
                <div id="left_col" class="col-md-6">   
                    <div class="form-group row"> 
                        <label for="firstname" class="col-lg-3 col-form-label">First Name:</label>
                        <div class="col-lg-9">
                            <input id="firstname" name="firstname" type="text" class="form-control <?= $fnamevalidclass ?>" required>
                            <div class="invalid-feedback">
                                <?= $fnameerror ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="lastname" class="col-lg-3 col-form-label">Last Name:</label>
                        <div class="col-lg-9">
                            <input id="lastname" name="lastname" type="text" class="form-control <?= $lnamevalidclass ?>" required>
                            <div class="invalid-feedback">
                                <?= $lnameerror ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="email" class="col-lg-3 col-form-label">Email:</label>
                        <div class="col-lg-9">
                            <input id="email" name="email" type="email" class="form-control <?= $emailvalidclass ?>" required>
                            <div class="invalid-feedback">
                                <?= $emailerror ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="username" class="col-lg-3 col-form-label">Username:</label>
                        <div class="col-lg-9">
                            <input id="username" name="username" type="text" class="form-control  <?= $usernamevalidclass ?>" required>
                            <div class="invalid-feedback">
                                <?= $usernameerror ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password" class="col-lg-3 col-form-label">Password:</label>
                        <div class="col-lg-9">
                            <input id="password" name="password" type="password" class="form-control <?= $passwordvalidclass ?>" required>
                            <div class="invalid-feedback">
                                <?= $passworderror ?> 
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="confirmpassword" class="col-lg-3 col-form-label">Confirm Password:</label>
                        <div class="col-lg-9">
                            <input id="confirmpassword" name="confirmpassword" type="password" class="form-control <?= $passconfirmvalidclass ?>" required>
                            <div class="invalid-feedback">
                                <?= $passconfirmerror ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="usertype" class="col-lg-3 col-form-label">Account Type:</label>
                        <div class="col-lg-9">
                            <select name="usertype" id="usertype" class="custom-select <?= $usertypevalidclass ?>">
                                <option value="employee" selected >Employee</option>
                                <option value="accountant">Accountant</option>
                                <option value="admin">Admin</option>
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
                                    <option value="<?= $jobsite['JobsiteID'] ?>"><?= $jobsite['Name'] ?></option>
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

                    <div class="card w-75">
                        <img src="images/BlankProfile.jpg"" alt="Blank profile picture." class="card-img-top">
                    </div>
                </div>

                <div class="form-group col-md-6">
                    <div class="form-group row">
                        <div class="col-lg-3"></div>
                        <div class="col-lg-9">
                            <input class="btn btn-primary w-100 mb-2" type="submit" name="create" value="Create User">

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
