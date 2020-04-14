 <?php 
 /*
    Description
    Author: Jeremy Grift
    Created: March 23, 2020
    Last Updated: March 23, 2020
*/

   // If session is not started, start.
   if (session_status() !== PHP_SESSION_ACTIVE) {
   session_start();
   }
   $authusertype = 'admin';
   require('authenticate.php');

   $title = 'Home';
 ?>
 
<?php include('header.php'); ?>
<div class="container">
   <h4>Admin Home</h4>
   <a href="viewusers.php" class="btn btn-primary mb-2">View Users</a>
   <a href="createuser.php" class="btn btn-primary mb-2">Add An User</a>
   <a href="viewjobsites.php" class="btn btn-primary mb-2">View Jobsites</a>
   <a href="addjobsite.php" class="btn btn-primary mb-2">Add A Jobsite</a>
</div>
</body>
</html>