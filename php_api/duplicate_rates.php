<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';

    $from_month = $_POST['from_month'];
    $from_year = $_POST['from_year'];
    $to_month = $_POST['to_month'];
    $to_year = $_POST['to_year'];

    $sql = "EXEC DuplicateRateFromDate :FromDate, :ToDate";
    $stmt = $conn -> prepare($sql);

    $from_date = $from_year . "-" . $from_month . "-01";
    $to_date = $to_year . "-" . $to_month . "-01";
    $stmt -> bindParam(":FromDate", $from_date);
    $stmt -> bindParam(":ToDate", $to_date);
    $stmt -> execute();

    $sql_add_unique_id = "UPDATE m_billing_compute set billing_compute_ref = :billing_compute_ref where id = :id";
    $stmt_add_unique_id = $conn -> prepare($sql_add_unique_id);
    while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        if ($data['billing_compute_ref'] == null) {
            $billing_compute_ref = uniqid("billing_m", true);
            $id = $data['id'];
            $stmt_add_unique_id -> bindParam(":billing_compute_ref", $billing_compute_ref);
            $stmt_add_unique_id -> bindParam(":id", $id);
            $stmt_add_unique_id -> execute();
        }
    }
    $notification = [
        "icon" => "success",
        "text" => "Data Updated",
    ];
    $_SESSION['notification'] = json_encode($notification);
    header('location: ../pages/add_billing.php');
    exit();