<?php
require 'db_connection.php';

$show_active = isset($_POST['show_active']) ? $_POST['show_active'] : 'off';
$month = $_POST['month'];
$year = $_POST['year'];

if ($show_active == "false" && ($year == "" || $month == "")) {
    exit();
}

$sql = "EXEC ActiveAirReport :ActiveOnly, :StartYear, :StartMonth";
$stmt = $conn -> prepare($sql);
$stmt -> bindParam(":ActiveOnly", $show_active);
$stmt -> bindParam(":StartYear", $year);
$stmt -> bindParam(":StartMonth", $month);
$stmt -> execute();

$headers = "";
$csv_data = [];
while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    if ($headers == "") {
        $headers = array_keys($data);
    }
    array_push($csv_data, array_values($data));
}

header('Content-Type: text/csv');
$output = fopen('php://output', 'w');

fputcsv($output, $headers);
foreach ($csv_data as $row) {
    fputcsv($output, $row);
}

fclose($output);