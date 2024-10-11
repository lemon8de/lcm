<?php 
    require 'db_connection.php';
    $type_of_expense = "MASTERLIST FAILURE";
    $commodity = "MASTERLIST FAILURE";
    $classification = "MASTERLIST FAILURE";

    $bl_number = $_POST['bl_number'];

    //revision, better adding
    $containers = $_POST['containers'];
    $container_sizes = $_POST['container_sizes'];
    $container_size_suffix = isset($_POST['container_size_suffix']) ? $_POST['container_size_suffix'] : null;

    $commercial_invoice = trim($_POST['commercial_invoice']);
    //removes shortcutting of invoices
    $list = explode(", ", $commercial_invoice);
    $fixed_list = [];
    $first = false;
    foreach ($list as $invoice) {
        //check if this invoice is cut or not
        //if not cut, i.e. start of the loop proceed immediately
        if ($first) {
            //now we can start
            if (strlen($invoice) == strlen($pattern_invoice)) {
                array_push($fixed_list, $prefix_invoice . $invoice);
            } else {
                //new invoice block, refresh the pattern lookup
                $hyphen_index = strrpos($invoice, '-');
                $pattern_invoice = substr($invoice, $hyphen_index + 1);
                $prefix_invoice = substr($invoice, 0, $hyphen_index + 1);
                array_push($fixed_list, $invoice);
            }
        } else {
            $hyphen_index = strrpos($invoice, '-');
            $pattern_invoice = substr($invoice, $hyphen_index + 1);
            $prefix_invoice = substr($invoice, 0, $hyphen_index + 1);
            $first = true;
            array_push($fixed_list, $invoice);
        }
    }
    $commercial_invoice = implode(", ", $fixed_list);
    $commodity_lookup = $_POST['commodity'];
    $shipping_lines = $_POST['shipping_lines'];
    $forwarder_name = $_POST['forwarder_name'];
    $origin_port = $_POST['origin_port'];

    //revisions
    $destination_port = $_POST['destination_port'];
    $tsad_number = $_POST['tsad_number'];

    $shipment_status = $_POST['shipment_status'];
    $shipment_status_percentage = $_POST['shipment_status_percentage'];
    $vessel_name = $_POST['vessel_name'];

    //9 OCT revision, validating the vessel_name input
    //TS KAOHSIUNG V.24017S
    //CALIDRIS    V.0134S
    //CALIDRISV.     134S
    //cases, too many spaces, no spaces, zero on the voyage number
    $pattern = '/(.*)(\s*V.\s*)(0*)(.*)/';
    if (preg_match_all($pattern, $vessel_name, $matches)) {
        $vessel_name = trim($matches[1][0]) . " " . trim($matches[2][0]) .  " " . trim($matches[4][0]);
    }

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
    $sql = "SELECT shipment_details_ref from m_shipment_sea_details where bl_number = :bl_number";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue(':bl_number', $bl_number);
    $stmt -> execute();
    $bl_logged = $stmt->fetch(PDO::FETCH_ASSOC);

    //we should loop here now based on the amount of containers and container sizes
    //note since we are going on a loop here, this breaking out on duplicate is unreliable since it would have data which are not duplicate
    //one good change here is to change the container logged check to just be BL, this has been done
    if ($bl_logged) {
        $notification = [
            "icon" => "warning",
            "text" => "BL DUPLICATE<br>No Additions Made",
        ];
        $return_body = [];
        $return_body['notification'] = $notification;
        echo json_encode($return_body);
        exit();
    } else {

        $sql = "SELECT COUNT(*) FROM m_shipment_sea_details WHERE shipment_details_ref = :shipment_details_ref";
        $stmt_find_duplicate = $conn->prepare($sql);

        $confirm_departure = 0;
        $sql = "INSERT INTO m_shipment_sea_details (shipment_details_ref, bl_number, container, container_size, commercial_invoice, commodity, type_of_expense, classification, shipping_lines, forwarder_name, origin_port, destination_port, shipment_status, shipment_status_percentage, tsad_number, confirm_departure) values (:shipment_details_ref, :bl_number, :container, :container_size, :commercial_invoice, :commodity, :type_of_expense, :classification, :shipping_lines, :forwarder_name, :origin_port, :destination_port, :shipment_status, :shipment_status_percentage, :tsad_number, :confirm_departure)";
        $stmt_insert_shipment = $conn->prepare($sql);

        $sql = "INSERT INTO m_vessel_details (shipment_details_ref, vessel_name, eta_mnl, ata_mnl, atb) values (:shipment_details_ref, :vessel_name, :eta_mnl, :ata_mnl, :atb) ";
        $stmt_insert_vessel = $conn->prepare($sql);

        //we can just loop here no problem
        for ($i = 0; $i < count($containers); $i++) {
            do {
                // Generate a new unique string
                $shipment_details_ref = uniqid('sea_', true);
                $stmt_find_duplicate->bindParam(':shipment_details_ref', $shipment_details_ref);
                $stmt_find_duplicate->execute();
                $count = $stmt_find_duplicate->fetchColumn();
            } while ($count > 0);

            //these are the reason for the loop
            $container = $containers[$i];
            $container_size = $container_sizes[$i] . $container_size_suffix;
            //insert code in two tables, m_shipment_sea_details and m_vessel_details
            $stmt_insert_shipment -> bindValue(':shipment_details_ref', $shipment_details_ref);
            $stmt_insert_shipment -> bindValue(':bl_number', $bl_number);
            $stmt_insert_shipment -> bindValue(':container', $container);
            $stmt_insert_shipment -> bindValue(':container_size', $container_size);
            $stmt_insert_shipment -> bindValue(':commercial_invoice', $commercial_invoice);
            $stmt_insert_shipment -> bindValue(':commodity', $commodity);
            $stmt_insert_shipment -> bindValue(':type_of_expense', $type_of_expense);
            $stmt_insert_shipment -> bindValue(':classification', $classification);
            $stmt_insert_shipment -> bindValue(':shipping_lines', $shipping_lines);
            $stmt_insert_shipment -> bindValue(':forwarder_name', $forwarder_name);
            $stmt_insert_shipment -> bindValue(':origin_port', $origin_port);
            $stmt_insert_shipment -> bindValue(':destination_port', $destination_port);
            $stmt_insert_shipment -> bindValue(':shipment_status', $shipment_status);
            $stmt_insert_shipment -> bindValue(':shipment_status_percentage', $shipment_status_percentage);
            $stmt_insert_shipment -> bindValue(':tsad_number', $tsad_number);
            $stmt_insert_shipment -> bindValue(':confirm_departure', $confirm_departure);
            $stmt_insert_shipment -> execute();
    
            $stmt_insert_vessel -> bindValue(':shipment_details_ref', $shipment_details_ref);
            $stmt_insert_vessel -> bindValue(':vessel_name', $vessel_name);
            $stmt_insert_vessel -> bindValue(':eta_mnl', $eta_mnl);
            $stmt_insert_vessel -> bindValue(':ata_mnl', $ata_mnl);
            $stmt_insert_vessel -> bindValue(':atb', $atb);
            $stmt_insert_vessel -> execute();
        }
        $conn = null;
        $created = count($containers);
        $notification = [
            "icon" => "success",
            "text" => "{$created} new datapoints added",
        ];
        $return_body = [];
        $return_body['notification'] = $notification;
        $return_body['added'] = true;
        echo json_encode($return_body);
    }