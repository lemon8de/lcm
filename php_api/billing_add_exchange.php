<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';

    $month = $_POST['month'];
    $year = $_POST['year'];
    $jpy_php = $_POST['jpy_php'];
    $usd_php = $_POST['usd_php'];
    $jpy_usd = $_POST['jpy_usd'];

    $sql = "
    MERGE INTO t_billing_exchange AS target
    USING (VALUES(:jpy_php, :usd_php, :jpy_usd, :for_date)) AS source (jpy_php, usd_php, jpy_usd, for_date)
    ON target.for_date = source.for_date
    WHEN MATCHED THEN
        UPDATE SET 
            target.jpy_php = source.jpy_php,
            target.usd_php = source.usd_php,
            target.jpy_usd = source.jpy_usd
    WHEN NOT MATCHED THEN
        INSERT (jpy_php, usd_php, jpy_usd, for_date)
        VALUES (source.jpy_php, source.usd_php, source.jpy_usd, source.for_date);
    ";

    $for_date = new DateTime();
    $for_date -> setDate($year, $month, 1);
    $for_date = $for_date -> format('Y-m-d');

    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":jpy_php", $jpy_php);
    $stmt -> bindParam(":usd_php", $usd_php);
    $stmt -> bindParam(":jpy_usd", $jpy_usd);
    $stmt -> bindParam(":for_date", $for_date);
    $stmt -> execute();

    $notification = [
        "icon" => "success",
        "text" => "Details Updated",
    ];
    $_SESSION['notification'] = json_encode($notification);
    header('location: ../pages/add_currency.php');
    exit();