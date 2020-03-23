<!-- 
    The logout page of the site. Clears all user related session data.
    $showresponse [optional] if false will not show any html response. Used to logout internally.
    Author: Jeremy Grift
    Created: March 20, 2020
    Last Updated: March 23, 2020
 -->
<?php 
    // determines if the 
    $showresponse;
    // Sets showrespnse if unset.
    if (! isset($showresponse)) {
        $showresponse = true;
    }

    $_SESSION['logged'] = false;
    $_SESSION['usertype'] = '';
    $_SESSION['userid'] = '';
    $_SESSION['firstname'] = '';
    $_SESSION['lastname'] = '';
?>

<?php if($showresponse): ?>

<?php include('header.php') ?>
    <div class="container d-flex justify-content-center">
        <h3 class="text-success">Successfully logged out</h3>
    </div>
    
    
</body>
</html>
<?php endif ?>