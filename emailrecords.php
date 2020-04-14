<?php 
/*
   Confirms that the user wants to email the records to themselves.
   Author: Jeremy Grift
   Created: April 13, 2020
*/
    // If session is not started, start.
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $authusertype = 'accountant';
    require('authenticate.php');

    $title = 'Email Records';

    $errormessage = '';

    // set the record insert parameters.
    $ri_email = true;
    $ri_confirm = true;
    if (isset($_SESSION['pagesize'])) {
        $ri_pagesize = $_SESSION['pagesize']; 
    }
    if (isset($_SESSION['search'])) {
        $ri_search = $_SESSION['search'];
    }
    if (isset($_SESSION['sortby'])) {
        $ri_sortby = $_SESSION['sortby'];
    }
    if (isset($_SESSION['employees'])) {
        $ri_employees = $_SESSION['employees'];
    }
    if (isset($_SESSION['jobsites'])) {
        $ri_jobsites = $_SESSION['jobsites'];
    }
    if (isset($_SESSION['startdate'])) {
        $ri_startdate = $_SESSION['startdate'];
    }
    if (isset($_SESSION['enddate'])) {
        $ri_enddate = $_SESSION['enddate'];
    }
    
    // get records as an string
    ob_start();
    include 'recordsinsert.php';
    $records =  ob_get_clean();

    // To send HTML mail, the Content-type header must be set
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    
    // Create email headers
    $headers .= 'X-Mailer: PHP/' . phpversion();

    // Send email
    try {
        mail($_SESSION['useremail'], 'Employee Records', $records, $headers );
    } catch (\Throwable $th) {
        $errormessage = 'An error occurred while sending email, please try again later.';
    }
    

?>
 
   <?php include('header.php') ?>

   <?php if($errormessage): ?>
        <div class="container alert alert-danger" role="alert">
            <h4 class="alert-heading">Error</h4>
            <p><?= $errormessage ?> </p>
            <hr>
            <a href="viewrecords.php" class="btn btn-primary mb-2">Cancel</a>
        </div>
    <?php else: ?>
        <div class="container alert alert-success" role="alert">
            <h4 class="alert-heading">Email Sent</h4>
            <p>The email was successfully sent to your inbox.</p>
            <hr>
            <a href="viewrecords.php" class="btn btn-primary mb-2">Return to view records.</a>
        </div>
    <?php endif ?>
</body>
</html>