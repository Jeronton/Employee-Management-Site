<!-- 
    Provides logic to authenticate users to the Users Table.
    Author: Jeremy Grift
    Created: March 5, 2020
    Last Updated: March 5, 2020
 -->
 <?php 

    // Temporary, for testing purposes. Will be replaced with proper login using Users table in DB.
    define('ADMIN_LOGIN','wally');

    define('ADMIN_PASSWORD','mypass');
  
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])
  
        || ($_SERVER['PHP_AUTH_USER'] != ADMIN_LOGIN)
  
        || ($_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD)) {
  
      header('HTTP/1.1 401 Unauthorized');
  
      header('WWW-Authenticate: Basic realm="Our Blog"');
  
      exit("Access Denied: Username and password required.");
  
    } 
 ?>