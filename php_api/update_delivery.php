<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';

    $required_delivery_sched = $_POST['required_delivery_sched'] == "" ? null : $_POST['required_delivery_sched'];
    $shipment_details_ref = $_POST['shipment_details_ref'];
    $deliver_plan = $_POST['deliver_plan'] == "" ? null : $_POST['deliver_plan'];
    $tabs = $_POST['tabs'] == '' ? null : $_POST['tabs'];

    //update block for updating the main table
    $sql = "SELECT required_delivery_sched, deliver_plan, tabs from m_delivery_plan where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();
    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

    $compare_set = array($required_delivery_sched, $deliver_plan, $tabs);
    if ($shipment) {
        $shipment_keys = array_keys($shipment);
        $shipment_values = array_values($shipment);
        $shipment_values[0] = $shipment_values[0] == null ? null : substr($shipment_values[0], 0, 10);
        $shipment_values[1] = $shipment_values[1] == null ? null : substr($shipment_values[1], 0, 10);
    }

    // compare_set, shipment_keys and shipment_values all have the same length and data format now, we compare
    $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to, username) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to, :username)";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":username", $_SESSION['username']);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> bindValue(':table_name', 'm_delivery_plan');

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
    $sql = "UPDATE m_delivery_plan set required_delivery_sched = :required_delivery_sched, deliver_plan = :deliver_plan, tabs = :tabs where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':required_delivery_sched', $required_delivery_sched);
    $stmt -> bindValue(':deliver_plan', $deliver_plan);
    $stmt -> bindValue(':tabs', $tabs);
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