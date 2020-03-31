<!-- 
    Directs the user to their respective home page, based on the type of user.
    Author: Jeremy Grift
    Created: March 23, 2020
    Last Updated: March 23, 2020
 -->
 <?php 
    session_start();

    $location;
    require('authenticate.php');
    switch ($_SESSION['usertype']) {
        case 'admin':
            $location ='adminhome.php';
            $_SESSION['header']['navlinks'] = [
                [
                    'href' => 'home.php',
                    'text' => 'Home'
                ],
                [
                    'href' => 'createuser.php',
                    'text' => 'Add User'
                ],
                [
                    'href' => 'viewusers.php',
                    'text' => 'View Users'
                ]
            ];
            break;
        case 'employee':
            $location ='employeehome.php';
            $_SESSION['header']['navlinks'] = [
                [
                    'href' => 'home.php',
                    'text' => 'Home'
                ],
                [
                    'href' => 'addrecord.php',
                    'text' => 'Add Record'
                ]
            ];
            break;

        case 'accountant':
            $location = 'index.php';
            //$location ='accountanthome.php';
            break;
        
        default:
            $location = 'login.php';
            break;
            
    }
    header("location: {$location}");
 ?>