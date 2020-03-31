<!-- 
    Authenticates that a user is logged in, and if $authusertype is specified that the logged in user is that type.
    Author: Jeremy Grift
    Created: March 5, 2020
    Last Updated: March 20, 2020
 -->
 <?php 
   // If session is not started, start.
   if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
   }

   $authusertype;
   if (empty($authusertype)) {
      $authusertype = 'any';
   }
   
   if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
      if($authusertype == 'any' || $_SESSION['usertype'] == $authusertype){
         // valid
      }
      else{
         header('location: insufficientprivileges.php');
         exit();
      }
   }
   else{
      // user is not logged in so redirect to login page
      header('location: login.php');
      exit();
   }


   







   // To be implemented as a time out latter

   //$time = $_SERVER['REQUEST_TIME'];

   /**
   * for a 30 minute timeout, specified in seconds
   */
   //$timeout_duration = 1800;

   /**
   * Here we look for the user's LAST_ACTIVITY timestamp. If
   * it's set and indicates our $timeout_duration has passed,
   * blow away any previous $_SESSION data and start a new one.
   */
   // if (isset($_SESSION['LAST_ACTIVITY']) && 
   //    ($time - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
   //    session_unset();
   //    session_destroy();
   //    session_start();
   // }

   /**
   * Finally, update LAST_ACTIVITY so that our timeout
   * is based on it and not the user's login time.
   */
   //$_SESSION['LAST_ACTIVITY'] = $time;
 ?>