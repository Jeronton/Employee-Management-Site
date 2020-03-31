<!-- 
    The home page of an employee user.
    Author: Jeremy Grift
    Created: March 30, 2020
    Last Updated: March 30, 2020
 -->
 <?php 
    session_start();

    $title = 'Home';
 ?>
 
<?php include('header.php'); ?>
<div class="container">
   <h4>Employee Home</h4>
   <a href="addrecord.php" class="btn btn-primary mb-2">Add Record</a>
</div>
</body>
</html>