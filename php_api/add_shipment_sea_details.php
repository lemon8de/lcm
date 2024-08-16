<?php 
    require '../php_static/session_lookup.php';
    require 'db_connection.php';
    require '../php_static/lookup_column.php';

    $bl_number = $_POST['bl_number'];
    $container = $_POST['container'];
    $container_size = $_POST['container_size'];
    $commercial_invoice = $_POST['commercial_invoice'];
    $commodity = $_POST['commodity'];
    $shipping_lines = $_POST['shipping_lines'];
    $forwarder_name = $_POST['forwarder_name'];
    $origin_port = $_POST['origin_port'];
    $shipment_status = $_POST['shipment_status'];
    $vessel_name = $_POST['vessel_name'];
    $eta_mnl = $_POST['eta_mnl'];
    $ata_mnl = $_POST['ata_mnl'];
    $atb = $_POST['atb'];

    //calculate the type of expense using commodity
    $method = 'sea';
    $sql = "SELECT type_of_expense, display_name, classification from list_commodity where value = :commodity and method = :method";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue(':commodity', $commodity);
    $stmt -> bindValue(':method', $method);
    $stmt -> execute();

    if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) { //get away with the array notice
        $type_of_expense = $result['type_of_expense'];
        $commodity = $result['display_name'];
        $classification = $result['classification'];
    }

    //check if the bl_number + container combination exists
    $sql = "SELECT id from m_shipment_sea_details where bl_number = :bl_number and container = :container";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue(':bl_number', $bl_number);
    $stmt -> bindValue(':container', $container);
    $stmt -> execute();
    $bl_container_logged = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($bl_container_logged) {
        //update
        echo 'update';
    } else {
        //insert into
        do {
            // Generate a new unique string
            $shipment_details_ref = uniqid('sea_', true);

            $sql = "SELECT COUNT(*) FROM m_shipment_sea_details WHERE shipment_details_ref = :shipment_details_ref";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':shipment_details_ref', $shipment_details_ref);
            $stmt->execute();
            $count = $stmt->fetchColumn();
        } while ($count > 0);

        //insert code in two tables, m_shipment_sea_details and m_vessel_details
        $confirm_departure = 0;
        $sql = "INSERT INTO m_shipment_sea_details (shipment_details_ref, bl_number, container, container_size, commercial_invoice, commodity, type_of_expense, classification, shipping_lines, forwarder_name, origin_port, shipment_status, confirm_departure) values (:shipment_details_ref, :bl_number, :container, :container_size, :commercial_invoice, :commodity, :type_of_expense, :classification, :shipping_lines, :forwarder_name, :origin_port, :shipment_status, :confirm_departure)";
        $stmt = $conn->prepare($sql);
        $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
        $stmt -> bindValue(':bl_number', $bl_number);
        $stmt -> bindValue(':container', $container);
        $stmt -> bindValue(':container_size', $container_size);
        $stmt -> bindValue(':commercial_invoice', $commercial_invoice);
        $stmt -> bindValue(':commodity', $commodity);
        $stmt -> bindValue(':type_of_expense', $type_of_expense);
        $stmt -> bindValue(':classification', $classification);
        $stmt -> bindValue(':shipping_lines', $shipping_lines);
        $stmt -> bindValue(':forwarder_name', $forwarder_name);
        $stmt -> bindValue(':origin_port', $origin_port);
        $stmt -> bindValue(':shipment_status', $shipment_status);
        $stmt -> bindValue(':confirm_departure', $confirm_departure);
        $stmt -> execute();

        $sql = "INSERT INTO m_vessel_details (shipment_details_ref, vessel_name, eta_mnl, ata_mnl, atb) values (:shipment_details_ref, :vessel_name, :eta_mnl, :ata_mnl, :atb) ";

        $stmt = $conn->prepare($sql);
        $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
        $stmt -> bindValue(':vessel_name', $vessel_name);
        $stmt -> bindValue(':eta_mnl', $eta_mnl == "" ? null : $eta_mnl);
        $stmt -> bindValue(':ata_mnl', $ata_mnl == "" ? null : $ata_mnl);
        $stmt -> bindValue(':atb', $atb == "" ? null : $atb);

        $stmt -> execute();
        $conn = null;

        header('location: ../pages/incoming_sea.php');
        exit();
    }