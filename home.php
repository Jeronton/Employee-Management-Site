
 <?php 
 /*
    Directs the user to their respective home page, based on the type of user.
    Author: Jeremy Grift
    Created: March 23, 2020
*/

    // If session is not started, start.
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
     }

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
                ],
                [
                    'href' => 'viewjobsites.php',
                    'text' => 'View Jobsites'
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
                ],
                [
                    'href' => 'viewusersrecords.php',
                    'text' => 'View Records'
                ]
            ];
            break;

        case 'accountant':
            $location ='accountanthome.php';
            $_SESSION['header']['navlinks'] = [
                [
                    'href' => 'home.php',
                    'text' => 'Home'
                ],
                [
                    'href' => 'addrecord.php',
                    'text' => 'Add Record'
                ],
                [
                    'href' => 'viewusersrecords.php',
                    'text' => 'View Personal Records'
                ],
                [
                    'href' => 'viewrecords.php',
                    'text' => 'View Records'
                ]
            ];
            break;
        
        default:
            $location = 'login.php';
            break;
            
    }
    header("location: {$location}");
 ?>