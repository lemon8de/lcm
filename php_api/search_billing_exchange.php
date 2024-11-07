<?php
    require 'db_connection.php';

    $month = $_GET['month'];
    $year = $_GET['year'];

    $for_date = new DateTime();
    $for_date -> setDate($year, $month, 1);
    $for_date = $for_date -> format('Y-m-d');

    $sql = "SELECT jpy_php, usd_php, jpy_usd from t_billing_exchange where for_date = :for_date";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":for_date", $for_date);
    $stmt -> execute();

    if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $response_body = [
            'jpy_php' => $data['jpy_php'],
            'usd_php' => $data['usd_php'],
            'jpy_usd' => $data['jpy_usd'],
        ];
    } else {
        $response_body = [
            'jpy_php' => 0,
            'usd_php' => 0,
            'jpy_usd' => 0,
        ];
    }
    echo json_encode($response_body);
    exit();