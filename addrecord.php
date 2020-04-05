<!-- 
    Adds an record to the employee records table.
    Author: Jeremy Grift
    Created: March 30, 2020
    Last Updated: March 30, 2020
 -->
 <?php 
    // If session is not started, start.
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $authusertype = 'employee';
    require('authenticate.php');
    require('utilities.php');
    $title = 'Add Record';

    // all error messages;
    $errormessage = '';
    $hourserror = '';
    $startodometererror = '';
    $endodometererror = '';
    $jobsiteerror = '';
    $dateerror = '';
    $commentserror = '';
    
    $successmessage = '';

    // load user data.
    $user = getUser($_SESSION['userid'], $errormessage);
    
    // load jobsites
    $jobsites = GetJobsites();


    // if post, validate all data
    if (isset($_POST['submit'])) {
        $valid = true;

        // HOURS

        // if empty, but 0 is considered empty, so if empty and not 0
        if (empty($_POST['hours'])) {
            $valid = false;
            $hourserror = '*Required';
        }
        elseif (filter_input(INPUT_POST, 'hours', FILTER_VALIDATE_FLOAT) === false 
                || filter_input(INPUT_POST, 'hours', FILTER_VALIDATE_FLOAT) < 0 
                || filter_input(INPUT_POST, 'hours', FILTER_VALIDATE_FLOAT) > 24)  {
            $valid = false;
            $hourserror = '*Must be between 0 and 24';
        }

        // ODOMETER

        // start and end odometer are optional, but if one is specified the other must be as well
        if (!empty($_POST['startodometer'] || !empty($_POST['endodemeter']))) {

            $startvalue = 0;

            if (empty($_POST['startodometer'])) {
                $valid = false;
                $startodometererror = '*Required if an ending reading is provided.';
            }
            elseif (filter_input(INPUT_POST, 'startodometer', FILTER_VALIDATE_FLOAT) === false || filter_input(INPUT_POST, 'startodometer', FILTER_VALIDATE_FLOAT) < 0) {
                $valid = false;
                $startodometererror = '*Must be an valid positive number';
            }
            else{
                $startodometervalue = filter_input(INPUT_POST, 'startodometer', FILTER_VALIDATE_FLOAT);
            }

            if (empty($_POST['endodometer'])) {
                $valid = false;
                $endodometererror = '*Required if an starting reading is provided';
            }
            elseif (filter_input(INPUT_POST, 'endodometer', FILTER_VALIDATE_FLOAT) === false || filter_input(INPUT_POST, 'endodometer', FILTER_VALIDATE_FLOAT) < 0) {
                $valid = false;
                $endodometererror = '*Must be an valid positive number';
            }
            elseif(filter_input(INPUT_POST, 'endodometer', FILTER_VALIDATE_FLOAT) < $startodometervalue){
                $valid = false;
                $endodometererror = '*The ending reading must be greater then the starting reading';
            }
            else{
                $endodometervalue = filter_input(INPUT_POST, 'endodometer', FILTER_VALIDATE_FLOAT);
            }
        }

        // JOBSITE

        if(! JobsiteExists(filter_input(INPUT_POST, 'jobsite', FILTER_SANITIZE_NUMBER_INT))){
            $valid = false;
            $jobsiteerror = '*That jobsite does not exist';
        }


        // DATE

        if(empty($_POST['date'])){
            $valid = false;
            $dateerror = '*Required';
        }
        elseif (filter_input(INPUT_POST, 'date', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/"))) === false) {
            $valid = false;
            $dateerror = '*Invalid date, try this format: YYYY-MM-DD';
        }
        elseif ( strtotime(filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS)) < strtotime('2000-01-01') 
                || strtotime(filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS)) > time() ){
            $valid = false;
            $dateerror = '*Date is out of range';
        }

        // COMMENTS

        if (empty($_POST['comments'])){
            $commentsvalue = null;
        }
        elseif ( strlen($_POST['comments']) > 250 ) {
            $valid = false;
            $commentserror = '*Maximum 250 characters';
        }
        else{
            $commentsvalue = filter_input(INPUT_POST, 'comments', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        // INSERT RECORD

        if ($valid) {
            if (!isset($db)) {
                require('connect.php');
            }

            $insert = "INSERT INTO EmployeeRecords (UserID, Jobsite, Date, Hours, StartOdometer, EndOdometer, Comments)
                        VALUES (:userid, :jobsite, :date, :hours, :start, :end, :comments)";
            $insert = $db->prepare($insert);
            $insert->bindValue(':userid', $_SESSION['userid']);
            $insert->bindValue(':jobsite', filter_input(INPUT_POST, 'jobsite', FILTER_SANITIZE_NUMBER_INT));
            $insert->bindValue(':date', filter_input(INPUT_POST, 'date', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/"))));
            $insert->bindValue(':hours',  filter_input(INPUT_POST, 'hours', FILTER_VALIDATE_FLOAT));
            if (empty($startodometervalue)) {
                $startodometervalue = null;
            }
            $insert->bindValue(':start',  $startodometervalue);
            if (empty($endodometervalue)) {
                $endodometervalue = null;
            }
            $insert->bindValue(':end', $endodometervalue);
            $insert->bindValue(':comments', $commentsvalue);

            if ($insert->execute()) {
                $successmessage = 'Record successfully entered!';
            }
            else{
                $errormessage = 'An error occurred while attempting to enter record.';
            }
        }

    }

 ?>
 
 <?php include('header.php') ?>
    <div class="container">
        <?php if($errormessage): ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Error</h4>
                <p><?= $errormessage ?></p>
                <hr>
                <a href="home.php" class="btn btn-primary mb-2">Return to home page</a>
            </div>
        <?php else: ?>  
            <form method="POST" action="#" class="needs-validation" enctype="multipart/form-data" novalidate>
                <div class="row">

                    <div class="col-md-6">   

                        <div class="form-group row"> 
                            <label for="hours" class="col-lg-4 col-form-label">Hours:</label>
                            <div class="col-lg-8">
                                <input id="hours" name="hours" type="text" class="form-control <?php if($hourserror){echo 'is-invalid';} ?>"placeholder="required" required>
                                <div class="invalid-feedback">
                                    <?= $hourserror ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row"> 
                            <label for="startodometer" class="col-lg-4 col-form-label">Starting odometer reading:</label>
                            <div class="col-lg-8">
                                <input id="startodometer" name="startodometer" type="text" class="form-control <?php if($startodometererror){echo 'is-invalid';} ?>" placeholder="optional">
                                <div class="invalid-feedback">
                                    <?= $startodometererror ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row"> 
                            <label for="endodometer" class="col-lg-4 col-form-label">Ending odometer reading:</label>
                            <div class="col-lg-8">
                                <input id="endodometer" name="endodometer" type="text" class="form-control <?php if($endodometererror){echo 'is-invalid';} ?>" placeholder="optional">
                                <div class="invalid-feedback">
                                    <?= $endodometererror ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">

                        <div class="form-group row">
                            <label for="jobsite" class="col-lg-4 col-form-label">Jobsite:</label>
                            <div class="col-lg-8">
                                <select name="jobsite" id="jobsite" class="custom-select  <?php if($jobsiteerror){echo 'is-invalid';} ?>">
                                    <?php foreach ($jobsites as $jobsite): ?>
                                        <option value="<?= $jobsite['JobsiteID'] ?>" <?php if($user['CurrentJobsite'] == $jobsite['JobsiteID']){echo 'selected';} ?>><?= $jobsite['Name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $jobsiteerror ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row"> 
                            <label for="date" class="col-lg-4 col-form-label">Date:</label>
                            <div class="col-lg-8">
                                <input id="date" name="date" type="date" class="form-control <?php if($dateerror){echo 'is-invalid';} ?>" value="<?= date('Y-m-d') ?>" required>
                                <div class="invalid-feedback">
                                    <?= $dateerror ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row"> 
                            <label for="comments" class="col-lg-4 col-form-label">Comments:</label>
                            <div class="col-lg-8">
                                <textarea id="comments" name="comments" class="form-control <?php if($commentserror){echo 'is-invalid';} ?>"></textarea>
                                <div class="invalid-feedback">
                                    <?= $commentserror ?>
                                </div>
                            </div>
                        </div>
                        
                    </div>

                </div>
                <div class="form-group col-md-6">
                    <div class="form-group row">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-8">
                            <input class="btn btn-primary w-100 mb-2" type="submit" name="submit" value="Enter Record">

                            <?php if($errormessage): ?>
                                <p class="text-danger"><?= $errormessage ?></p>
                            <?php endif ?>

                            <?php if($successmessage): ?>
                                <p class="text-success"><?= $successmessage ?></p>
                            <?php endif ?>
                        </div>
                    </div>

                    
                </div>
            </form>
        <?php endif ?>
    </div>
</body>
</html>