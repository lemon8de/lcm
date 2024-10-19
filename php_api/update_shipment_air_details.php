<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';

    $shipment_details_ref = $_POST['shipment_details_ref'];
    $forwarder = $_POST['forwarder'];
    $origin = $_POST['origin'];
    $hawb_awb = $_POST['hawb_awb'];
    $eta = $_POST['eta'] == "" ? null : $_POST['eta'];
    $gross_weight = $_POST['gross_weight'];
    $chargeable_weight = $_POST['chargeable_weight'];
    $no_packages = $_POST['no_packages'];
    $invoice_no = $_POST['invoice_no'];
    $commodity = $_POST['commodity'];
    $classification = $_POST['classification'];
    $shipment_status = $_POST['shipment_status'];
    $shipment_status_progress = $_POST['shipment_status_progress'];
    $incoterm = $_POST['incoterm'];

    $sql = "SELECT forwarder, origin, hawb_awb, format(eta, 'yyyy-MM-dd') as eta, round(gross_weight, 2) as gross_weight, round(chargeable_weight, 2) as chargeable_weight, no_packages, invoice_no, commodity, classification, shipment_status, shipment_status_progress, incoterm from t_shipment_air_details where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":shipment_details_ref", $shipment_details_ref);
    $stmt -> execute();

    $compare_set = array($forwarder, $origin, $hawb_awb, $eta, $gross_weight, $chargeable_weight, $no_packages, $invoice_no, $commodity, $classification, $shipment_status, $shipment_status_progress, $incoterm);
    if ($shipment = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $old_invoice = $shipment['invoice_no'];
        $shipment_keys = array_keys($shipment);
        $shipment_values = array_values($shipment);
    }

    $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to, username) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to, :username)";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":username", $_SESSION['username']);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> bindValue(':table_name', 't_shipment_air_details');

    for ($i = 0; $i < count($shipment_keys); $i++) {
        if ($compare_set[$i] !== $shipment_values[$i]) {
            //insert into changes table
            $stmt -> bindParam(':column_name', $shipment_keys[$i]);
            $stmt -> bindParam(':changed_from', $shipment_values[$i]);
            $stmt -> bindParam(':changed_to', $compare_set[$i]);
            $stmt -> execute();
        }
    }
    #type_of_expense validation
    $sql = "SELECT type_of_expense from list_commodity where method = 'air' and classification = :classification";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(':classification', $classification);
    $stmt -> execute();

    if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $type_of_expense = $data['type_of_expense'];
    }

    $sql = "UPDATE t_shipment_air_details set forwarder = :forwarder, origin = :origin, hawb_awb = :hawb_awb, eta = :eta, gross_weight = :gross_weight, chargeable_weight = :chargeable_weight, no_packages = :no_packages, invoice_no = :invoice_no, commodity = :commodity, classification = :classification, type_of_expense = :type_of_expense, shipment_status = :shipment_status, shipment_status_progress = :shipment_status_progress, incoterm = :incoterm where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt->bindParam(':forwarder', $forwarder);
    $stmt->bindParam(':origin', $origin);
    $stmt->bindParam(':hawb_awb', $hawb_awb);
    $stmt->bindParam(':eta', $eta);
    $stmt->bindParam(':gross_weight', $gross_weight);
    $stmt->bindParam(':chargeable_weight', $chargeable_weight);
    $stmt->bindParam(':no_packages', $no_packages);
    $stmt->bindParam(':invoice_no', $invoice_no);
    $stmt->bindParam(':commodity', $commodity);
    $stmt->bindParam(':classification', $classification);
    $stmt->bindParam(':type_of_expense', $type_of_expense);
    $stmt->bindParam(':shipment_status', $shipment_status);
    $stmt->bindParam(':shipment_status_progress', $shipment_status_progress);
    $stmt->bindParam(':incoterm', $incoterm);
    $stmt->bindParam(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    //invoice change we need to wipe that shit

    if ($old_invoice !== $invoice_no) {
        $sql = "DELETE import_data where shipment_details_ref = :shipment_details_ref";
        $stmt = $conn -> prepare($sql);
        $stmt -> bindParam(":shipment_details_ref", $shipment_details_ref);
        $stmt -> execute();

        //build the invoices
        #invoice validation
        //removes shortcutting of invoices
        $list = explode(", ", $invoice_no);
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

        $sql_invoice = "INSERT into import_data (shipment_details_ref, shipping_invoice) values (:shipment_details_ref, :shipping_invoice)";
        $stmt_invoice = $conn -> prepare($sql_invoice);
        foreach ($fixed_list as $invoice) {
            $stmt_invoice -> bindParam(':shipment_details_ref', $shipment_details_ref);
            $stmt_invoice -> bindParam(':shipping_invoice', $invoice);
            $stmt_invoice -> execute();
        }
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