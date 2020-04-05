<!-- 
    Connects to the database
    Author: Jeremy Grift
    Created: March 5, 2020
    Last Updated: March 5, 2020
 -->

<?php 
    $DB_DSN = 'mysql:host=localhost;dbname=final_project;charset=utf8';
    $DB_USER = 'final_project';
    $DB_PASS = 'J9541r9541';
    $db;   
    try {
    	$db = new PDO($DB_DSN, $DB_USER, $DB_PASS);

    } catch (PDOException $e) {
    	print('Error: ' . $e->getMessage());
    	die();
    }
?>