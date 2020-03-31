<!-- 
    Views the records of the individual user that is logged on.
    Author: Jeremy Grift
    Created: March 23, 2020
    Last Updated: March 23, 2020
 -->
 <?php 
    // If session is not started, start.
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // user must be logged, on but it doesn't matter what type of user they are, if they don't have any records then none will display.
    require('authenticate.php');

    if (!isset($db)) {
        require('connect.php');
    }

    $title = "{$_SESSION['firstname']}'s Records";

    $errormessage = '';

    $sortby = 'Date DESC';
    $sortvalue = 'Date';

    if (!empty($_GET['sortby'])) {
        switch (filter_input(INPUT_GET, 'sortby', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
            case 'date':
                $sortby = 'Date DESC';
                $sortvalue = 'Date';
                break;

            case 'jobsite':
                $sortby = 'Jobsite DESC';
                $sortvalue = 'Jobsite';
                break;
            
            case 'odometer':
                $sortby = 'StartOdometer DESC';
                $sortvalue = 'Odometer';
                break;

            default: 
                $sortby = 'Date DESC';
                $sortvalue = 'Date';
                break;
        }
    }

    $records = "SELECT RecordID, Name AS JobsiteName, Date, Hours, StartOdometer, EndOdometer, Comments 
                    FROM employeerecords 
                    JOIN Jobsites ON Jobsite = JobsiteId
                    WHERE UserID = :userid
                    ORDER BY {$sortby}";
    $records = $db->prepare($records);
    $records ->bindValue(':userid', $_SESSION['userid'], PDO::PARAM_INT);
    if (!$records->execute()) {
        $errormessage = 'An error occurred while loading records.';
    }
 ?>
 
 <?php include('header.php') ?>
    <div class="container">
        <?php if($errormessage):?>

            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Error</h4>
                <p><?= $errormessage ?></p>
                <hr>
                <a href="home.php" class="btn btn-primary mb-2">Return to home page</a>
            </div>

        <?php elseif($records->rowCount() == 0): ?>

            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">No Results</h4>
                <p>No records found.</p>
                <hr>
                <a href="home.php" class="btn btn-primary mb-2">Return to home page</a>
            </div>

        <?php else: ?>  
            <div class="container d-flex justify-content-end">
                <div class="dropdown mb-2">
                    <label for="dropdownSortByButton" class="form-label mr-2">Sort By:  </label>
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownSortByButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= $sortvalue ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownSortByButton">
                        <a class="dropdown-item" href="viewusersrecords.php?sortby=date">Date</a>
                        <a class="dropdown-item" href="viewusersrecords.php?sortby=jobsite">Jobsite</a>
                        <a class="dropdown-item" href="viewusersrecords.php?sortby=odometer">Odometer</a>
                    </div>
                </div>
            </div>

            <div class="container table-responsive">
                <table class="table table-striped">   
                    <thead>
                        <th>Date</th>
                        <th>Hours</th>
                        <th>Start Odometer</th>
                        <th>End Odometer</th>
                        <th>Jobsite</th>
                        <th>Comments</th>
                        <th></th>
                        <th></th>
                    </thead> 
                    <tbody>
                        <?php while($row = $records->fetch()): ?>
                            <tr>
                                <td><?= $row['Date'] ?></td>
                                <td><?= $row['Hours'] ?></td>
                                <td><?= $row['StartOdometer'] ?></td>
                                <td><?= $row['EndOdometer'] ?></td>
                                <td><?= $row['JobsiteName'] ?></td>
                                <td> <iframe srcdoc="<?= html_entity_decode($row['Comments'],ENT_NOQUOTES) ?>" class="commentsiframe"></iframe></td>
                                <td><a href="editrecord.php?recordid=<?= $row['RecordID'] ?>">edit</a></td>
                                <td><a href="deleterecord.php?recordid=<?= $row['RecordID'] ?>">delete</a></td>
                            </tr>
                        <?php endwhile ?>                  
                    </tbody>    
                            
                </table>
            </div>
        <?php endif ?>
    </div>
</body>
</html>