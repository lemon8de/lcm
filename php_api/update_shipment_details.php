<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';
    
    $shipment_details_ref = $_POST['shipment_details_ref'];
    $bl_number = $_POST['bl_number'];
    $container = $_POST['container'];
    $container_size = $_POST['container_size'];
    $commercial_invoice = $_POST['commercial_invoice'];
    $commodity = $_POST['commodity'];
    $shipping_lines = $_POST['shipping_lines'];
    $forwarder_name = $_POST['forwarder_name'];
    $origin_port = $_POST['origin_port'];
    $shipment_status = $_POST['shipment_status'];
    $destination_port = $_POST['destination_port'];
    $tsad_number = $_POST['tsad_number'];

    //check if changed, $commodity value is fucked and not the actual value on m_shipment_sea_details
    //why do i do this to myself
    $method = 'sea';
    $sql = "SELECT type_of_expense, display_name, classification, polytainer_detail from list_commodity where value = :commodity and method = :method";
    $stmt = $conn->prepare($sql);
    $stmt -> bindValue(':commodity', $commodity);
    $stmt -> bindValue(':method', $method);
    $stmt -> execute();

    if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) { //get away with the array notice
        $needs_polytainer = $result['polytainer_detail'];
        $type_of_expense = $result['type_of_expense'];
        $commodity = $result['display_name'];
        $classification = $result['classification'];
    }
    //now we can check, saves on api processing; if we can just stop right here it would be nice
    $sql = "SELECT bl_number, container, container_size, commercial_invoice, commodity, shipping_lines, forwarder_name, origin_port, shipment_status, destination_port, tsad_number, confirm_departure, polytainer_detail from m_shipment_sea_details left join list_commodity on commodity = display_name where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();
    $a = $stmt -> fetch(PDO::FETCH_ASSOC);

    if ($a) {
        //need this two later on when fixing the import_data
        $confirm_departure = $a['confirm_departure'];
        $has_polytainer = $a['polytainer_detail']; //needed by the polytainer edits below
        $invoices_old = $a['commercial_invoice'];

        if ($bl_number == $a['bl_number'] and $container == $a['container'] and $container_size == $a['container_size'] and $commercial_invoice == $a['commercial_invoice'] and $commodity == $a['commodity'] and $shipping_lines == $a['shipping_lines'] and $forwarder_name == $a['forwarder_name'] and $origin_port == $a['origin_port'] and $shipment_status == $a['shipment_status'] and $destination_port == $a['destination_port'] and $tsad_number == $a['tsad_number']) {
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
    }
    //its official there are changes made,
    //some notes, this change api only affects one row in the database, so it should be simpler right?
    //the only worse contender here is commercial invoice, because we have to wipe all the existing invoices in import_data
    //so maybe get the old and new invoice first before anything so we can preserve those.

    //find what changed, and make m_change_history logs for all shipment_details_ref
    $compare_set_user = [$bl_number, $container, $container_size, $commercial_invoice, $commodity, $shipping_lines, $forwarder_name, $origin_port, $shipment_status, $destination_port, $tsad_number];
    $compare_set_database_values = array_values($a);
    $compare_set_database_keys = array_keys($a);
    //there is confirm_departure here, not need on compare but I need it later on for updating import_data
    array_pop($compare_set_database_values);
    array_pop($compare_set_database_keys);
    array_pop($compare_set_database_values);
    array_pop($compare_set_database_keys);

    $sql_history = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
    $stmt_history = $conn -> prepare($sql_history);
    $stmt_history -> bindValue(':table_name', 'm_shipment_sea_details');
    $stmt_history -> bindValue(':shipment_details_ref', $shipment_details_ref);

    //so we now check for changes here
    for ($i = 0; $i < count($compare_set_database_keys); $i++) {
        if ($compare_set_user[$i] !== $compare_set_database_values[$i]) {
            $stmt_history -> bindValue(':column_name', $compare_set_database_keys[$i]);
            $stmt_history -> bindValue(':changed_from', $compare_set_database_values[$i]);
            $stmt_history -> bindValue(':changed_to', $compare_set_user[$i]);
            $stmt_history -> execute();
        }
    }
    //changes are now logged into history, we can proceed on updating now
    $sql = "UPDATE m_shipment_sea_details set bl_number = :bl_number, container = :container, container_size = :container_size, commercial_invoice = :commercial_invoice, commodity = :commodity, type_of_expense = :type_of_expense, classification = :classification, shipping_lines = :shipping_lines, forwarder_name = :forwarder_name, origin_port = :origin_port, shipment_status = :shipment_status, destination_port = :destination_port, tsad_number = :tsad_number where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(':bl_number', $bl_number);
    $stmt -> bindParam(':container', $container);
    $stmt -> bindParam(':container_size', $container_size);
    $stmt -> bindParam(':commercial_invoice', $commercial_invoice);
    $stmt -> bindParam(':commodity', $commodity);
    $stmt -> bindParam(':type_of_expense', $type_of_expense);
    $stmt -> bindParam(':classification', $classification);
    $stmt -> bindParam(':shipping_lines', $shipping_lines);
    $stmt -> bindParam(':forwarder_name', $forwarder_name);
    $stmt -> bindParam(':origin_port', $origin_port);
    $stmt -> bindParam(':shipment_status', $shipment_status);
    $stmt -> bindParam(':destination_port', $destination_port);
    $stmt -> bindParam(':tsad_number', $tsad_number);
    $stmt -> bindParam(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    //now thats updated move on to the invoice
    if ($confirm_departure and $invoices_old != $commercial_invoice) {
        //means there are information on the import data table, DELETE and insert into new ones
        //$invoices - grabbed from earlier needs to split still
        $pattern = '/([A-Za-z0-9-_()]+)/';
        if (preg_match_all($pattern, $invoices_old, $matches)) {
            $invoices_old = $matches[0];
        } 
        if (preg_match_all($pattern, $commercial_invoice, $matches)) {
            $invoices_new = $matches[0];
        } 
        $placeholders = rtrim(str_repeat('?,', count($invoices_old)), ',');
        //deletes the old invoices, this can just rerun everytime no problem
        $sql = "DELETE from import_data where shipping_invoice IN ($placeholders)";
        $stmt = $conn -> prepare($sql);
        $stmt -> execute($invoices_old);

        //how to do that: well we have invoices_new, we just need to check for its existence
        $placeholders = rtrim(str_repeat('?,', count($invoices_new)), ',');
        $sql = "SELECT shipping_invoice from import_data where shipping_invoice IN ($placeholders)";
        $stmt = $conn -> prepare($sql);
        $stmt -> execute($invoices_new);
        $existing_invoices = $stmt -> fetchAll(PDO::FETCH_COLUMN);

        foreach($existing_invoices as $exclude_insert) {
            $index = array_search($exclude_insert, $invoices_new);
            if ($index !== false) {
                unset($invoices_new[$index]);
            }
        }
        $invoices_new = array_values($invoices_new);

        //this though, no can do need to check if the new invoice already exists first
        $sql = "INSERT into import_data (shipment_details_ref, shipping_invoice) values (:shipment_details_ref, :shipping_invoice)";
        $stmt = $conn -> prepare($sql);
        $stmt -> bindParam(':shipment_details_ref', $shipment_details_ref);

        foreach ($invoices_new as $invoice) {
            $stmt -> bindValue(':shipping_invoice', $invoice);
            $stmt -> execute();
        }
    }

    //one last thing, we need wipe the polytainer details if it doesn't need one, or needs a new one
    if ($confirm_departure and ($has_polytainer and !$needs_polytainer)) {
        //delete polytainer detail
        $sql_delete = "DELETE from m_polytainer_details where shipment_details_ref = :shipment_details_ref";
        $stmt_delete = $conn -> prepare($sql_delete);
        $stmt_delete -> bindParam(':shipment_details_ref', $shipment_details_ref);
        $stmt_delete -> execute();
    } else if ($confirm_departure and (!$has_polytainer and $needs_polytainer)) {
        //insert into polytainer detail
        $sql_insert = "INSERT into m_polytainer_details (shipment_details_ref) values (:shipment_details_ref)";
        $stmt_insert = $conn -> prepare($sql_insert);
        $stmt_insert -> bindParam(':shipment_details_ref', $shipment_details_ref);
        $stmt_insert -> execute();
    }

    $conn = null;
    //header('location: ../pages/incoming_sea.php');
    //exit();
    $notification = [
        "icon" => "success",
        "text" => "Changes Applied",
    ];
    $return_body = [];
    $return_body['notification'] = $notification;
    echo json_encode($return_body);
    exit();