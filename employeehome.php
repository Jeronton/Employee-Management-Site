<!-- 
    The home page of an employee user.
    Author: Jeremy Grift
    Created: March 30, 2020
    Last Updated: March 30, 2020
 -->
 <?php 
   session_start();

   $authusertype = 'employee';
   require('authenticate.php');
   
   $title = 'Home';
 ?>
 
<?php include('header.php'); ?>
<div class="container">
   <h4>Employee Home</h4>
   <a href="addrecord.php" class="btn btn-primary mb-2">Add Record</a>
   <a href="viewusersrecords.php" class="btn btn-primary mb-2">View Records</a>
</div>
</body>
</html>