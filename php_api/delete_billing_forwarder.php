<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';

    $billing_forwarder_details_ref = $_POST['billing_forwarder_details_ref'];

    $sql = "SELECT forwarder_logo from m_billing_forwarder where billing_forwarder_details_ref = :billing_forwarder_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":billing_forwarder_details_ref", $billing_forwarder_details_ref);
    $stmt -> execute();
    if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $filePath = $data['forwarder_logo'];
        // Check if the file exists
        if (file_exists($filePath)) {
            // Attempt to delete the file
            unlink($filePath);
        }
    }

    $sql = "DELETE from m_billing_forwarder where billing_forwarder_details_ref = :billing_forwarder_details_ref;
            DELETE from m_billing_compute where billing_forwarder_details_ref = :billing_forwarder_details_ref2;
    ";

    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":billing_forwarder_details_ref", $billing_forwarder_details_ref);
    $stmt -> bindParam(":billing_forwarder_details_ref2", $billing_forwarder_details_ref);
    $stmt -> execute();
    $notification = [
        "icon" => "success",
        "text" => "Forwarder and its Rates Deleted",
    ];
    $_SESSION['notification'] = json_encode($notification);
    header('location: ../pages/edit_billing_forwarder.php');
    exit();