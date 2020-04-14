<?php 

/*
    The functionality to display records including as displaying them as pages. 
    Must be run internally and will output an table element with the records in it.
    Is customizable by specifying  the following parameters:

    $ri_confirm - Required - must be true or will output nothing. Is used to prevent an user from accessing this page directly.

    $ri_employees - optional - An array(or single if only one) of the employeeids of the employee(s) to filter the results by.
                    If zero or unset defaults to all employees.

    $ri_email - optional - If true, adjusts the format to make it more email friendly.

    $ri_jobsites - optional - An array of all the jobsite Id's that are to be included. If zero or unset defaults to all jobsites.

    $ri_sortby - optional - The column to sort the results by, must be one of the following: 'date', 'jobsite', 'odometer' 
                    By default sorts by date.

    $ri_search - optional - If specified, only returns rows that have a name or jobsite like the search condition.


    Author: Jeremy Grift
    Created: April 7, 2020
*/

    // Ensure that page is only accessed internally.
    if (empty($ri_confirm) || ! $ri_confirm){
        echo '<h2>Improperly Accessed.</h2>';
        exit();
    }

    // If session is not started, start.
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $errormessage = '';

    // the where statement to be built based on input parameters.
    $whereStatement = "";

    // the LIMIT statement if paging is enabled
    $limitStatement = "";

    // set print if not set
    if (empty($ri_email)) {
        $ri_email = false;
    }

    // EMPLOYEES

    // add an IN statement to the where clause if trying to filter by employees
    if (!empty($ri_employees)) {
        // check if where has already been added, if so then add &&, otherwise add WHERE
        if (empty($whereStatement)) {
            $addition = "WHERE ";
        }
        else {
            $addition = " && ";
        }

        if (is_array($ri_employees)){
            $addition .= "Employeerecords.UserID IN (";
            $comma = '';
            foreach ($ri_employees as $employee) {
                $addition .= $comma . $employee;
                $comma = ' ,';
            }
            $addition .= ')';
        }
        else{
            $addition .= "UserID = {$ri_employees}";
        }

        // append to WHERE
        $whereStatement .= $addition;
    }

    // DATES

    // add an BETWEEN statement to the where clause if trying to filter by Dates.
    if (!empty($ri_startdate) && !empty($ri_enddate) && validateDate($ri_startdate) && validateDate($ri_enddate) ){
        // check if where has already been added, if so then add &&, otherwise add WHERE
        if (empty($whereStatement)) {
            $addition = "WHERE ";
        }
        else {
            $addition = " && ";
        }

        $addition .= "Date BETWEEN CAST('{$ri_startdate}' AS DATE) AND CAST('{$ri_enddate}' AS DATE)";

        // append to WHERE
        $whereStatement .= $addition;
    }

    // JOBSITES

    if (!empty($ri_jobsites)) {
        // check if WHERE has already been added, if so then add &&, otherwise add WHERE
        if (empty($whereStatement)) {
            $addition = "WHERE ";
        }
        else {
            $addition = " && ";
        }

        $addition .= "Jobsite IN(";
        $comma = '';

        foreach ($ri_jobsites as $site) {
            $addition .= $comma . filter_var($site, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $comma = ',';
        }
        $addition .= ")";

        // append to WHERE
        $whereStatement .= $addition;
        
    }

    // SORT BY

    // THe sort value, default date.
    $sortBy = 'Date DESC';

    if (!empty($ri_sortby)) {
        switch ($ri_sortby) {
            case 'date':
                $sortBy = 'Date DESC';
                break;

            case 'jobsite':
                $sortBy = 'Jobsite DESC, Date DESC';
                break;
            
            case 'odometer':
                $sortBy = 'StartOdometer DESC';
                break;

            default: 
                $sortBy = 'Date DESC';
                break;
        }
    }

    // SEARCH

    if (!empty($ri_search)) {
        // check if WHERE has already been added, if so then add &&, otherwise add WHERE
        if (empty($whereStatement)) {
            $addition = "WHERE ";
        }
        else {
            $addition = " && ";
        }
        $ri_search = filter_var($ri_search, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $addition .= "( LOWER(FirstName) LIKE LOWER('%{$ri_search}%') || LOWER(LastName) LIKE LOWER('%{$ri_search}%') || LOWER(Name) LIKE LOWER('%{$ri_search}%') )";

        // append to WHERE
        $whereStatement .= $addition;
    }

    // PAGEING

    $paging = false;
    // only pageinate if not in print mode
    if (!$ri_email && !empty($ri_pagenum) && filter_var($ri_pagenum, FILTER_VALIDATE_INT) !== false) {
        $paging = true;

        // Set the pagesize
        if (empty($ri_pagesize)) {
            // set page size to default.
            $ri_pagesize = 25;
        }
        // validate pagesize
        elseif (filter_var($ri_pagesize, FILTER_VALIDATE_INT) !== false) {
            $ri_pagesize = abs($ri_pagesize);
        }
        else {
            // invalid pagesize, so don't page
            $paging = false;
        }

        if ($paging) {
            $offset = ($ri_pagenum - 1) * $ri_pagesize;
            $limitStatement = "LIMIT {$offset},{$ri_pagesize}";
        }

    }
    


    


//************** SELECT ******************/


    if (!isset($db)) {
        require('connect.php');
    }
    // Perform the select
    $records = "SELECT  RecordID, Name AS JobsiteName, Date, Hours, StartOdometer, EndOdometer, Comments, 
                        CONCAT(FirstName,' ', LastName) AS FullName
                    FROM Employeerecords 
                    JOIN Jobsites ON Jobsite = JobsiteId
                    JOIN Users ON Employeerecords.UserID = Users.UserID
                    {$whereStatement}
                    ORDER BY {$sortBy}
                    {$limitStatement}";

    

    $records = $db->prepare($records);
    // if successfully executed
    if ($records->execute()) {
        // If we are paging the results, get the total count
        if ($paging) {
            $count = "SELECT  COUNT(*) AS RowCount
                    FROM Employeerecords 
                    JOIN Jobsites ON Jobsite = JobsiteId
                    JOIN Users ON Employeerecords.UserID = Users.UserID
                    {$whereStatement}";

            $count = $db->prepare($count);
            if ($count->execute()) {
                // the page count will used to display page # out of # 
                $ri_pagecount = ceil( $count->fetch()['RowCount'] / $ri_pagesize );
                
            }
            else{
                $errormessage = 'An error occurred while counting records.';
            }
        }
    }
    else{
        $errormessage = 'An error occurred while loading records.';
    }

?>
 

<?php if($errormessage): ?>
    <div class="container alert alert-danger" role="alert">
        <h4 class="alert-heading">Error</h4>
        <p><?= $errormessage ?></p>
    </div>
<?php elseif($records->rowCount() > 0): ?>
    <?php if($ri_email): ?>
        <!-- If being sent as an email -->
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <style type="text/css">
                .table {
                    width: 100%;
                    margin-bottom: 1rem;
                    color: #212529;
                }

                .table th,
                .table td {
                    padding: 0.75rem;
                    vertical-align: top;
                    border-top: 1px solid #dee2e6;
                }

                .table thead th {
                    vertical-align: bottom;
                    border-bottom: 2px solid #dee2e6;
                }

                .table tbody + tbody {
                    border-top: 2px solid #dee2e6;
                }

                .table-bordered {
                    border: 1px solid #dee2e6;
                }

                .table-bordered th,
                .table-bordered td {
                    border: 1px solid #dee2e6;
                }

                .table-bordered thead th,
                .table-bordered thead td {
                    border-bottom-width: 2px;
                }
            </style>
        </head>
        <body>
            <table class="table table-bordered">   
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Hours</th>
                        <th>Start Odometer</th>
                        <th>End Odometer</th>
                        <th>Jobsite</th>
                        <th>Comments</th>
                    </tr>
                </thead> 
                <tbody>
                    <?php while($row = $records->fetch()): ?>
                        <tr>
                            <td><?= $row['FullName']?></td>
                            <td><?= $row['Date'] ?></td>
                            <td><?= $row['Hours'] ?></td>
                            <td><?= $row['StartOdometer'] ?></td>
                            <td><?= $row['EndOdometer'] ?></td>
                            <td><?= $row['JobsiteName'] ?></td>
                            <td><div class="max-height-60 overflow-auto"><?= html_entity_decode($row['Comments'],ENT_NOQUOTES) ?></div></td>
                        </tr>
                    <?php endwhile ?>                  
                </tbody>                     
            </table>
        </body>
        </html>
    
    <?php else: ?>

        <div class="table-responsive">
            <table class="table table-striped">   
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Hours</th>
                        <th>Start Odometer</th>
                        <th>End Odometer</th>
                        <th>Jobsite</th>
                        <th>Comments</th>
                    </tr>
                </thead> 
                <tbody>
                    <?php while($row = $records->fetch()): ?>
                        <tr>
                            <td><?= $row['FullName']?></td>
                            <td><?= $row['Date'] ?></td>
                            <td><?= $row['Hours'] ?></td>
                            <td><?= $row['StartOdometer'] ?></td>
                            <td><?= $row['EndOdometer'] ?></td>
                            <td><?= $row['JobsiteName'] ?></td>
                            <td><div class="max-height-60 overflow-auto"><?= html_entity_decode($row['Comments'],ENT_NOQUOTES) ?></div></td>
                        </tr>
                    <?php endwhile ?>                  
                </tbody>    
                        
            </table>
        </div>

    <?php endif ?>
<?php else: ?>
    <div class="container alert alert-danger" role="alert">
        <h4 class="alert-heading">No Results</h4>
        <p>No records were found matching those criteria.</p>
    </div>
<?php endif ?>

