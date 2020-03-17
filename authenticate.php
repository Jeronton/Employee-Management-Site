<!-- 
    Provides logic to authenticate users to the Users Table.
    Author: Jeremy Grift
    Created: March 5, 2020
    Last Updated: March 5, 2020
 -->
 <?php 
   /*
   *  Verifies the login information is correct and updates SESSION values
   *  
   * $Username The username to verify.
   * $password The plaintext password to verify against the password of the User.
   */
   function VerifyLogin($username, $password){
      require('connect.php');
      $valid = false;

      $query = "SELECT Password FROM Users WHERE Username = :username";
      $statement = $db->prepare($query);
      $statement->bindValue(':username', filter_var($username, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
      $statement->execute();

      // checks the password against the salted and hashed password from the database
      if ($user = $statement->fetch() password_verify($password, $user['Password'])) {
         $valid = true;
      }

      return $valid;
   }



   


   if (!isset($_SESSION['username']) || !isset($_SESSION['password'])
      || !VerifyLogin($_SESSION['username'], $_SESSION['password'])) {

   header('location: login.php');
   exit("Access Denied.");
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