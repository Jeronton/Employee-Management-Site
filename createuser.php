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

    // Checks the username against the database to determine if it is unique
    function UniqueUsername($username){

        require('connect.php');
        $unique = false;
        $query = "SELECT 1 FROM Users WHERE Username = :username";
        $statement = $db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();

        if($statement->rowCount() == 0){
            $unique = true;
        }
        return $unique;
    }

    // Checks that the jobsite exists in the database
    function JobsiteExists($jobsiteid){
        require('connect.php');
        $exists = false;
        $query = "SELECT 1 FROM Jobsites WHERE JobsiteID = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $jobsiteid);
        $statement->execute();

        if($statement->rowCount() == 1){
            $exists = true;
        }
        return $exists;
    }

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
    $emailerror = '*required';

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
        // only allows ,.- and spac
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
        elseif (! filter_input(INPUT_POST, 'username', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-z0-9]*$/")))) {
            $usernameerror = "*Only lowercase letters and numbers are permitted";
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




        // if all inputs are valid create account
        if ($valid) {
            require('connect.php');

            $query = "INSERT INTO Users (FirstName, LastName, Username, Password, UserType, ProfilePicture, CurrentJobsite) 
                    VALUES (:firstname, :lastname, :username, :password, :usertype, :profilepicture, :jobsite)";

            $create = $db->prepare($query);

            // All inputs are validated at this point
            $create->bindValue(':firstname', $_POST['firstname']);
            $create->bindValue(':lastname', $_POST['lastname']);
            $create->bindValue(':username', $_POST['username']);
            $create->bindValue(':password', password_hash($_POST['password'], PASSWORD_DEFAULT ));
            $create->bindValue(':usertype', $_POST['usertype']);
            $create->bindValue(':profilepicture', null);
            $create->bindValue(':jobsite', null);

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
        <form method="POST" class="needs-validation" novalidate>
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
                            <input id="profileimage" name="profileimage" type="file" class="form-control <?= $profileimagevalidclass ?>">
                            <div class="invalid-feedback">
                                <?= $profileimageerror ?>
                            </div>
                        </div>                       
                    </div>

                    <div class="card w-50">
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

<!-- 
                    <div class="form-group row">
                        <label for="" class="col-lg-3 col-form-label">:</label>
                        <div class="col-lg-9">
                            <input id="" name="" type="text" class="form-control" required>
                            <div class="invalid-feedback">
                                *Required
                            </div>
                        </div>
                    </div> -->