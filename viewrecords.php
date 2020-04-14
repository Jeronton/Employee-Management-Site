<?php 
/*
   Page for an accountant to view and email any records in the database
   Author: Jeremy Grift
   Created: April 7, 2020
*/


    // If session is not started, start.
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $authusertype = 'accountant';
    require('authenticate.php');

    require('utilities.php');
    if (!isset($db)) {
        require('connect.php');
    }

    $title = 'View Records';

    // load jobsites
    $jobsites = GetJobsites();

    // load users
    $query = "SELECT UserID, SUBSTRING(CONCAT(FirstName, ' ', LastName),1,30) AS FullName
                FROM Users
                LEFT JOIN Jobsites ON CurrentJobsite = JobsiteID";
    $users = $db->prepare($query);
    $users->execute();


    // The values to pass to record insert
    $ri_confirm = true;

    //$ri_email = true;
    // $ri_startdate = '';
    // $ri_enddate = '';

    // update the page number from get
    if (filter_input(INPUT_GET, 'pagenum', FILTER_VALIDATE_INT)) {
        $ri_pagenum = $_GET['pagenum'];
    }
    else{
        // or default to 1
        $ri_pagenum = 1;
    }

    if (empty($ri_pagesize)) {
        $ri_pagesize = 20;
    }


    /******** VALIDATION ********/

    //Error messages
    $startdateerror = '';
    $enddateerror = '';
    $jobsiteserror = '';
    $pagesizeerror = '';
    $employeeserror = '';
    $sortbyerror = '';

    $valid = true;

    if (isset($_POST['postback'])) {
        // Reset page number on postback, as a post only occurs when the filter is being changed, and thus the results should be reset
        $ri_pagenum = 1;

        $_SESSION['pagesize'] = null; 
        $_SESSION['search'] = null;
        $_SESSION['sortby'] = null;
        $_SESSION['employees'] = null;
        $_SESSION['jobsites'] = null;
        $_SESSION['startdate'] = null;
        $_SESSION['enddate'] = null;
    }

        // DATE

        if (empty($_POST['startdate'])) {
            // not required so do nothing
        }
        elseif ( ! validateDate($_POST['startdate'])) {
            $valid = false;
            $startdateerror = '*Invalid date, try this format: YYYY-MM-DD';
        }
        else{
            // is valid so set ri_startdate
            $_SESSION['startdate'] = $_POST['startdate'];
        }

        if (empty($_POST['enddate'])) {
            if (isset($ri_startdate)) {
                // start date is entered, but end date is not, so assume current date
                $ri_enddate = date('Y-m-d');
            }        
        }
        elseif (! isset($ri_startdate)) {
            // end date is entered, but no start date is entered, so error.
            $valid = false;
            $enddateerror = '*Must enter a start date';
        }
        elseif (! validateDate($_POST['startdate'])) {
            $valid = false;
            $enddateerror = '*Invalid date, try this format: YYYY-MM-DD';
        }
        else{
            // is valid so set ri_enddate
            $_SESSION['enddate'] = filter_input(INPUT_POST, 'enddate', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/")));
        }

        // JOBSITES

        if (!empty($_POST['jobsites'])) {

            $validjobsites = true;
            foreach ($_POST['jobsites'] as $site) {
                if (filter_var($site, FILTER_VALIDATE_INT) === false) {
                    // none numeric jobsite entered, so invalid
                    $validjobsites = false;
                    $valid = false;
                    $jobsiteserror = '*Invalid Jobsite(s)';
                    break;
                }    
                elseif ($site == -1) {
                    // is all jobsites, so don't set it as that will default to all jobsites
                    $validjobsites = false;
                    break;
                }
            }

            if ($validjobsites) {
                $_SESSION['jobsites'] = $_POST['jobsites'];
            }
        }

        // EMPLOYEES

        if (!empty($_POST['employees'])) {
            $validemployees = true;
            foreach ($_POST['employees'] as $employee) {
                if (filter_var($employee, FILTER_VALIDATE_INT) === false) {
                    // none numeric jobemployee entered, so invalid
                    $validemployees = false;
                    $valid = false;
                    $employeeserror = '*Invalid employee(s)';
                    break;
                }    
                elseif ($employee == -1) {
                    // is all employees, so don't set it as that will default to all employees.
                    $validemployees = false;
                    break;
                }
            }

            if ($validemployees) {
                $_SESSION['employees'] = $_POST['employees'];
            }
        }

        // SORT BY

        if (! empty($_POST['sortby']) ) {
            $_SESSION['sortby'] = filter_input(INPUT_POST, 'sortby', FILTER_SANITIZE_STRIPPED);
        }

        // SEARCH

        if(! empty($_POST['search'])){
            $_SESSION['search'] = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        // PAGINATION
        if (empty($_SESSION['pagesize'])) {
            // use session to store pagesize, so that it persists even after GET.
            $_SESSION['pagesize'] = 20;
        }

        if (filter_input(INPUT_POST, 'pagesize', FILTER_VALIDATE_INT) && $_POST['pagesize'] > 0 && $_POST['pagesize'] <=100) {
            $_SESSION['pagesize'] = $_POST['pagesize'];
        }
        elseif(!empty($_POST['pagesize'])){
            $valid = false;
            $pagesizeerror = 'Invalid page size, must between 1 and 100.';
        }


    // set the record insert parameters.
    if (isset($_SESSION['pagesize'])) {
        $ri_pagesize = $_SESSION['pagesize']; 
    }
    if (isset($_SESSION['search'])) {
        $ri_search = $_SESSION['search'];
    }
    if (isset($_SESSION['sortby'])) {
        $ri_sortby = $_SESSION['sortby'];
    }
    if (isset($_SESSION['employees'])) {
        $ri_employees = $_SESSION['employees'];
    }
    if (isset($_SESSION['jobsites'])) {
        $ri_jobsites = $_SESSION['jobsites'];
    }
    if (isset($_SESSION['startdate'])) {
        $ri_startdate = $_SESSION['startdate'];
    }
    if (isset($_SESSION['enddate'])) {
        $ri_enddate = $_SESSION['enddate'];
    }
    
    
    
    
    


?>
        
    <?php include('header.php') ?>
    <div class="container">
        <form method="POST" class="needs-validation" id="filterform" enctype="multipart/form-data" novalidate>
            
            <!-- Used to check wether is a postback or not -->
            <input type='hidden' name='postback' />

            <!-- Search -->
            <div class="mb-4 row">
                <div class="col-md-3"></div>
                <div class="flex-item input-group col-md-6">
                    <input type="text" class="form-control" id="search" name="search" placeholder="Search" aria-describedby="search" >
                    <div class="input-group-append">
                        <input type="button" id="searchbutton" class="btn btn-outline-secondary" onclick="this.form.submit()" value="Search">
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>

            
            <div class="d-md-flex justify-content-between">

                <!-- Email button -->
                <div class="flex-item order-2">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#emailmodal">
                        Email Records
                    </button>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="emailmodal" tabindex="-1" role="dialog" aria-labelledby="emailmodalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="emailmodalLabel">Send Email</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Email records to <?= $_SESSION['useremail'] ?>? Will email all records that match your filter settings, even records on another page.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <a href="emailrecords.php" class="flex-item btn btn-primary">Send</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sort by button -->
                <div class="flex-item form-group row order-3">
                    <label for="sortby" class="col-md-5 col-form-label">Sort By:</label>
                    <div class="col-md-7">
                        <select name="sortby" id="sortby" class="custom-select <?php if($sortbyerror){echo 'is-invalid';} ?>" onchange='this.form.submit()'>
                            <option value="date" <?php if(isset($ri_sortby) && $ri_sortby == 'date'){echo 'selected';} ?>>Date</option>
                            <option value="jobsite" <?php if(isset($ri_sortby) && $ri_sortby == 'jobsite'){echo 'selected';} ?>>Jobsite</option>
                            <option value="odometer" <?php if(isset($ri_sortby) && $ri_sortby == 'odometer'){echo 'selected';} ?>>Odometer</option>
                        </select>
                    </div>
                </div>

                <!-- Filter button -->
                <div class="flex-item order-1">
                    <button class="flex-item btn btn-primary" type="button" data-toggle="collapse" data-target="#filter_collapse" aria-expanded="false" aria-controls="filter_collapse">
                        Filter Options...
                    </button>
                </div>
            </div>

            <div class="collapse" id="filter_collapse">
                <div class="row">
                    <div id="left_col" class="col-md-6">  

                        <!-- jobsites -->
                        <div class="form-group row">
                            <label for="jobsites" class="col-lg-3 col-form-label">Jobsite(s):</label>
                            <div class="col-lg-9">
                                <select name="jobsites[]" id="jobsites" class="custom-select  <?php if($jobsiteserror){echo 'is-invalid';} ?>" multiple>
                                    <option value="-1" <?php if( empty($ri_jobsites)){echo 'selected';} ?>>All</option>
                                    <?php foreach ($jobsites as $jobsite): ?>        
                                        <option value="<?= $jobsite['JobsiteID'] ?>" <?php if(isset($ri_jobsites) && in_array($jobsite['JobsiteID'], $ri_jobsites)){echo 'selected';} ?> >
                                            <?= $jobsite['Name'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $jobsiteserror ?>
                                </div>
                            </div>
                        </div> 

                        <!-- Start Date -->
                        <div class="form-group row"> 
                            <label for="startdate" class="col-lg-3 col-form-label">StartDate:</label>
                            <div class="col-lg-9">
                                <input id="startdate" name="startdate" type="date" class="form-control <?php if($startdateerror){echo 'is-invalid';} ?>" value="<?php if(isset($ri_startdate)){echo $ri_startdate;} ?>">
                                <div class="invalid-feedback">
                                    <?= $startdateerror ?>
                                </div>
                            </div>
                        </div>

                        <!--End Date -->
                        <div class="form-group row"> 
                            <label for="enddate" class="col-lg-3 col-form-label">End Date:</label>
                            <div class="col-lg-9">
                                <input id="enddate" name="enddate" type="date" class="form-control <?php if($enddateerror){echo 'is-invalid';} ?>" value="<?php if(isset($ri_enddate)){echo $ri_enddate;} ?>">
                                <div class="invalid-feedback">
                                    <?= $enddateerror ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div id="right_col" class="col-md-6"> 

                        <!-- employees -->
                        <div class="form-group row">
                            <label for="employees" class="col-lg-3 col-form-label">Employee(s):</label>
                            <div class="col-lg-9">
                                <select name="employees[]" id="employees" class="custom-select  <?php if($employeeserror){echo 'is-invalid';} ?>" multiple>
                                <option value="-1" <?php if( empty($ri_employees)){echo 'selected';} ?>>All</option>
                                <?php while( $row = $users->fetch()): ?>
                                    <option value="<?= $row['UserID'] ?>" <?php if(isset($ri_employees) && in_array($row['UserID'], $ri_employees)){echo 'selected';} ?>>
                                    <?= $row['FullName'] ?>
                                </option>
                                <?php endwhile ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $employeeserror ?>
                                </div>
                            </div>
                        </div> 

                        <!-- Pagesize -->
                        <div class="form-group row"> 
                            <label for="pagesize" class="col-lg-3 col-form-label">Page Size:</label>
                            <div class="col-lg-9">
                                <input id="pagesize" name="pagesize" type="number" class="form-control <?php if($pagesizeerror){echo 'is-invalid';} ?>" value="<?php if(isset($ri_pagesize)){echo $ri_pagesize;} ?>">
                                <div class="invalid-feedback">
                                    <?= $pagesizeerror ?>
                                </div>
                            </div>
                        </div>

                    <div class="form-group row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-9">
                                <input class="btn btn-primary w-100 mb-2" type="submit" name="filter" value="Filter Records">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="container">
        <?php if($valid): ?>
            
            <!-- Inserts a table with all the records -->
            <?php include('recordsinsert.php'); ?>

            <!-- Pagination -->
            <!-- If more then one page of results, display page navigator -->
            <?php if(! empty($ri_pagecount) && $ri_pagecount > 1): ?>
                <nav class="d-flex justify-content-center">
                    <ul class="pagination">
                        <li class="page-item <?php if($ri_pagenum <= 1){ echo 'disabled'; } ?>">
                            <a href="<?php if($ri_pagenum <= 1){ echo ''; } else { echo "?pagenum=".($ri_pagenum - 1); } ?>" class="page-link">Prev</a>
                        </li>
                        <?php for ($i=1; $i <= $ri_pagecount; $i++): ?>
                            <li class="page-item <?php if(!empty($ri_pagenum) && $ri_pagenum == $i){echo 'active';} ?>">
                                <a class="page-link" href="?pagenum=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor ?>
                        <li class="page-item <?php if($ri_pagenum >= $ri_pagecount){ echo 'disabled'; } ?>">
                            <a href="<?php if($ri_pagenum >= $ri_pagecount){ echo ''; } else { echo "?pagenum=".($ri_pagenum + 1); } ?>" class="page-link">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif ?>

        <?php else: ?>
            <div class="container alert alert-danger" role="alert">
                <h4 class="alert-heading">Invalid Filter</h4>
                <p>Invalid filter criteria, correct and try again.</p>
            </div>
        <?php endif ?>
    </div>
    
</body>
</html>