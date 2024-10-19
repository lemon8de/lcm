<?php
    require 'db_connection.php';
    require '../php_static/session_lookup.php';
    
    function convert_empty_tonull ($string) {
        return $string == "" ? null : $string;
    }

    $shipping_invoice = isset($_POST['shipping_invoice']) ? $_POST['shipping_invoice'] : null;
    $shipment_details_ref = isset($_POST['shipment_details_ref']) ? $_POST['shipment_details_ref'] : null;

    //history logging
    $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to, username) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to, :username)";
    $stmt_history = $conn -> prepare($sql);
    $stmt_history -> bindParam(":username", $_SESSION['username']);
    $stmt_history -> bindValue(':table_name', 'import_data');
    function formatValue($value) {
        // Convert to float, round to 2 decimal places, and then to string
        $roundedValue = round(floatval($value), 2);
                
        // Return null if the rounded value is zero, otherwise return the string representation
        return $roundedValue == 0 ? null : (string)$roundedValue;
    }

    if ($shipping_invoice == null) {
        //meaning shipment_details_ref is not null, meaning the button clicked is the update general
        $shipper = convert_empty_tonull($_POST['shipper']);
        $gross_weight = convert_empty_tonull($_POST['gross_weight']);
        $brokerage_fee = convert_empty_tonull($_POST['brokerage_fee']);
        $incoterm = convert_empty_tonull($_POST['incoterm']);
        $ip_number = convert_empty_tonull($_POST['ip_number']);
        $dr_number = convert_empty_tonull($_POST['dr_number']);

        $received_by = convert_empty_tonull($_POST['received_by']);
        $time_received = convert_empty_tonull($_POST['time_received']);
        $wharfage = convert_empty_tonull($_POST['wharfage']);
        $arrastre_charges = convert_empty_tonull($_POST['arrastre_charges']);
        $flight_no = convert_empty_tonull($_POST['flight_no']);

        $total_custom_value = convert_empty_tonull($_POST['total_custom_value']);
        $duitable_value = convert_empty_tonull($_POST['duitable_value']);
        $rate = convert_empty_tonull($_POST['rate']);
        $customs_duty = convert_empty_tonull($_POST['customs_duty']);
        $landed_cost = convert_empty_tonull($_POST['landed_cost']);
        $vat = convert_empty_tonull($_POST['vat']);
        $bank_charges = convert_empty_tonull($_POST['bank_charges']);
        $entry_no = convert_empty_tonull($_POST['entry_no']);
        $or_number = convert_empty_tonull($_POST['or_number']);
        $assessment_date = convert_empty_tonull($_POST['assessment_date']);

        //this edit will apply to all invoices of that bl_number, this could have been simpler with a database change, but whatever
        //this will produce so much redundant data it will be insane

        $sql = "SELECT shipping_invoice from import_data where shipment_details_ref = :shipment_details_ref";
        $stmt_getall = $conn -> prepare($sql);
        $stmt_getall -> bindParam(":shipment_details_ref", $shipment_details_ref);
        $stmt_getall -> execute();
        $shipping_invoices = $stmt_getall -> fetchAll(PDO::FETCH_COLUMN);

        $sql = "SELECT top 1 shipper, gross_weight, incoterm, ip_number, dr_number, received_by, time_received, total_custom_value, duitable_value, rate, customs_duty, landed_cost, vat, bank_charges, wharfage, arrastre_charges, entry_no, or_number, assessment_date, brokerage_fee, flight_no from import_data where shipment_details_ref = :shipment_details_ref";
        $stmt = $conn-> prepare($sql);
        $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
        $stmt -> execute();
        $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

        

        $compare_set = array($shipper, $gross_weight, $incoterm, $ip_number, $dr_number, $received_by, $time_received, $total_custom_value, $duitable_value, $rate, $customs_duty, $landed_cost, $vat, $bank_charges, $wharfage, $arrastre_charges, $entry_no, $or_number, $assessment_date, $brokerage_fee, $flight_no);
        if ($shipment) {
            $shipment_keys = array_keys($shipment);
            
            
            $shipment['gross_weight'] = formatValue($shipment['gross_weight']);
            $shipment['total_custom_value'] = formatValue($shipment['total_custom_value']);
            $shipment['duitable_value'] = formatValue($shipment['duitable_value']);
            $shipment['rate'] = formatValue($shipment['rate']);
            $shipment['customs_duty'] = formatValue($shipment['customs_duty']);
            $shipment['landed_cost'] = formatValue($shipment['landed_cost']);
            $shipment['vat'] = formatValue($shipment['vat']);
            $shipment['bank_charges'] = formatValue($shipment['bank_charges']);
            $shipment['wharfage'] = formatValue($shipment['wharfage']);
            $shipment['arrastre_charges'] = formatValue($shipment['arrastre_charges']);
            $shipment['brokerage_fee'] = formatValue($shipment['brokerage_fee']);
            $shipment_values = array_values($shipment);
        }
        for ($i = 0; $i < count($shipment_keys); $i++) {
            if ($compare_set[$i] !== $shipment_values[$i]) {
                //insert into changes table
                foreach($shipping_invoices as $shipping_invoice) {
                    $stmt_history -> bindParam(":shipment_details_ref", $shipping_invoice);
                    $stmt_history -> bindParam(':column_name', $shipment_keys[$i]);
                    $stmt_history -> bindParam(':changed_from', $shipment_values[$i]);
                    $stmt_history -> bindParam(':changed_to', $compare_set[$i]);
                    $stmt_history -> execute();
                }
            }
        }
        //history log done now update
        $sql = "UPDATE import_data SET shipper = :shipper, gross_weight = :gross_weight, incoterm = :incoterm, ip_number = :ip_number, dr_number = :dr_number, received_by = :received_by, time_received = :time_received, total_custom_value = :total_custom_value, duitable_value = :duitable_value, rate = :rate, customs_duty = :customs_duty, landed_cost = :landed_cost, vat = :vat, bank_charges = :bank_charges, wharfage = :wharfage, arrastre_charges = :arrastre_charges, entry_no = :entry_no, or_number = :or_number, assessment_date = :assessment_date, brokerage_fee = :brokerage_fee, flight_no = :flight_no WHERE shipment_details_ref = :shipment_details_ref";
        $stmt = $conn -> prepare($sql);
        $stmt->bindValue(':shipper', $shipper);
        $stmt->bindValue(':gross_weight', $gross_weight);
        $stmt->bindValue(':incoterm', $incoterm);
        $stmt->bindValue(':ip_number', $ip_number);
        $stmt->bindValue(':dr_number', $dr_number);
        $stmt->bindValue(':received_by', $received_by);
        $stmt->bindValue(':time_received', $time_received);
        $stmt->bindValue(':total_custom_value', $total_custom_value);
        $stmt->bindValue(':duitable_value', $duitable_value);
        $stmt->bindValue(':rate', $rate);
        $stmt->bindValue(':customs_duty', $customs_duty);
        $stmt->bindValue(':landed_cost', $landed_cost);
        $stmt->bindValue(':vat', $vat);
        $stmt->bindValue(':bank_charges', $bank_charges);
        $stmt->bindValue(':wharfage', $wharfage);
        $stmt->bindValue(':arrastre_charges', $arrastre_charges);
        $stmt->bindValue(':entry_no', $entry_no);
        $stmt->bindValue(':or_number', $or_number);
        $stmt->bindValue(':assessment_date', $assessment_date);
        $stmt->bindValue(':shipment_details_ref', $shipment_details_ref);
        $stmt->bindValue(':brokerage_fee', $brokerage_fee);
        $stmt->bindValue(':flight_no', $flight_no);
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
        exit();
    } else {
        //they only wanted to edit the singular invoice
        $commodity_quantity = convert_empty_tonull($_POST['commodity_quantity']);
        $commodity_uo = convert_empty_tonull($_POST['commodity_uo']);
        $commercial_invoice_currency = convert_empty_tonull($_POST['commercial_invoice_currency']);
        $commercial_invoice_amount = convert_empty_tonull($_POST['commercial_invoice_amount']);

        //this edit will only make changes to the invoice specific details, 4 of them
        $sql = "SELECT commodity_quantity, commodity_uo, commercial_invoice_currency, commercial_invoice_amount from import_data where shipping_invoice = :shipping_invoice";
        $stmt = $conn-> prepare($sql);
        $stmt -> bindValue(':shipping_invoice', $shipping_invoice);
        $stmt -> execute();
        $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

        $compare_set = array($commodity_quantity, $commodity_uo, $commercial_invoice_currency, $commercial_invoice_amount);
        if ($shipment) {
            $shipment_keys = array_keys($shipment);
            $shipment['commercial_invoice_amount'] = formatValue($shipment['commercial_invoice_amount']);
            $shipment_values = array_values($shipment);
        }
        $stmt_history -> bindParam(":shipment_details_ref", $shipping_invoice);
        for ($i = 0; $i < count($shipment_keys); $i++) {
            if ($compare_set[$i] !== $shipment_values[$i]) {
                //insert into changes table
                $stmt_history -> bindParam(':column_name', $shipment_keys[$i]);
                $stmt_history -> bindParam(':changed_from', $shipment_values[$i]);
                $stmt_history -> bindParam(':changed_to', $compare_set[$i]);
                $stmt_history -> execute();
            }
        }

        $sql = "UPDATE import_data SET commodity_quantity = :commodity_quantity, commodity_uo = :commodity_uo, commercial_invoice_currency = :commercial_invoice_currency, commercial_invoice_amount = :commercial_invoice_amount WHERE shipping_invoice = :shipping_invoice";
        $stmt = $conn -> prepare($sql);
        $stmt->bindValue(':commodity_quantity', $commodity_quantity);
        $stmt->bindValue(':commodity_uo', $commodity_uo);
        $stmt->bindValue(':commercial_invoice_currency', $commercial_invoice_currency);
        $stmt->bindValue(':commercial_invoice_amount', $commercial_invoice_amount);

        $stmt->bindValue(':shipping_invoice', $shipping_invoice);
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
        exit();
    }