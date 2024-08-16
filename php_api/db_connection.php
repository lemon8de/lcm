<?php
//database
date_default_timezone_set('Asia/Manila');
$servername = '172.25.112.100,49153'; 
$username = 'sa'; 
$password = 'YourStrong!Password';
$database = 'logistic_cost_management';
try {
    $conn = new PDO ("sqlsrv:Server=$servername;Database=$database",$username,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'NO CONNECTION'.$e->getMessage();
}
//end database
?>