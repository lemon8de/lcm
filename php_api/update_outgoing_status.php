<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';
    $outgoing_details_ref = $_POST['outgoing_details_ref'];
    $status = $_POST['status'];
    $co_status = isset($_POST['co_status']) ? $_POST['co_status'] : 'N/A';
    

    $sql = "SELECT * from m_outgoing_status_details where outgoing_details_ref = :outgoing_details_ref";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
    $stmt -> execute();
    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

    $compare_set = array($outgoing_details_ref, $status, $co_status); 
    if ($shipment) {
        unset($shipment['id']);
        $shipment_keys = array_keys($shipment);
        $shipment_values = array_values($shipment);
    }

    $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to, username) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to, :username)";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":username", $_SESSION['username']);
    $stmt -> bindValue(':shipment_details_ref', $outgoing_details_ref);
    $stmt -> bindValue(':table_name', 'm_outgoing_status_details');

    for ($i = 0; $i < count($shipment_keys); $i++) {
        if ($compare_set[$i] !== $shipment_values[$i]) {
            //insert into changes table
            $stmt -> bindParam(':column_name', $shipment_keys[$i]);
            $stmt -> bindParam(':changed_from', $shipment_values[$i]);
            $stmt -> bindParam(':changed_to', $compare_set[$i]);
            $stmt -> execute();
        }
    }
    $sql = "UPDATE m_outgoing_status_details set status = ?, co_status = ? where outgoing_details_ref = ?";
    $stmt = $conn -> prepare($sql);
    $stmt -> execute([$status, $co_status, $outgoing_details_ref]);

    $conn = null;
    //header('location: ../pages/incoming_sea.php');
    //exit();
    $notification = [
        "icon" => "success",
        "text" => "Details Updated",
    ];
    $return_body = [];
    $return_body['notification'] = $notification;

    echo json_encode($return_body);