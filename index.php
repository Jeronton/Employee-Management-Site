<!-- 
    The welcome/login page of the site.
    Author: Jeremy Grift
    Created: March 4, 2020
    Last Updated: March 4, 2020
 -->

 <?php
    // the title to be used by header.php
    $title = 'Welcome!';

    require("connect.php");

    $users = $db->prepare("SELECT * FROM Users");
    $users->execute();

    $jobsites = $db->prepare("SELECT * FROM Jobsites");
    $jobsites->execute();

    $records = $db->prepare("SELECT * FROM EmployeeRecords");
    $records->execute();
 ?>

<?php include("header.php") ?>
    <div class="container bg-light ">
        <h1>Users Table</h1>
        <table>
            <thead>
                <th>Username</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Jobsite</th>
                <th>Password</th>
                <th>User Type</th>
            </thead>
            <tbody>
                <?php while( $row = $users->fetch()): ?>
                    <tr>
                        <td><?= $row["Username"] ?></td>
                        <td><?= $row["FirstName"] ?></td>
                        <td><?= $row["LastName"] ?></td>
                        <td><?= $row["CurrentJobsite"] ?></td>
                        <td><?= $row["Password"] ?></td>
                        <td><?= $row["UserType"] ?></td>
                    </tr>
                <?php endwhile ?>
            </tbody>
        </table>
        
        <h1>Jobsites Table</h1>
        <table>
            <thead>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Address</th>
                <th>IsActive</th>
            </thead>
            <tbody>
                <?php while( $row = $jobsites->fetch()): ?>
                    <tr>
                        <td><?= $row["JobsiteID"] ?></td>
                        <td><?= $row["Name"] ?></td>
                        <td><?= $row["Description"] ?></td>
                        <td><?= $row["Address"] ?></td>
                        <td><?= $row["IsActive"] ?></td>
                    </tr>
                <?php endwhile ?>
            </tbody>
        </table>

        <h1>Employee Records Table</h1>
        <table>
            <thead>
                <th>ID</th>
                <th>UserID</th>
                <th>JobsiteID</th>
                <th>Date</th>
                <th>Hours</th>
                <th>KMs</th>
                <th>Comments</th>
            </thead>
            <tbody>
                <?php while( $row = $records->fetch()): ?>
                    <tr>
                        <td><?= $row["RecordID"] ?></td>
                        <td><?= $row["UserID"] ?></td>
                        <td><?= $row["Jobsite"] ?></td>
                        <td><?= $row["Date"] ?></td>
                        <td><?= $row["Hours"] ?></td>
                        <td><?= $row["KMs"] ?></td>
                        <td><?= $row["Comments"] ?></td>
                    </tr>
                <?php endwhile ?>
            </tbody>
        </table>
    </div>
</body>
</html>