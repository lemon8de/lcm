<?php
require 'db_connection.php';

$sql = "SELECT * from active_report";
$stmt = $conn -> prepare($sql);
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
    $adf= ' asdf';
    fputcsv($output, $row);
}

fclose($output);