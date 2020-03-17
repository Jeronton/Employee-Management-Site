<!-- 
    Provides the form to for a user to login.
    Author: Jeremy Grift
    Created: March 5, 2020
    Last Updated: March 16, 2020
 -->

 <?php 
    /*
   *  Verifies the login information is correct and updates SESSION values
   *  
   * $Username The username to verify.
   * $password The plaintext password to verify against the password of the User.
   * 
   * Returns true if login successful, false otherwise
   */
   function login($username, $password){
        require('connect.php');
        $valid = false;

        $query = "SELECT UserID, Password, UserType, FirstName, LastName, FROM Users WHERE Username = :username";
        $statement = $db->prepare($query);
        $statement->bindValue(':username', filter_var($username, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $statement->execute();

        // checks the password against the salted and hashed password from the database
        if ($user = $statement->fetch() && password_verify($password, $user['Password'])) {
            $_SESSION['logged'] = true;
            $_SESSION['usertype'] = $user['UserType'];
            $_SESSION['userid'] = $user['UserID'];
            $_SESSION['firstname'] = $user['FirstName'];
            $_SESSION['lastname'] = $user['LastName'];
            $valid = true;
        }

        return $valid;
    }


    $usernamevalidclass = '';
    $passwordevalidclass = '';
    $successful = false;
    // Used to display incorrect login message on login attempt.
    $errormessage = '';

    // IF is post, attempt login
    if(isset($_POST['login']))
    {
        $usernamevalidclass = '';
        $passwordevalidclass = '';
        $validinput = true;

        if (empty($_POST['username'])) {
            $usernamevalidclass = 'is-invalid';
            $validinput = false;
        }

        if (empty($_POST['password'])) {
            $passwordvalidclass = 'is-invalid';
            $validinput = false;
        }

        if ($validinput) {
            if (login(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS), $_POST['password'])) {
                $successful = true;
            }
            else{
                $errormessage = 'Incorrect password or username. Try again.';
            }

            // $_SESSION['password'] = $_POST['password'];
            // $_SESSION['username'] = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            // include('authenticate.php');
        }


    }
    
 ?>

<?php include('header.php') ?>
    <div class="container-sm bg-light d-flex justify-content-center">
        <form method="POST" class="flex-item needs-validation" novalidate>
            <div class="form-group row">
                <label for="username" class="col-sm-4 col-form-label">Username:</label>
                <div class="col-sm-8">
                    <input id="username" name="username" type="text" class="form-control <?= $usernamevalidclass ?>" required>
                    <div class="invalid-feedback">
                        Please enter a username.
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="password" class="col-sm-4 col-form-label">Password:</label>
                <div class="col-sm-8">
                    <input id="password" name="password" type="password" class="form-control <?= $passwordvalidclass ?>" required>
                    <div class="invalid-feedback">
                        Please enter a password.
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <input class="btn btn-primary" type="submit" name="login" value="Login">
            </div>
            <?php if($errormessage): ?>
                <p class="text-danger"><?= $errormessage ?></p>
            <?php endif ?>
            <?php if($successful): ?>
                <p class="text-success">Login successful</p>
            <?php endif ?>
        </form>
    </div>
 </body>

    <!-- <script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
    'use strict';
    window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
        });
    }, false);
    })();
    </script> -->
 </html>