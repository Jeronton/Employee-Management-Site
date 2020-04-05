<!-- 
   Deletes the record if the user has permission to delete it.
   Author: Jeremy Grift
   Created: March 31, 2020
   Last Updated: March 31, 2020
-->
<?php 
    // If session is not started, start.
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    require('authenticate.php');

    $title = 'Delete Record';

    require('utilities.php');

    $errormessage = '';
    $successfullydeleted = false;

    // Validate the GET record id
    if (empty($_GET['recordid'])) {
        $errormessage = 'No record selected for deletion.';
    }
    elseif (filter_input(INPUT_GET, 'recordid', FILTER_VALIDATE_INT) === false ){
        $errormessage = 'Invalid record Id.';
    }
    elseif (! DoesUserHavePermissionsForRecord(filter_input(INPUT_GET, 'recordid', FILTER_SANITIZE_NUMBER_INT))) {
        $errormessage = 'You do not have permission to delete this record.';
    }

    // if confirm is set and the record id is valid then delete
    if (!empty($_GET['confirm']) && $_GET['confirm'] == 1 && $errormessage === '') {
        if (!isset($db)) {
            require('connect.php');
        }
        $delete = "DELETE FROM EmployeeRecords WHERE RecordId = :recordid";
        $delete = $db->prepare($delete);
        $delete->bindValue(':recordid', filter_input(INPUT_GET, 'recordid', FILTER_SANITIZE_NUMBER_INT) );
        if ($delete->execute()){
            $successfullydeleted = true;
        }
        else{
            $errormessage = 'An error occurred while attempting to delete record.';
        }
    }

?>
 
   <?php include('header.php') ?>
    <div class="container">
    <?php if($errormessage):?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Error</h4>
                <p><?= $errormessage ?></p>
                <hr>
                <a href="viewusersrecords.php" class="btn btn-primary mb-2">Return to view records</a>
            </div>
        <?php elseif($successfullydeleted): ?>
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Success</h4>
                <p>Record successfully deleted.</p>
                <hr>
                <a href="viewusersrecords.php" class="btn btn-primary mb-2">Return to view records</a>
            </div>
        <?php else: ?> 
            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Are You Sure?</h4>
                <p>You are about to delete this an record, you will not be able to recover it. Are you sure you want to delete it?</p>
                <hr>
                <a href="deleterecord.php?recordid=<?= filter_input(INPUT_GET, 'recordid', FILTER_VALIDATE_INT) ?>&confirm=1" class="btn btn-primary mb-2">Delete</a>
                <a href="viewusersrecords.php" class="btn btn-primary mb-2">Cancel</a>
            </div>
        <?php endif ?>
    </div>
</body>
</html>