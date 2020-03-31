<!-- 
    Description
    Author: Jeremy Grift
    Created: March 24, 2020
    Last Updated: March 24, 2020
 -->
 <?php 
    session_start();
    $authusertype = 'admin';
    require('authenticate.php');

    require('connect.php');

    $query = "SELECT u.UserID, SUBSTRING(CONCAT(u.FirstName, ' ', u.LastName),1,30) AS FullName, u.Username, u.UserType AS Type, SUBSTRING(j.Name,1,30) AS Jobsite
                FROM Users u
                LEFT JOIN Jobsites j ON u.CurrentJobsite = j.JobsiteID";
    $users = $db->prepare($query);
    $users->execute();

    
    $title;
 ?>
 
 <?php include('header.php') ?>
    <div class="container table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Username</th>
                    <th scope="col">Type</th>
                    <th scope="col">Current Jobsite</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while( $row = $users->fetch()): ?>
                    <tr>
                        <td><?= $row['FullName'] ?></td>
                        <td><?= $row['Username'] ?></td>
                        <td><?= $row['Type'] ?></td>
                        <td><?= $row['Jobsite'] ?></td>
                        <td><a href="edituser.php?id=<?= $row['UserID'] ?>">edit</a></td>
                        <td><a href="deleteuser.php?id=<?= $row['UserID'] ?>">delete</a></td>
                    </tr>
                <?php endwhile ?>
            </tbody>
        </table>
    </div>
</body>
