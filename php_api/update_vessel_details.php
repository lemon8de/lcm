<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';
    
    $shipment_details_ref = $_POST['shipment_details_ref'];
    $vessel_name = $_POST['vessel_name'];
    $eta_mnl = $_POST['eta_mnl'] == "" ? null : $_POST['eta_mnl'];
    $ata_mnl = $_POST['ata_mnl'] == "" ? null : $_POST['ata_mnl'];
    $atb = $_POST['atb'] == "" ? null : $_POST['atb'];

    //check if it changed
    $sql = "SELECT vessel_name, eta_mnl, ata_mnl, atb from m_vessel_details where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":shipment_details_ref", $shipment_details_ref);
    $stmt -> execute();
    
    if ($vessel_details = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $vessel_details['eta_mnl'] = $vessel_details['eta_mnl'] == null ? null : substr($vessel_details['eta_mnl'], 0, 10);
        $vessel_details['ata_mnl'] = $vessel_details['ata_mnl'] == null ? null : substr($vessel_details['ata_mnl'], 0, 10);
        $vessel_details['atb'] = $vessel_details['atb'] == null ? null : substr($vessel_details['atb'], 0, 10);
    }

    //check if unchanged, this is just a check, no changes will be made, early out of the code too
    if ($vessel_details['vessel_name'] == $vessel_name and $vessel_details['eta_mnl'] == $eta_mnl and $vessel_details['ata_mnl'] == $ata_mnl and $vessel_details['atb'] == $atb) {
        $conn = null;
        //header('location: ../pages/incoming_sea.php');
        //exit();
        $notification = [
            "icon" => "info",
            "text" => "No changes were made",
        ];
        $return_body = [];
        $return_body['notification'] = $notification;
        echo json_encode($return_body);
        exit();
    } 

    //addition sep5: when the vessel name changed, the entire vessel update should only occur on that container, so we have to change the query sql_containers from selecting alot of shipment_details_ref down to just one.
    //well i already have the shipment_details_ref of that singular entry from the post request so we just have to requery the similarity and choose to do a fetch to get all containers, or just use what one shipment details ref we have stop the execution of the code block below
    if ($vessel_details['vessel_name'] == $vessel_name) {
        //if it passes thru here---then there is a change and we need to get all the shipment_details_ref of all affected
        $sql_containers = "SELECT shipment_details_ref from m_vessel_details where vessel_name = :vessel_name and (eta_mnl = :eta_mnl or eta_mnl is null) and (ata_mnl = :ata_mnl or ata_mnl is null) and (atb = :atb or atb is null) order by id desc";
        $stmt_containers = $conn -> prepare($sql_containers);
        $stmt_containers -> bindParam(':vessel_name', $vessel_details['vessel_name']);
        $stmt_containers -> bindParam(':eta_mnl', $vessel_details['eta_mnl']);
        $stmt_containers -> bindParam(':ata_mnl', $vessel_details['ata_mnl']);
        $stmt_containers -> bindParam(':atb', $vessel_details['atb']);
        $stmt_containers -> execute();
        $containers = $stmt_containers->fetchAll(PDO::FETCH_COLUMN);
        //now container is an array that holds all shipment_detail_ref of all would be affected containers
    } else {
        $containers = [$shipment_details_ref];
    }

    //find what changed, and make m_change_history logs for all shipment_details_ref
    $compare_set_user = [$vessel_name, $eta_mnl, $ata_mnl, $atb];
    $compare_set_database_values = array_values($vessel_details);
    $compare_set_database_keys = array_keys($vessel_details);
    $sql_history = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to, username) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to, :username)";
    $stmt_history = $conn -> prepare($sql_history);
    $stmt_history -> bindParam(":username", $_SESSION['username']);
    $stmt_history -> bindValue(':table_name', 'm_vessel_details');

    //so we now check for changes here
    for ($i = 0; $i < count($compare_set_database_keys); $i++) {
        if ($compare_set_user[$i] !== $compare_set_database_values[$i]) {
            //another loop to make history logs for each and every container
            foreach($containers as $container_shipment_details_ref) {
                $stmt_history -> bindValue(':shipment_details_ref', $container_shipment_details_ref);
                $stmt_history -> bindValue(':column_name', $compare_set_database_keys[$i]);
                $stmt_history -> bindValue(':changed_from', $compare_set_database_values[$i]);
                $stmt_history -> bindValue(':changed_to', $compare_set_user[$i]);
                $stmt_history -> execute();
            }
        }
    }
    //changes logged, now do the update set for real
    $placeholders = rtrim(str_repeat('?,', count($containers)), ',');
    $sql_update = "UPDATE m_vessel_details set vessel_name = ?, eta_mnl = ?, ata_mnl = ?, atb = ? WHERE shipment_details_ref IN ($placeholders)";
    $stmt_update = $conn -> prepare($sql_update);
    $stmt_update->execute(array_merge([$vessel_name, $eta_mnl, $ata_mnl, $atb], $containers));

    //no of days updating, calculation of days at port
    //uses m_completion_details - date_port_out; m_vessel_details - atb
    $sql = "SELECT date_port_out from m_completion_details where shipment_details_ref = :shipment_details_ref";
    $stmt_check_days = $conn -> prepare($sql);
    $stmt_check_days -> bindParam(':shipment_details_ref', $shipment_details_ref);
    $stmt_check_days -> execute();
    if ($mm_detail = $stmt_check_days -> fetch(PDO::FETCH_ASSOC)) {
        //this if statement actually filters for confirmed shipments lol
        //notes that $atb might be null, the computation is atb - date port out + 1
        // Create DateTime objects
        $dateTime1 = $atb == null ? null : new DateTime($atb);
        $dateTime2 = $mm_detail['date_port_out'] == null ? null : new DateTime($mm_detail['date_port_out']);

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

    $notification = [
        "icon" => "success",
        "text" => "Details Updated",
    ];
    $return_body = [];
    $return_body['notification'] = $notification;
    echo json_encode($return_body);
    exit();