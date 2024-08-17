<?php
    require 'db_connection.php';

    $shipment_details_ref = $_POST['shipment_details_ref'];
    $container_status = $_POST['container_status'];
    $date_return_reused = $_POST['date_return_reused'] == "" ? null : $_POST['date_return_reused'];
    $no_days_port = $_POST['no_days_port'];
    $no_days_falp = $_POST['no_days_falp'];

    //update block for updating the main table
    $sql = "SELECT container_status, date_return_reused, no_days_port, no_days_falp from m_mmsystem where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();
    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

    $compare_set = array($container_status, $date_return_reused, $no_days_port, $no_days_falp);
    if ($shipment) {
        $shipment_keys = array_keys($shipment);
        $shipment_values = array_values($shipment);
        $shipment_values[1] = $shipment_values[1] == null ? null : substr($shipment_values[1], 0, 10);
    }

    // compare_set, shipment_keys and shipment_values all have the same length and data format now, we compare
    $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> bindValue(':table_name', 'm_mmsystem');

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
    $sql = "UPDATE m_mmsystem set container_status = :container_status, date_return_reused = :date_return_reused, no_days_port = :no_days_port, no_days_falp = :no_days_falp where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':container_status', $container_status);
    $stmt -> bindValue(':date_return_reused', $date_return_reused);
    $stmt -> bindValue(':no_days_port', $no_days_port);
    $stmt -> bindValue(':no_days_falp', $no_days_falp);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    $conn = null;
    header('location: ../pages/incoming_sea.php');
    exit();