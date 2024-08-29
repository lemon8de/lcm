<?php
    require 'db_connection.php';

    $shipment_details_ref = $_POST['shipment_details_ref'];
    $polytainer_size = $_POST['polytainer_size'];
    $polytainer_quantity = $_POST['polytainer_quantity'];
    $etd = $_POST['etd'] == "" ? null : $_POST['etd'];

    //update block for updating the main table
    $sql = "SELECT polytainer_size, polytainer_quantity, etd from m_polytainer_details where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();
    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

    $compare_set = array($polytainer_size, $polytainer_quantity, $etd);
    if ($shipment) {
        $shipment_keys = array_keys($shipment);
        $shipment_values = array_values($shipment);
        $shipment_values[2] = $shipment_values[2] == null ? null : substr($shipment_values[2], 0, 10);
    }

    // compare_set, shipment_keys and shipment_values all have the same length and data format now, we compare
    $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> bindValue(':table_name', 'm_polytainer_details');

    for ($i = 0; $i < count($shipment_keys); $i++) {
        if ($compare_set[$i] !== $shipment_values[$i]) {
            //insert into changes table
            $stmt -> bindParam(':column_name', $shipment_keys[$i]);
            $stmt -> bindParam(':changed_from', $shipment_values[$i]);
            $stmt -> bindParam(':changed_to', $compare_set[$i]);
            $stmt -> execute();
        }
    }

    $sql = "UPDATE m_polytainer_details set polytainer_size = :polytainer_size, polytainer_quantity = :polytainer_quantity, etd = :etd where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':polytainer_size', $polytainer_size);
    $stmt -> bindValue(':polytainer_quantity', $polytainer_quantity);
    $stmt -> bindValue(':etd', $etd);
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