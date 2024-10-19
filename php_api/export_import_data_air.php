<?php
require 'db_connection.php';

    $start_year = $_POST['year'];
    $start_month = $_POST['month'] ?? "";
    $remove_active = isset($_POST['remove_active']) ? $_POST['remove_active'] : 'off';
    if ($start_year == "" || $start_month == "") {
        echo json_encode(['exited' => true]);
        exit();
    }
    $sql = "EXEC AirImportReport :RemoveActive, :StartYear, :StartMonth";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":RemoveActive", $remove_active);
    $stmt -> bindParam(":StartYear", $start_year);
    $stmt -> bindParam(":StartMonth", $start_month);
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