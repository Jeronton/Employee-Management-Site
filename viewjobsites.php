<?php 
/*
   Description
   Author: Jeremy Grift
   Created: April 14, 2020
*/

   // If session is not started, start.
   if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
   }

   $authusertype = 'admin';
    require('authenticate.php');

   include('utilities.php');

   $title = 'Jobsites';

   $jobsites = GetJobsites();


?>
 
<?php include('header.php') ?>
    <div class="container">
        <a href="addjobsite.php" class="btn btn-primary mb-2">Add A Jobsite</a>
    </div>
    <div class="container table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <!-- <th>Address</th> -->
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while( $row = $jobsites->fetch()): ?>
                    <tr>
                        <td><?= $row['Name'] ?></td>
                        <td><?= $row['Description'] ?></td>
                        <!-- <td><?= $row['Address'] ?></td> -->
                        <td>
                            <?php
                                switch ($row['IsActive']) {
                                    case '0':
                                        echo 'No';
                                        break;
                                    case '1':
                                        echo 'Yes';
                                        break;
                                } 
                            ?>
                         </td>
                        <td><a href="editjobsite.php?jobsiteid=<?= $row['JobsiteID'] ?>">edit</a></td>
                    </tr>
                <?php endwhile ?>
            </tbody>
        </table>
    </div>
</body>
</html>