<!-- 
    The logout page of the site. Clears all user related session data.
    Author: Jeremy Grift
    Created: March 20, 2020
    Last Updated: March 20, 2020
 -->
<?php 
    $_SESSION['logged'] = false;
    $_SESSION['usertype'] = '';
    $_SESSION['userid'] = '';
    $_SESSION['firstname'] = '';
    $_SESSION['lastname'] = '';
?>


<?php include('header.php') ?>
    <div class="container d-flex justify-content-center">
        <h3 class="text-success">Successfully logged out</h3>
    </div>
    
    
</body>
</html>