<?php
    require 'db_connection.php';
    function convert_empty_tonull ($string) {
        return $string == "" ? null : $string;
    }

    $shipping_invoice = convert_empty_tonull($_POST['shipping_invoice']);
    $shipper = convert_empty_tonull($_POST['shipper']);
    $port = convert_empty_tonull($_POST['port']);
    $commodity_quantity = convert_empty_tonull($_POST['commodity_quantity']);
    $commodity_uo = convert_empty_tonull($_POST['commodity_uo']);
    $commercial_invoice_currency = convert_empty_tonull($_POST['commercial_invoice_currency']);
    $commercial_invoice_amount = convert_empty_tonull($_POST['commercial_invoice_amount']);
    $gross_weight = convert_empty_tonull($_POST['gross_weight']);
    $incoterm = convert_empty_tonull($_POST['incoterm']);
    $etd = convert_empty_tonull($_POST['etd']);
    $ip_number = convert_empty_tonull($_POST['ip_number']);
    $dr_number = convert_empty_tonull($_POST['dr_number']);
    $received_by = convert_empty_tonull($_POST['received_by']);
    $time_received = convert_empty_tonull($_POST['time_received']);
    $total_custom_value = convert_empty_tonull($_POST['total_custom_value']);
    $duitable_value = convert_empty_tonull($_POST['duitable_value']);
    $rate = convert_empty_tonull($_POST['rate']);
    $customs_duty = convert_empty_tonull($_POST['customs_duty']);
    $landed_cost = convert_empty_tonull($_POST['landed_cost']);
    $vat = convert_empty_tonull($_POST['vat']);
    $bank_charges = convert_empty_tonull($_POST['bank_charges']);
    $wharfage = convert_empty_tonull($_POST['wharfage']);
    $arrastre_charges = convert_empty_tonull($_POST['arrastre_charges']);
    $entry_no = convert_empty_tonull($_POST['entry_no']);
    $or_number = convert_empty_tonull($_POST['or_number']);
    $assessment_date = convert_empty_tonull($_POST['assessment_date']);

    //update block for updating the main table
    $sql = "SELECT shipper, port, commodity_quantity, commodity_uo, commercial_invoice_currency, commercial_invoice_amount, gross_weight, incoterm, etd, ip_number, dr_number, received_by, time_received, total_custom_value, duitable_value, rate, customs_duty, landed_cost, vat, bank_charges, wharfage, arrastre_charges, entry_no, or_number, assessment_date from import_data where shipping_invoice = :shipping_invoice";
    $stmt = $conn-> prepare($sql);
    $stmt -> bindValue(':shipping_invoice', $shipping_invoice);
    $stmt -> execute();
    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

    $compare_set = array($shipper, $port, $commodity_quantity, $commodity_uo, $commercial_invoice_currency, $commercial_invoice_amount, $gross_weight, $incoterm, $etd, $ip_number, $dr_number, $received_by, $time_received, $total_custom_value, $duitable_value, $rate, $customs_duty, $landed_cost, $vat, $bank_charges, $wharfage, $arrastre_charges, $entry_no, $or_number, $assessment_date);
    if ($shipment) {
        $shipment_keys = array_keys($shipment);
        $shipment_values = array_values($shipment);
    }

    // compare_set, shipment_keys and shipment_values all have the same length and data format now, we compare
    $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:shipping_invoice, :table_name, :column_name, :changed_from, :changed_to)";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipping_invoice', $shipping_invoice);
    $stmt -> bindValue(':table_name', 'import_data');

    for ($i = 0; $i < count($shipment_keys); $i++) {
        if ($compare_set[$i] !== $shipment_values[$i]) {
            //insert into changes table
            $stmt -> bindParam(':column_name', $shipment_keys[$i]);
            $stmt -> bindParam(':changed_from', $shipment_values[$i]);
            $stmt -> bindParam(':changed_to', $compare_set[$i]);
            $stmt -> execute();
        }
    }

    $sql = "UPDATE import_data SET shipper = :shipper, port = :port, commodity_quantity = :commodity_quantity, commodity_uo = :commodity_uo, commercial_invoice_currency = :commercial_invoice_currency, commercial_invoice_amount = :commercial_invoice_amount, gross_weight = :gross_weight, incoterm = :incoterm, etd = :etd, ip_number = :ip_number, dr_number = :dr_number, received_by = :received_by, time_received = :time_received, total_custom_value = :total_custom_value, duitable_value = :duitable_value, rate = :rate, customs_duty = :customs_duty, landed_cost = :landed_cost, vat = :vat, bank_charges = :bank_charges, wharfage = :wharfage, arrastre_charges = :arrastre_charges, entry_no = :entry_no, or_number = :or_number, assessment_date = :assessment_date WHERE shipping_invoice = :shipping_invoice";
    $stmt = $conn -> prepare($sql);
    $stmt->bindValue(':shipper', $shipper);
    $stmt->bindValue(':port', $port);
    $stmt->bindValue(':commodity_quantity', $commodity_quantity);
    $stmt->bindValue(':commodity_uo', $commodity_uo);
    $stmt->bindValue(':commercial_invoice_currency', $commercial_invoice_currency);
    $stmt->bindValue(':commercial_invoice_amount', $commercial_invoice_amount);
    $stmt->bindValue(':gross_weight', $gross_weight);
    $stmt->bindValue(':incoterm', $incoterm);
    $stmt->bindValue(':etd', $etd);
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
    $stmt->bindValue(':shipping_invoice', $shipping_invoice);
    $stmt -> execute();

    header('location: ../pages/sea_import_details.php');
    exit();