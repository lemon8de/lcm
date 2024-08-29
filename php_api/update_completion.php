<?php
    require 'db_connection.php';

    $shipment_details_ref = $_POST['shipment_details_ref'];
    $date_port_out = $_POST['date_port_out'] == "" ? null : $_POST['date_port_out'];
    $actual_received_at_falp = $_POST['actual_received_at_falp'] == "" ? null : $_POST['actual_received_at_falp'];

    //update block for updating the main table
    $sql = "SELECT date_port_out, actual_received_at_falp from m_completion_details where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();
    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

    $compare_set = array($date_port_out, $actual_received_at_falp);
    if ($shipment) {
        $shipment_keys = array_keys($shipment);
        $shipment_values = array_values($shipment);
        $shipment_values[0] = $shipment_values[0] == null ? null : substr($shipment_values[0], 0, 10);
        $shipment_values[1] = $shipment_values[1] == null ? null : substr($shipment_values[1], 0, 10);
    }

    // compare_set, shipment_keys and shipment_values all have the same length and data format now, we compare
    $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> bindValue(':table_name', 'm_completion_details');

    for ($i = 0; $i < count($shipment_keys); $i++) {
        if ($compare_set[$i] !== $shipment_values[$i]) {
            //insert into changes table
            $stmt -> bindParam(':column_name', $shipment_keys[$i]);
            $stmt -> bindParam(':changed_from', $shipment_values[$i]);
            $stmt -> bindParam(':changed_to', $compare_set[$i]);
            $stmt -> execute();
        }
    }
    //finally update the m_shipment_sea_details table
    $sql = "UPDATE m_completion_details set date_port_out = :date_port_out, actual_received_at_falp = :actual_received_at_falp where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':date_port_out', $date_port_out);
    $stmt -> bindValue(':actual_received_at_falp', $actual_received_at_falp);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

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