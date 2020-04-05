<!-- 
    Description
    Author: Jeremy Grift
    Created: March 23, 2020
    Last Updated: March 23, 2020
 -->
 <?php 
    session_start();

    $title = 'Home';
 ?>
 
<?php include('header.php'); ?>
<div class="container">
   <h4>Admin Home</h4>
   <a href="viewusers.php" class="btn btn-primary mb-2">View Users</a>
   <a href="createuser.php" class="btn btn-primary mb-2">Add An User</a>
</div>
</body>
</html>