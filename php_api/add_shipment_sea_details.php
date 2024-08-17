<?php 
    require 'db_connection.php';

    $type_of_expense = "MASTERLIST FAILURE";
    $commodity = "MASTERLIST FAILURE";
    $classification = "MASTERLIST FAILURE";

    $bl_number = $_POST['bl_number'];
    $container = $_POST['container'];
    $container_size = $_POST['container_size'];
    $commercial_invoice = $_POST['commercial_invoice'];
    $commodity_lookup = $_POST['commodity'];
    $shipping_lines = $_POST['shipping_lines'];
    $forwarder_name = $_POST['forwarder_name'];
    $origin_port = $_POST['origin_port'];
    $shipment_status = $_POST['shipment_status'];
    $vessel_name = $_POST['vessel_name'];

    $eta_mnl = $_POST['eta_mnl'] == "" ? null : $_POST['eta_mnl'];
    $ata_mnl = $_POST['ata_mnl'] == "" ? null : $_POST['ata_mnl'];
    $atb = $_POST['atb'] == "" ? null : $_POST['atb'];

    //calculate the type of expense using commodity
    $method = 'sea';
    $sql = "SELECT type_of_expense, display_name, classification from list_commodity where value = :commodity and method = :method";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue(':commodity', $commodity_lookup);
    $stmt -> bindValue(':method', $method);
    $stmt -> execute();

    if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) { //get away with the array notice
        $type_of_expense = $result['type_of_expense'];
        $commodity = $result['display_name'];
        $classification = $result['classification'];
    }

    //check if the bl_number + container combination exists
    $sql = "SELECT shipment_details_ref from m_shipment_sea_details where bl_number = :bl_number and container = :container";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue(':bl_number', $bl_number);
    $stmt -> bindValue(':container', $container);
    $stmt -> execute();
    $bl_container_logged = $stmt->fetch(PDO::FETCH_ASSOC);

    //if statement to figure out if we do an insert into or update
    if ($bl_container_logged) {
        //UPDATE
        $shipment_details_ref = $bl_container_logged['shipment_details_ref'];

        //update block for updating the main table
        $sql = "SELECT bl_number, container, container_size, commercial_invoice, commodity, type_of_expense, classification, shipping_lines, forwarder_name, origin_port, shipment_status from m_shipment_sea_details where bl_number = :bl_number and container = :container";

        $stmt = $conn->prepare($sql);
        $stmt -> bindValue(':bl_number', $bl_number);
        $stmt -> bindValue(':container', $container);
        $stmt -> execute();
        $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

        $compare_set = array($bl_number, $container, $container_size, $commercial_invoice, $commodity, $type_of_expense, $classification, $shipping_lines, $forwarder_name, $origin_port, $shipment_status);
        if ($shipment) {
            $shipment_keys = array_keys($shipment);
            $shipment_values = array_values($shipment);
        }

        // compare_set, shipment_keys and shipment_values all have the same length and data format now, we compare
        $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
        $stmt = $conn -> prepare($sql);
        $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
        $stmt -> bindValue(':table_name', 'm_shipment_sea_details');

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
        $sql = "UPDATE m_shipment_sea_details set bl_number = :bl_number, container = :container, container_size = :container_size, commercial_invoice = :commercial_invoice, commodity = :commodity, type_of_expense = :type_of_expense, classification = :classification, shipping_lines = :shipping_lines, forwarder_name = :forwarder_name, origin_port = :origin_port, shipment_status = :shipment_status where shipment_details_ref = :shipment_details_ref";
        $stmt = $conn -> prepare($sql);
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
        $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
        $stmt -> execute();

        //update code for updating the second table, vessel_details
        $sql = "SELECT vessel_name, eta_mnl, ata_mnl, atb from m_vessel_details where shipment_details_ref = :shipment_details_ref";
        $stmt = $conn->prepare($sql);
        $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
        $stmt -> execute();
        $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

        $compare_set = array($vessel_name, $eta_mnl, $ata_mnl, $atb);
        if ($shipment) {
            $shipment_keys = array_keys($shipment);
            $shipment_values = array_values($shipment);
            // make the dates match string wise for proper comparing
            $shipment_values[1] = $shipment_values[1] == null ? null : substr($shipment_values[1], 0, 10);
            $shipment_values[2] = $shipment_values[2] == null ? null : substr($shipment_values[2], 0, 10);
            $shipment_values[3] = $shipment_values[3] == null ? null : substr($shipment_values[3], 0, 10);
        }

        // compare_set, shipment_keys and shipment_values all have the same length and data format now, we compare
        $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
        $stmt = $conn -> prepare($sql);
        $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
        $stmt -> bindValue(':table_name', 'm_vessel_details');

        for ($i = 0; $i < count($shipment_keys); $i++) {
            if ($compare_set[$i] !== $shipment_values[$i]) {
                //insert into changes table
                $stmt -> bindParam(':column_name', $shipment_keys[$i]);
                $stmt -> bindParam(':changed_from', $shipment_values[$i]);
                $stmt -> bindParam(':changed_to', $compare_set[$i]);
                $stmt -> execute();
            }
        }
        //finally update
        $sql = "UPDATE m_vessel_details set vessel_name = :vessel_name, eta_mnl = :eta_mnl, ata_mnl = :ata_mnl, atb = :atb where shipment_details_ref = :shipment_details_ref";
        $stmt = $conn -> prepare($sql);
        $stmt -> bindValue(':vessel_name', $vessel_name);
        $stmt -> bindValue(':eta_mnl', $eta_mnl);
        $stmt -> bindValue(':ata_mnl', $ata_mnl);
        $stmt -> bindValue(':atb', $atb);
        $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
        $stmt -> execute();
        $conn = null;

        header('location: ../pages/incoming_sea.php');
        exit();

    } else {
        //insert into

        $sql = "SELECT COUNT(*) FROM m_shipment_sea_details WHERE shipment_details_ref = :shipment_details_ref";
        $stmt = $conn->prepare($sql);
        do {
            // Generate a new unique string
            $shipment_details_ref = uniqid('sea_', true);
            $stmt->bindParam(':shipment_details_ref', $shipment_details_ref);
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
        $stmt -> bindValue(':eta_mnl', $eta_mnl);
        $stmt -> bindValue(':ata_mnl', $ata_mnl);
        $stmt -> bindValue(':atb', $atb);

        $stmt -> execute();
        $conn = null;

        header('location: ../pages/incoming_sea.php');
        exit();
    }