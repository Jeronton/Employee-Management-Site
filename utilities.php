 <?php 
/*
    Utility functions to be used throughout the project.
    Author: Jeremy Grift
    Created: March 23, 2020
    Last Updated: March 23, 2020
*/


    /*
    * Validates that the image is indeed an image.
    * 
    * temporarypath ~ the temporary path of the image, used to get meme type.
    * newpath ~ the path the image is to be uploaded to, used to get extension.
    * Returns: True if file is an image, false otherwise.
    */
    function validateImage($temporarypath, $extension){
        $valid_mimes = ['image/gif', 'image/jpeg', 'image/png'];
        $valid_extensions = ['gif', 'jpg', 'jpeg', 'png'];

        $mime = mime_content_type($temporarypath);

        return in_array( strtolower($mime), $valid_mimes) && in_array(strtolower($extension), $valid_extensions);
    }

    /*
    * Builds relative the upload path for the an file.
    *
    * filename: the name of the file, if includes extension, do not specify $extension.
    * extension: The extension of the file to be added in addition to the filename, defaults to use only the filename.
    * folder: The folder to put file into, default 'uploads'.
    * Returns: The upload path.
    */
    function buildUploadPath($filename, $extension = '', $folder = 'uploads'){
        $pathsegments = [$folder, $filename . '.' . $extension];
        return join(DIRECTORY_SEPARATOR, $pathsegments);
    }


    // Checks the username against the database to determine if it is unique
   function UniqueUsername($username){

        require('connect.php');
        $unique = false;
        $query = "SELECT 1 FROM Users WHERE Username = :username";
        $statement = $db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();

        if($statement->rowCount() == 0){
            $unique = true;
        }
        return $unique;
    }

    // Checks that the jobsite exists in the database
    function JobsiteExists($jobsiteid){
        require('connect.php');
        $exists = false;
        $query = "SELECT 1 FROM Jobsites WHERE JobsiteID = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $jobsiteid);
        $statement->execute();

        if($statement->rowCount() == 1){
            $exists = true;
        }
        return $exists;
    }

    /*
    * Gets the user of the specific id.
    *
    * $id: The userID of the user.
    * &$invalidmessage an out variable that is used to output an errormessage.
    *
    * Returns: An hash with the user data or null if an error occurs.
    */
    function getUser($id, &$invalidmessage){
        if (!isset($db)) {
            require('connect.php');
        }
        $query = "SELECT UserID, FirstName, Email, LastName, Username, UserType, CurrentJobsite, ProfilePicture
                    FROM Users
                    WHERE UserID = :userid";
        $user = $db->prepare($query);
        $user->bindValue(':userid', $id);
        if($user->execute()){
            $user = $user->fetch();   
            return $user;       
        }
        else {
            $invalidmessage = 'An error occurred while trying to load user.';
            return null;
        }
    }


    /*
    * Gets the user of the specific id.
    *
    * Returns: An hash of all the jobsites or false if an error occurs;
    */
    function GetJobsites(){
        if (!isset($db)) {
            require('connect.php');
        }


        // load jobsites
        $jobsites = "SELECT JobsiteID, Name FROM Jobsites WHERE IsActive = true";
        $jobsites = $db->prepare($jobsites);
       
        // if execute is successful, return its result, otherwise return false.
        if ( $jobsites->execute()) {
            return $jobsites;
        }
        return false;
    }

    /*
    * Determines if the currently logged in user has permission to access/edit/delete the record.
    * A user has permission if it is the owner of the record, or an admin user.
    *
    * $recordid: The id of the record in question.
    *
    * Returns: True if user has permission, false otherwise.
    */
    function DoesUserHavePermissionsForRecord($recordid){
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        require('authenticate.php');

        $result = false;

        // if an admin, has access to all records
        if ( $_SESSION['usertype'] == 'admin') {
            $result = true;
        }
        else {
            if (!isset($db)) {
                require('connect.php');
            }
            
            // find the record by id and owner
            $query = "SELECT 1 FROM employeerecords WHERE UserID = :userid AND RecordID = :recordid";
            $query = $db->prepare($query);
            $query->bindValue(':userid', $_SESSION['userid'], PDO::PARAM_INT);
            $query->bindValue(':recordid', filter_var($recordid, FILTER_SANITIZE_NUMBER_INT), PDO::PARAM_INT);
    
            // if there is no error and an row is returned, then the user has permission as it it their record.
            if ($query->execute() && $query->rowCount() == 1) {
                $result = true;
            }
        } 

        return $result;
    }



    /**
     * Check if a string is a valid date(time)
     *
     * @param string $datestring The date to validate.
     * @param string $format The format of the date.
     * @return bool True if valid, false otherwise.
     */
    function validateDate($datestring, $format = 'Y-m-d') {
        $date = DateTime::createFromFormat($format, $datestring);
        return $date && DateTime::getLastErrors()["warning_count"] == 0 && DateTime::getLastErrors()["error_count"] == 0;
    }

?>