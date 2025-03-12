<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';

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
    $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to, username) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to, :username)";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":username", $_SESSION['username']);
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
    $sql = "DECLARE @date_port_out DATETIME;
            DECLARE @actual_received_at_falp DATETIME;

            SET @date_port_out = :date_port_out;
            SET @actual_received_at_falp = :actual_received_at_falp;

            UPDATE m_completion_details 
            SET date_port_out = CASE 
                WHEN (date_port_out IS NULL AND (@date_port_out IS NULL AND @actual_received_at_falp IS NOT NULL)) 
                THEN @actual_received_at_falp 
                ELSE @date_port_out 
            END,
            actual_received_at_falp = @actual_received_at_falp 
            WHERE shipment_details_ref = :shipment_details_ref;
    ";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':date_port_out', $date_port_out);
    $stmt -> bindValue(':actual_received_at_falp', $actual_received_at_falp);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    //no of days updating, calculation of days at port
    //uses m_completion_details - date_port_out; m_vessel_details - atb
    $sql = "SELECT atb from m_vessel_details where shipment_details_ref = :shipment_details_ref";
    $stmt_check_days = $conn -> prepare($sql);
    $stmt_check_days -> bindParam(':shipment_details_ref', $shipment_details_ref);
    $stmt_check_days -> execute();
    if ($mm_detail = $stmt_check_days -> fetch(PDO::FETCH_ASSOC)) {
        //this if statement actually filters for confirmed shipments lol
        //notes that $atb might be null, the computation is atb - date port out + 1
        // Create DateTime objects
        $dateTime1 = $mm_detail['atb'] == null ? null : new DateTime($mm_detail['atb']);
        $dateTime2 = $date_port_out == null ? null : new DateTime($date_port_out);

        // Calculate the difference
        if ($dateTime1 and $dateTime2) {
            $interval = $dateTime1->diff($dateTime2);
            $differenceInDays = $interval->days + 1;
        } else {
            $differenceInDays = 0;
        }
        //insert this bad boy, fuck history we don't need that shit here
        $sql = "UPDATE m_mmsystem set no_days_port = :no_days_port where shipment_details_ref = :shipment_details_ref";
        $stmt_update_mm = $conn -> prepare($sql);
        $stmt_update_mm -> bindParam(':no_days_port', $differenceInDays);
        $stmt_update_mm -> bindParam(':shipment_details_ref', $shipment_details_ref);
        $stmt_update_mm -> execute();
    }
    //no of days updating, calculation of days at falp
    //uses m_completion_details - actual_received_at_falp; m_mmsystem - date_return_reused
    $sql = "SELECT date_return_reused from m_mmsystem where shipment_details_ref = :shipment_details_ref";
    $stmt_check_days = $conn -> prepare($sql);
    $stmt_check_days -> bindParam(':shipment_details_ref', $shipment_details_ref);
    $stmt_check_days -> execute();
    if ($mm_detail = $stmt_check_days -> fetch(PDO::FETCH_ASSOC)) {
        //this if statement actually filters for confirmed shipments lol
        //notes that $atb might be null, the computation is atb - date port out + 1
        // Create DateTime objects
        $dateTime1 = $actual_received_at_falp == null ? null : new DateTime($actual_received_at_falp);
        $dateTime2 = $mm_detail['date_return_reused'] == null ? null : new DateTime($mm_detail['date_return_reused']);

        // Calculate the difference
        if ($dateTime1 and $dateTime2) {
            $interval = $dateTime1->diff($dateTime2);
            $differenceInDays = $interval->days + 1;
        } else {
            $differenceInDays = 0;
        }
        //insert this bad boy, fuck history we don't need that shit here
        $sql = "UPDATE m_mmsystem set no_days_falp = :no_days_falp where shipment_details_ref = :shipment_details_ref";
        $stmt_update_mm = $conn -> prepare($sql);
        $stmt_update_mm -> bindParam(':no_days_falp', $differenceInDays);
        $stmt_update_mm -> bindParam(':shipment_details_ref', $shipment_details_ref);
        $stmt_update_mm -> execute();
    }

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