<?php
date_default_timezone_set('Asia/Manila');
//$servername = '172.25.112.131, 1433\SQLEXPRESS'; $username = 'SA'; $password = 'SystemGroup2018';
$servername = '172.25.112.100,49153'; 
$username = 'sa'; 
$password = 'YourStrong!Password';
$database = 'logistic_cost_management';

$currentDateTime = date('Y-m-d H:i:s');

echo <<<HTML
    username: $username<br>
    password: $password<br>
    server name: $servername<br>
    database name: $database<br>
HTML;

try {
    $conn = new PDO ("sqlsrv:Server=$servername;Database=$database",$username,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "[$currentDateTime]: Database Connection Established\n";
} catch (PDOException $e) {
    echo "[$currentDateTime]: Database Connection Failure\n";
    echo $e->getMessage();
}