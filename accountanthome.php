<!-- 
    The home page of an accountant user.
    Author: Jeremy Grift
    Created: April 7, 2020
 -->
 <?php 
    // If session is not started, start.
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $authusertype = 'accountant';
    require('authenticate.php');

    $title = 'Home';
 ?>
 
<?php include('header.php'); ?>
<div class="container">
   <h4>Accountant Home</h4>
   <!-- <a href="addrecord.php" class="btn btn-primary mb-2">Add Record</a> -->
   <a href="viewrecords.php" class="btn btn-primary mb-2">View Records</a>
</div>
</body>
</html>