<?php 
    require 'db_connection.php';
    require '../php_static/session_lookup.php';
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

    if (!empty($_FILES['import_sea_peza_file']['name']) && in_array($_FILES['import_sea_peza_file']['type'],$csvMimes)) {
        if (is_uploaded_file($_FILES['import_sea_peza_file']['tmp_name'])) {
            //READ FILE
            $csvFile = fopen($_FILES['import_sea_peza_file']['tmp_name'],'r');
            // SKIP FIRST LINE
            fgetcsv($csvFile);
            // PARSE
            $lines = 0;
            $updated = 0;
            $skip_lines = 5;

            $sql_check = "SELECT shipment_details_ref from import_data where shipping_invoice = :shipping_invoice";
            $stmt_check = $conn -> prepare($sql_check);

            $sql_update_history = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:shipping_invoice, :table_name, :column_name, :changed_from, :changed_to)";
            $stmt_update_history = $conn -> prepare($sql_update_history);

            $sql_get_details = "SELECT ip_number, gross_weight, total_custom_value, port from import_data where shipping_invoice = :shipping_invoice";
            $stmt_get_details = $conn -> prepare($sql_get_details);

            $sql_get_details_origin_port = "SELECT origin_port from m_shipment_sea_details where shipment_details_ref = :shipment_details_ref";
            $stmt_get_details_origin_port = $conn -> prepare($sql_get_details_origin_port);

            $sql_update = "UPDATE import_data set ip_number = :ip_number, gross_weight = :gross_weight, total_custom_value = :total_custom_value, port = :port where shipping_invoice = :shipping_invoice";
            $stmt_update = $conn -> prepare($sql_update);

            $sql_update_origin_port = "UPDATE m_shipment_sea_details set origin_port = :origin_port where shipment_details_ref = :shipment_details_ref";
            $stmt_update_origin_port = $conn -> prepare($sql_update_origin_port);

            while (($line = fgetcsv($csvFile)) !== false) {
                if ($lines < $skip_lines) {
                    $lines++;
                    continue;
                }

                if (empty(implode('', $line))) {
                    continue; // Skip blank lines
                }

                $precheck = $line[0];
                if ($precheck == "") {
                    continue;
                }
                $lines++;
                $ip_number = $line[4];
                $weight = floatval(str_replace(',', '', $line[14]));
                $total_value = floatval(str_replace(',', '', $line[16]));
                $invoice_no = $line[17];
                $origin = $line[21];
                $port_of_entry = $line[23];
                $shipment_mode = $line[24];

                //echo <<<HTML
                    //<p>{$ip_number} , {$weight} , {$total_value} , {$invoice_no} , {$origin} , {$port_of_entry} , {$shipment_mode}</p>
                //HTML;
                
                $stmt_check -> bindValue(':shipping_invoice', $invoice_no);
                $stmt_check -> execute();
                $has_information = $stmt_check -> fetch(PDO::FETCH_ASSOC);

                if ($has_information) {
                    $updated++;
                    $shipment_details_ref = $has_information['shipment_details_ref'];
                    //proceed update

                    //history logging
                    //unfortunately origin port is straight up in a different dimension, we will have to double up here
                    //grab details and match with the compare_set ALL CHANGES ON IMPORT DATA
                    $stmt_get_details -> bindParam(':shipping_invoice', $invoice_no);
                    $stmt_get_details -> execute();
                    $data = $stmt_get_details -> fetch(PDO::FETCH_ASSOC);
                    if ($data) {
                        $import_data_keys = array_keys($data);
                        $import_data_values = array_values($data);
                        //make the float comparable
                        $import_data_values[1] = round((float)$import_data_values[1], 2);
                        $import_data_values[2] = round((float)$import_data_values[2], 2);
                    }
                    $compare_set = array($ip_number, $weight, $total_value, $port_of_entry);
                    $stmt_update_history -> bindParam(':shipping_invoice', $invoice_no);
                    $stmt_update_history -> bindValue(':table_name', 'import_data');
                    for ($i = 0; $i < count($import_data_keys); $i++) {
                        if ($compare_set[$i] !== $import_data_values[$i]) {
                            $stmt_update_history -> bindParam(':column_name', $import_data_keys[$i]);
                            $stmt_update_history -> bindParam(':changed_from', $import_data_values[$i]);
                            $stmt_update_history -> bindParam(':changed_to', $compare_set[$i]);
                            $stmt_update_history -> execute();
                        }
                    }
                    //special update block for origin_port
                    $stmt_get_details_origin_port -> bindParam(':shipment_details_ref', $shipment_details_ref);
                    $stmt_get_details_origin_port -> execute();
                    $shipment = $stmt_get_details_origin_port -> fetch(PDO::FETCH_ASSOC);
                    if($shipment) {
                        if ($shipment['origin_port'] != $origin) {
                            $stmt_update_history -> bindParam(':shipping_invoice', $shipment_details_ref);
                            $stmt_update_history -> bindValue(':table_name', 'm_shipment_sea_details');
                            $stmt_update_history -> bindValue(':column_name', 'origin_port' );
                            $stmt_update_history -> bindParam(':changed_from', $shipment['origin_port']);
                            $stmt_update_history -> bindParam(':changed_to', $origin);
                            $stmt_update_history -> execute();
                        }
                    }
                    //update history table is now caught up, time to actually update
                    $stmt_update -> bindParam(':ip_number', $ip_number);
                    $stmt_update -> bindParam(':gross_weight', $weight);
                    $stmt_update -> bindParam(':total_custom_value', $total_value);
                    $stmt_update -> bindParam(':port', $port_of_entry);
                    $stmt_update -> bindParam(':shipping_invoice', $invoice_no);
                    $stmt_update -> execute();

                    $stmt_update_origin_port -> bindParam(':origin_port', $origin);
                    $stmt_update_origin_port -> bindParam(':shipment_details_ref', $shipment_details_ref);
                    $stmt_update_origin_port -> execute();

                    //echo <<<HTML
                        //<p>MATCH with {$shipment_details_ref}</p>
                    //HTML;
                } else {
                    //echo <<<HTML
                        //<p>NO MATCH</p>
                    //HTML;
                }
            }
            fclose($csvFile);
            $notification = [
                "icon" => "success",
                "text" => "File Imported Successfully<br> {$lines} items loaded<br>{$updated} matched and updated",
            ];
            $_SESSION['notification'] = json_encode($notification);
            header('location: ../pages/sea_import_details.php');
            exit();
        } else {
            //file not uploaded
            echo 'file not uploaded';
        }
    } else {
        //invalid file format
        echo 'invalid file format';
    }