<?php
    require 'db_connection.php';
    $forwarder = $_POST['forwarder'];
    $origin = $_POST['origin'];
    $hawb_awb = $_POST['hawb_awb'];
    //instant end if hawb_awb exists
    if ($conn->query("SELECT COUNT(*) FROM t_shipment_air_details WHERE hawb_awb = '$hawb_awb'")->fetchColumn() > 0) {
        $conn = null;
        $notification = [
            "icon" => "warning",
            "text" => "Duplicate HAWB / AWB. No new data points added",
        ];
        $return_body = [];
        $return_body['notification'] = $notification;
        echo json_encode($return_body);
        exit();
    }

    $gross_weight = $_POST['gross_weight'];
    $chargeable_weight = $_POST['chargeable_weight'];
    $no_packages = $_POST['no_packages'];
    $commodity = $_POST['commodity'];
    $classification = $_POST['classification'];
    $incoterm = $_POST['incoterm'];
    $shipment_status = $_POST['shipment_status'];
    $shipment_status_progress = $_POST['shipment_status_progress'];

    $time_received = $_POST['time_received'] == "" ? null : $_POST['time_received'];
    $received_by = $_POST['received_by'] == "" ? null : $_POST['received_by'];

    #date validation
    $eta = $_POST['eta'] == "" ? null : $_POST['eta'];
    $tentative_delivery_schedule = $_POST['tentative_delivery_schedule'] == "" ? null : $_POST['tentative_delivery_schedule'];
    $required_delivery = $_POST['required_delivery'] == "" ? null : $_POST['required_delivery'];
    $actual_date_of_delivery = $_POST['actual_date_of_delivery'] == "" ? null : $_POST['actual_date_of_delivery'];

    #invoice validation
    $commercial_invoice = trim($_POST['invoice_no']);
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
    $invoice_no = implode(", ", $fixed_list);
    #you also have fixed list which will be useful on iterating on invoices

    #type_of_expense validation
    $sql = "SELECT type_of_expense from list_commodity where method = 'air' and classification = :classification";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(':classification', $classification);
    $stmt -> execute();

    if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $type_of_expense = $data['type_of_expense'];
    }

    //generate a shipment details ref
    $sql = "SELECT COUNT(*) FROM t_shipment_air_details WHERE shipment_details_ref = :shipment_details_ref";
    $stmt_find_duplicate = $conn->prepare($sql);
    do {
        // Generate a new unique string
        $shipment_details_ref = uniqid('air_', true);
        $stmt_find_duplicate->bindParam(':shipment_details_ref', $shipment_details_ref);
        $stmt_find_duplicate->execute();
        $count = $stmt_find_duplicate->fetchColumn();
    } while ($count > 0);

    // First insert into t_shipment_air_details
    $sql1 = "INSERT INTO t_shipment_air_details (shipment_details_ref, forwarder, origin, hawb_awb, eta, gross_weight, chargeable_weight, no_packages, invoice_no, commodity, classification, type_of_expense, incoterm, shipment_status, shipment_status_progress) VALUES (:shipment_details_ref, :forwarder, :origin, :hawb_awb, :eta, :gross_weight, :chargeable_weight, :no_packages, :invoice_no, :commodity, :classification, :type_of_expense, :incoterm, :shipment_status, :shipment_status_progress)";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bindParam(':shipment_details_ref', $shipment_details_ref);
    $stmt1->bindParam(':forwarder', $forwarder);
    $stmt1->bindParam(':origin', $origin);
    $stmt1->bindParam(':hawb_awb', $hawb_awb);
    $stmt1->bindParam(':eta', $eta);
    $stmt1->bindParam(':gross_weight', $gross_weight);
    $stmt1->bindParam(':chargeable_weight', $chargeable_weight);
    $stmt1->bindParam(':no_packages', $no_packages);
    $stmt1->bindParam(':invoice_no', $invoice_no);
    $stmt1->bindParam(':commodity', $commodity);
    $stmt1->bindParam(':classification', $classification);
    $stmt1->bindParam(':type_of_expense', $type_of_expense);
    $stmt1->bindParam(':incoterm', $incoterm);
    $stmt1->bindParam(':shipment_status', $shipment_status);
    $stmt1->bindParam(':shipment_status_progress', $shipment_status_progress);
    $stmt1->execute();

    // Now insert into t_air_delivery_details
    $sql2 = "INSERT INTO t_air_delivery_details (shipment_details_ref, tentative_delivery_schedule, required_delivery, actual_date_of_delivery, time_received, received_by) VALUES (:shipment_details_ref, :tentative_delivery_schedule, :required_delivery, :actual_date_of_delivery, :time_received, :received_by)";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bindParam(':shipment_details_ref', $shipment_details_ref);
    $stmt2->bindParam(':tentative_delivery_schedule', $tentative_delivery_schedule);
    $stmt2->bindParam(':required_delivery', $required_delivery);
    $stmt2->bindParam(':actual_date_of_delivery', $actual_date_of_delivery);
    $stmt2->bindParam(':time_received', $time_received);
    $stmt2->bindParam(':received_by', $received_by);
    $stmt2->execute();

    //now we need to make the invoices, we can use $fixed_list just because
    $sql_duplicate = "SELECT id from import_data where shipping_invoice = :shipping_invoice";
    $stmt_duplicate = $conn -> prepare($sql_duplicate);
    $sql_invoice = "INSERT into import_data (shipment_details_ref, shipping_invoice) values (:shipment_details_ref, :shipping_invoice)";
    $stmt_invoice = $conn -> prepare($sql_invoice);

    foreach ($fixed_list as $invoice) {
        $stmt_duplicate -> bindParam(':shipping_invoice', $invoice);
        $stmt_duplicate -> execute();
        $duplicate = $stmt_duplicate -> fetch(PDO::FETCH_ASSOC);

        if (!$duplicate) {
            $stmt_invoice -> bindParam(':shipment_details_ref', $shipment_details_ref);
            $stmt_invoice -> bindParam(':shipping_invoice', $invoice);
            $stmt_invoice -> execute();
        }
    }

    $conn = null;
    $notification = [
        "icon" => "success",
        "text" => "good",
    ];
    $return_body = [];
    $return_body['notification'] = $notification;
    $return_body['added'] = true;
    echo json_encode($return_body);