<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';

    $billing_details_ref = $_POST['billing_details_ref'];

    $sql = "DELETE from m_billing_information where billing_details_ref = :billing_details_ref;
            DELETE from m_billing_compute where billing_details_ref = :billing_details_ref2;
    ";

    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":billing_details_ref", $billing_details_ref);
    $stmt -> bindParam(":billing_details_ref2", $billing_details_ref);
    $stmt -> execute();
    $notification = [
        "icon" => "success",
        "text" => "Details of Charge and its Rates Deleted",
    ];
    $_SESSION['notification'] = json_encode($notification);
    header('location: ../pages/details_of_charge.php');
    exit();