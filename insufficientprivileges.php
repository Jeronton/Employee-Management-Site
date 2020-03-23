<!-- 
    Shown when a user attempts to perform an task that they do not have permission for. 
    Author: Jeremy Grift
    Created: March 23, 2020
    Last Updated: March 23, 2020
 -->


 <?php 
    session_start();
    $title = 'Insufficient Permissions';
    include("header.php"); 
 ?>
    <div class="container">
        <h4 class="text-caution">You do not have permission to perform this action.</h4>
        <a href="home.php">Return to homepage.</a>
        
    </div>
</body>
</html>