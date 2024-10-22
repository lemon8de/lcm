<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';

    $invoice_no = $_POST['invoice_no'];
    $container_no = $_POST['container_no'];
    $destination_service_center = $_POST['destination_service_center'];
    $ship_out_date = $_POST['ship_out_date'] == "" ? null : $_POST['ship_out_date'];
    $no_pallets = $_POST['no_pallets'];
    $no_cartons = $_POST['no_cartons'];
    $pack_qty = $_POST['pack_qty'];
    $invoice_amount = $_POST['invoice_amount'];
    

    //check if duplicate, reject
    $sql_if_duplicate = "SELECT id from m_outgoing_fsib where invoice_no = :invoice_no";
    $stmt_if_duplcate = $conn -> prepare($sql_if_duplicate);
    $stmt_if_duplcate -> bindParam(":invoice_no", $invoice_no);
    $stmt_if_duplcate -> execute();

    if ($stmt_if_duplcate -> fetch(PDO::FETCH_ASSOC)) {
        //NOTE, concerning the addition of the update api, this update block is disabled for good
        $notification = [
            "icon" => "warning",
            "text" => "INVOICE NO DUPLICATE<br>No Additions Made",
        ];
        $_SESSION['notification'] = json_encode($notification);
        header('location: ../pages/add_outgoing.php');
        exit();
    }

    //generate more details
    $sql_uniqueid_duplicate = "SELECT COUNT(id) from m_outgoing_fsib where outgoing_details_ref = :outgoing_details_ref";
    $stmt_uniqueid_duplicate = $conn -> prepare($sql_uniqueid_duplicate);
    do {
        // Generate a new unique string
        $outgoing_details_ref = uniqid('outgoing_', true);
        $stmt_uniqueid_duplicate->bindParam(':outgoing_details_ref', $outgoing_details_ref);
        $stmt_uniqueid_duplicate->execute();
        $count = $stmt_uniqueid_duplicate->fetchColumn();
    } while ($count > 0);

    $destination = $destination_service_center;
    if (in_array($destination_service_center, ['LANGELES1W', 'LANGELES1', 'LONGBEACHW'])) {
        $car_model = "HONDA";
    } else if ($destination_service_center == "LANGELESW") {
        $car_model = "SUBARU";
    }

    if (strlen(trim($container_no)) == 11) {
        $mode_of_shipment = "SEA";
    } else if (strlen(trim($container_no)) == 6 || in_array($container_no, ['AIRCRAFT', 'LCL'])) {
        $mode_of_shipment = "AIR";
    } else {
        $mode_of_shipment = "TBA";
    }

    //insert into
    $sql_insert_fsib = "INSERT into m_outgoing_fsib (outgoing_details_ref, invoice_no, container_no, destination_service_center, destination, car_model, ship_out_date, no_pallets, no_cartons, pack_qty, invoice_amount) values (:outgoing_details_ref, :invoice_no, :container_no, :destination_service_center, :destination, :car_model, :ship_out_date, :no_pallets, :no_cartons, :pack_qty, :invoice_amount); INSERT into m_outgoing_vessel_details (outgoing_details_ref, mode_of_shipment) values (:outgoing_details_ref2, :mode_of_shipment)";
    $stmt_insert_fsib = $conn -> prepare($sql_insert_fsib);

    $sql_init_tables = "INSERT into m_outgoing_rtv (outgoing_details_ref) values (:outgoing_details_ref); INSERT into m_outgoing_invoice_details (outgoing_details_ref) values (:outgoing_details_ref2); INSERT into m_outgoing_bl_details (outgoing_details_ref) values (:outgoing_details_ref3); INSERT into m_outgoing_container_details (outgoing_details_ref) values (:outgoing_details_ref4); INSERT into m_outgoing_dispatching_details (outgoing_details_ref) values (:outgoing_details_ref5); INSERT into m_outgoing_cont_lineup (outgoing_details_ref) values (:outgoing_details_ref6)";
    $stmt_init_tables = $conn -> prepare($sql_init_tables);

    $stmt_insert_fsib -> bindParam(":outgoing_details_ref", $outgoing_details_ref);
    $stmt_insert_fsib -> bindParam(":outgoing_details_ref2", $outgoing_details_ref);
    $stmt_insert_fsib -> bindParam(":invoice_no", $invoice_no);
    $stmt_insert_fsib -> bindParam(":container_no", $container_no);
    $stmt_insert_fsib -> bindParam(":destination_service_center", $destination_service_center);
    $stmt_insert_fsib -> bindParam(":destination", $destination);
    $stmt_insert_fsib -> bindParam(":car_model", $car_model);
    $stmt_insert_fsib -> bindParam(":ship_out_date", $ship_out_date);
    $stmt_insert_fsib -> bindParam(":no_pallets", $no_pallets);
    $stmt_insert_fsib -> bindParam(":no_cartons", $no_cartons);
    $stmt_insert_fsib -> bindParam(":pack_qty", $pack_qty);
    $stmt_insert_fsib -> bindParam(":invoice_amount", $invoice_amount);
    $stmt_insert_fsib -> bindParam(":mode_of_shipment", $mode_of_shipment);
    $stmt_insert_fsib -> execute();

    $stmt_init_tables -> bindParam(":outgoing_details_ref", $outgoing_details_ref);
    $stmt_init_tables -> bindParam(":outgoing_details_ref2", $outgoing_details_ref);
    $stmt_init_tables -> bindParam(":outgoing_details_ref3", $outgoing_details_ref);
    $stmt_init_tables -> bindParam(":outgoing_details_ref4", $outgoing_details_ref);
    $stmt_init_tables -> bindParam(":outgoing_details_ref5", $outgoing_details_ref);
    $stmt_init_tables -> bindParam(":outgoing_details_ref6", $outgoing_details_ref);
    $stmt_init_tables -> execute();

    //status monitoring, based on the status - switch invoice masterlist
    $pattern = '/(.+)-(.+)-(.+)-(.+)/';
    if (preg_match_all($pattern, $invoice_no, $matches)) {
        $switch_invoice_code = $matches[2];
    }
    $sql = "SELECT status_allowed from m_outgoing_status_list where switch_invoice_code = :switch_invoice_code";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":switch_invoice_code", $switch_invoice_code[0]);
    $stmt -> execute();

    if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        if ($data['status_allowed'] == '1') {
            $sql = "INSERT into m_outgoing_status_details (outgoing_details_ref, status, co_status) values (:outgoing_details_ref, 'N/A', 'N/A')";
            $stmt = $conn -> prepare($sql);
            $stmt -> bindParam(":outgoing_details_ref", $outgoing_details_ref);
            $stmt -> execute();
        } else {
            //this switch invoice do not need a status monitoring tab
        }
    } else {
        //masterlist failure
    }

    $notification = [
        "icon" => "success",
        "text" => "Invoice Added",
    ];
    $_SESSION['notification'] = json_encode($notification);
    header('location: ../pages/add_outgoing.php');
    exit();
