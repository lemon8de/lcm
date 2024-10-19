<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';

    $shipment_details_ref = $_POST['shipment_details_ref'];

    $tentative_delivery_schedule = $_POST['tentative_delivery_schedule'] == "" ? null : $_POST['tentative_delivery_schedule'];
    $required_delivery = $_POST['required_delivery'] == "" ? null : $_POST['required_delivery'];
    $actual_date_of_delivery = $_POST['actual_date_of_delivery'] == "" ? null : $_POST['actual_date_of_delivery'];

    $time_received = $_POST['time_received'];
    $received_by = $_POST['received_by'];

    $sql = "SELECT tentative_delivery_schedule, required_delivery, actual_date_of_delivery, time_received, received_by from t_air_delivery_details where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":shipment_details_ref", $shipment_details_ref);
    $stmt -> execute();

    $compare_set = array($tentative_delivery_schedule, $required_delivery, $actual_date_of_delivery, $time_received, $received_by);
    if ($shipment = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $shipment_keys = array_keys($shipment);
        $shipment_values = array_values($shipment);
    }

    $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to, username) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to, :username)";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":username", $_SESSION['username']);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> bindValue(':table_name', 't_air_delivery_details');

    for ($i = 0; $i < count($shipment_keys); $i++) {
        if ($compare_set[$i] !== $shipment_values[$i]) {
            //insert into changes table
            $stmt -> bindParam(':column_name', $shipment_keys[$i]);
            $stmt -> bindParam(':changed_from', $shipment_values[$i]);
            $stmt -> bindParam(':changed_to', $compare_set[$i]);
            $stmt -> execute();
        }
    }

    $sql = "UPDATE t_air_delivery_details set tentative_delivery_schedule = :tentative_delivery_schedule, required_delivery = :required_delivery, actual_date_of_delivery = :actual_date_of_delivery, time_received = :time_received, received_by = :received_by where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(':tentative_delivery_schedule', $tentative_delivery_schedule);
    $stmt -> bindParam(':required_delivery', $required_delivery);
    $stmt -> bindParam(':actual_date_of_delivery', $actual_date_of_delivery);
    $stmt -> bindParam(':time_received', $time_received);
    $stmt -> bindParam(':received_by', $received_by);
    $stmt -> bindParam(':shipment_details_ref', $shipment_details_ref);
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