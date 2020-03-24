<!-- 
    Description
    Author: Jeremy Grift
    Created: March 23, 2020
    Last Updated: March 23, 2020
 -->
 <?php 
    $errormessage = '';
    $deleted = false;
    $username = 'user';

    if (empty($_GET['id'])) {
        $errormessage = 'No user is specified.';
    }
    elseif (filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) === false) {
        $errormessage = 'Non-numeric user id entered.';
    }
    else{
        // is validated
        require('connect.php');
        $query = "SELECT Username FROM Users WHERE UserID = :userid";
        $user = $db->prepare($query);
        $user->bindValue(':userid', filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
        $user->execute();
        $username = $user->fetch()['Username'];
    }

   if (isset($_GET['confirm']) && filter_input(INPUT_GET, 'confirm', FILTER_VALIDATE_INT) && $errormessage == '' ) {
       if (filter_input(INPUT_GET, 'confirm', FILTER_SANITIZE_NUMBER_INT) == 1) {
           // is confirmed to delete, so delete.
            require('connect.php');
            $query = "DELETE FROM Users WHERE UserID = :userid";
            $user = $db->prepare($query);
            $user->bindValue(':userid', filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
            $deleted = $user->execute();
       }
   }


    $title = "Delete {$username}";
 ?>
 
 <?php include('header.php') ?>
    <?php if($errormessage): ?>
        <div class="container alert alert-danger" role="alert">
            <h4 class="alert-heading">Error</h4>
            <p><?= $errormessage ?></p>
            <hr>
            <a href="viewusers.php" class="btn btn-primary mb-2">Cancel</a>
        </div>
    <?php elseif($deleted): ?>
        <div class="container alert alert-success" role="alert">
            <h4 class="alert-heading">Success</h4>
            <p><?= $username ?> successfully deleted.</p>
            <hr>
            <a href="viewusers.php" class="btn btn-primary mb-2">Return to users view.</a>
        </div>
    <?php else: ?>
        <div class="container alert alert-warning" role="alert">
            <h4 class="alert-heading">Are You Sure?</h4>
            <p>Are you sure you want to delete <?= $username ?>?</p>
            <hr>
            <a href="deleteuser.php?id=<?=filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) ?>&confirm=1" class="btn btn-primary mb-2">Delete</a>
            <a href="viewusers.php" class="btn btn-primary mb-2">Cancel</a>
        </div>
    <?php endif ?>
</body>
</html>