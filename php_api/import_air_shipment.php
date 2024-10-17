<?php 
    require 'db_connection.php';
    require '../php_static/session_lookup.php';
    //why is there so many of these if we just want csv files smh
    //$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    $csvMimes = array('text/csv', 'application/csv');

    if (!empty($_FILES['import_air_shipment_file']['name']) && in_array($_FILES['import_air_shipment_file']['type'],$csvMimes)) {
        if (is_uploaded_file($_FILES['import_air_shipment_file']['tmp_name'])) {
            //READ FILE
            $csvFile = fopen($_FILES['import_air_shipment_file']['tmp_name'],'r');
            // SKIP FIRST LINE
            $headers = fgets($csvFile);
            // Remove BOM and any non-printable characters
            $headers = preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/', '', $headers);
            $expectedHeaders = "FORWARDER,ORIGIN,HAWB / AWB,ETA,GROSS WEIGHT (KG),CHARGEABLE WEIGHT (KG),NO. OF PKGS,INVOICE NO.,COMMODITY,CLASSIFICATION,INCOTERM,SHIPMENT STATUS,SHIPMENT STATUS PROGRESS,TENTATIVE DELIVERY SCHEDULE,REQUIRED DELIVERY,ACTUAL DATE OF DELIVERY,TIME RECEIVED,RECEIVED BY";

            // Trim any whitespace and compare with expected headers
            if (trim($headers) !== $expectedHeaders) {
                $notification = [
                    "icon" => "error",
                    "text" => "Wrong / Outdated Template File",
                ];
                $_SESSION['notification'] = json_encode($notification);
                //header('location: ../pages/incoming_sea.php');
                header('location: ../pages/add_shipment_air.php');
                exit();
            }
            $updated = 0;
            $created = 0;

            $sql = "SELECT shipment_details_ref from t_shipment_air_details where hawb_awb = :hawb_awb";
            $stmt_duplicate = $conn->prepare($sql);

            $sql1 = "INSERT INTO t_shipment_air_details (shipment_details_ref, forwarder, origin, hawb_awb, eta, gross_weight, chargeable_weight, no_packages, invoice_no, commodity, classification, type_of_expense, incoterm, shipment_status, shipment_status_progress) VALUES (:shipment_details_ref, :forwarder, :origin, :hawb_awb, :eta, :gross_weight, :chargeable_weight, :no_packages, :invoice_no, :commodity, :classification, :type_of_expense, :incoterm, :shipment_status, :shipment_status_progress)";
            $stmt1 = $conn->prepare($sql1);

            $sql2 = "INSERT INTO t_air_delivery_details (shipment_details_ref, tentative_delivery_schedule, required_delivery, actual_date_of_delivery, time_received, received_by) VALUES (:shipment_details_ref, :tentative_delivery_schedule, :required_delivery, :actual_date_of_delivery, :time_received, :received_by)";
            $stmt2 = $conn -> prepare($sql2);

            $sql_update_details = "UPDATE t_shipment_air_details SET forwarder = :forwarder, hawb_awb = hawb_awb,  origin = :origin, eta = :eta, gross_weight = :gross_weight, chargeable_weight = :chargeable_weight, no_packages = :no_packages, commodity = :commodity, classification = :classification, type_of_expense = :type_of_expense, incoterm = :incoterm, shipment_status = :shipment_status, shipment_status_progress = :shipment_status_progress WHERE shipment_details_ref = :shipment_details_ref";
            $stmt_update_details = $conn -> prepare($sql_update_details);

            $sql_update_delivery = "UPDATE t_air_delivery_details SET tentative_delivery_schedule = :tentative_delivery_schedule, required_delivery = :required_delivery, actual_date_of_delivery = :actual_date_of_delivery, time_received = :time_received, received_by = :received_by WHERE shipment_details_ref = :shipment_details_ref";
            $stmt_update_delivery = $conn -> prepare($sql_update_delivery);

            //adding to history query
            $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to, username) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to, :username)";
            $stmt_add_history = $conn -> prepare($sql);
            $stmt_add_history -> bindParam(":username", $_SESSION['username']);
            
            //select the old data for history logging
            $sql_old_details = "SELECT forwarder, origin, hawb_awb, eta, gross_weight, chargeable_weight, no_packages, commodity, classification, type_of_expense, incoterm, shipment_status, shipment_status_progress from t_shipment_air_details where shipment_details_ref = :shipment_details_ref";
            $stmt_old_details = $conn -> prepare($sql_old_details);

            $sql_old_delivery = "SELECT tentative_delivery_schedule, required_delivery, actual_date_of_delivery, time_received, received_by from t_air_delivery_details where shipment_details_ref = :shipment_details_ref";
            $stmt_old_delivery = $conn -> prepare($sql_old_delivery);

            #type_of_expense validation
            $sql_type_of_expense = "SELECT type_of_expense from list_commodity where method = 'air' and classification = :classification";
            $stmt_type_of_expense = $conn -> prepare($sql_type_of_expense);

            $sql_code = "SELECT COUNT(*) FROM t_shipment_air_details WHERE shipment_details_ref = :shipment_details_ref";
            $stmt_code = $conn->prepare($sql_code);

            $sql_duplicate_invoice = "SELECT id from import_data where shipping_invoice = :shipping_invoice";
            $stmt_duplicate_invoice = $conn -> prepare($sql_duplicate_invoice);

            $sql_invoice = "INSERT into import_data (shipment_details_ref, shipping_invoice) values (:shipment_details_ref, :shipping_invoice)";
            $stmt_invoice = $conn -> prepare($sql_invoice);

            //main logic here
            while (($line = fgetcsv($csvFile)) !== false) {
                //get the information in $line
                $forwarder = $line[0] == "" ? null : $line[0];
                $origin = $line[1] == "" ? null : $line[1];
                $hawb_awb = $line[2] == "" ? null : $line[2];
                $gross_weight = $line[4] == "" ? null : $line[4];
                $chargeable_weight = $line[5] == "" ? null : $line[5];
                $no_packages = $line[6] == "" ? null : $line[6];
                $commodity = $line[8] == "" ? null : $line[8];
                $classification = $line[9] == "" ? null : $line[9];
                $incoterm = $line[10] == "" ? null : $line[10];
                $shipment_status = $line[11] == "" ? null : $line[11];
                $shipment_status_progress = $line[12] == "" ? null : $line[12];

                $time_received = $line[16] == "" ? null : $line[16];
                $received_by = $line[17] == "" ? null : $line[17];
                
                $type_of_expense = "CLASSIFICATION FAILURE";

                #date validation
                $eta = $line[3] == "" || $line[3] == 'TBA' ? null : date("Y-m-d", strtotime($line[3]));
                $tentative_delivery_schedule = $line[13] == "" || $line[13] == 'TBA' ? null : date("Y-m-d", strtotime($line[13]));
                $required_delivery = $line[14] == "" || $line[14] == 'TBA' ? null : date("Y-m-d", strtotime($line[14]));
                $actual_date_of_delivery = $line[15] == "" || $line[15] == 'TBA' ? null : date("Y-m-d", strtotime($line[15]));

                #invoice validation
                $commercial_invoice = trim($line[7]);
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

                $stmt_type_of_expense -> bindParam(':classification', $classification);
                $stmt_type_of_expense -> execute();
                if ($data = $stmt_type_of_expense -> fetch(PDO::FETCH_ASSOC)) {
                    $type_of_expense = $data['type_of_expense'];
                }

                //see if its a duplicate
                $stmt_duplicate -> bindValue(':hawb_awb', $hawb_awb);
                $stmt_duplicate -> execute();
                if ($data = $stmt_duplicate -> fetch(PDO::FETCH_ASSOC)) {
                    $updated++;
                    //history logging
                    
                    //get old logs for history logging
                    $stmt_old_details -> bindValue(':shipment_details_ref', $data['shipment_details_ref']);
                    $stmt_old_details -> execute();
                    $shipment = $stmt_old_details->fetch(PDO::FETCH_ASSOC);

                    $compare_set = array($forwarder, $origin, $hawb_awb, $eta, $gross_weight, $chargeable_weight, $no_packages, $commodity, $classification, $type_of_expense, $incoterm, $shipment_status, $shipment_status_progress);
                    if ($shipment) {
                        $shipment_keys = array_keys($shipment);
                        $shipment_values = array_values($shipment);
                    }
                    // compare_set, shipment_keys and shipment_values all have the same length and data format now, we compare
                    $stmt_add_history -> bindValue(':shipment_details_ref', $data['shipment_details_ref']);
                    $stmt_add_history -> bindValue(':table_name', 't_shipment_air_details');

                    for ($i = 0; $i < count($shipment_keys); $i++) {
                        if ($compare_set[$i] !== $shipment_values[$i]) {
                            //insert into changes table
                            $stmt_add_history -> bindParam(':column_name', $shipment_keys[$i]);
                            $stmt_add_history -> bindParam(':changed_from', $shipment_values[$i]);
                            $stmt_add_history -> bindParam(':changed_to', $compare_set[$i]);
                            $stmt_add_history -> execute();
                        }
                    }
                    //now do it again for the second table, fuck me in the asshole
                    //get old logs for history logging
                    $stmt_old_delivery -> bindValue(':shipment_details_ref', $data['shipment_details_ref']);
                    $stmt_old_delivery -> execute();
                    $shipment = $stmt_old_delivery->fetch(PDO::FETCH_ASSOC);

                    $compare_set = array($tentative_delivery_schedule, $required_delivery, $actual_date_of_delivery, $time_received, $received_by);
                    if ($shipment) {
                        $shipment_keys = array_keys($shipment);
                        $shipment_values = array_values($shipment);
                    }
                    // compare_set, shipment_keys and shipment_values all have the same length and data format now, we compare
                    $stmt_add_history -> bindValue(':shipment_details_ref', $data['shipment_details_ref']);
                    $stmt_add_history -> bindValue(':table_name', 't_air_delivery_details');

                    for ($i = 0; $i < count($shipment_keys); $i++) {
                        if ($compare_set[$i] !== $shipment_values[$i]) {
                            //insert into changes table
                            $stmt_add_history -> bindParam(':column_name', $shipment_keys[$i]);
                            $stmt_add_history -> bindParam(':changed_from', $shipment_values[$i]);
                            $stmt_add_history -> bindParam(':changed_to', $compare_set[$i]);
                            $stmt_add_history -> execute();
                        }
                    }

                    $stmt_update_details -> bindParam(':shipment_details_ref', $data['shipment_details_ref']);
                    $stmt_update_details -> bindParam(':forwarder', $forwarder);
                    $stmt_update_details -> bindParam(':origin', $origin);
                    $stmt_update_details -> bindParam(':hawb_awb', $hawb_awb);
                    $stmt_update_details -> bindParam(':eta', $eta);
                    $stmt_update_details -> bindParam(':gross_weight', $gross_weight);
                    $stmt_update_details -> bindParam(':chargeable_weight', $chargeable_weight);
                    $stmt_update_details -> bindParam(':no_packages', $no_packages);
                    $stmt_update_details -> bindParam(':commodity', $commodity);
                    $stmt_update_details -> bindParam(':classification', $classification);
                    $stmt_update_details -> bindParam(':type_of_expense', $type_of_expense);
                    $stmt_update_details -> bindParam(':incoterm', $incoterm);
                    $stmt_update_details -> bindParam(':shipment_status', $shipment_status);
                    $stmt_update_details -> bindParam(':shipment_status_progress', $shipment_status_progress);
                    $stmt_update_details -> execute();

                    $stmt_update_delivery -> bindParam(':shipment_details_ref', $data['shipment_details_ref']);
                    $stmt_update_delivery -> bindParam(':tentative_delivery_schedule', $tentative_delivery_schedule);
                    $stmt_update_delivery -> bindParam(':required_delivery', $required_delivery);
                    $stmt_update_delivery -> bindParam(':actual_date_of_delivery', $actual_date_of_delivery);
                    $stmt_update_delivery -> bindParam(':time_received', $time_received);
                    $stmt_update_delivery -> bindParam(':received_by', $received_by);
                    $stmt_update_delivery -> execute();

                } else {
                    //insert
                    //need to generate a shipment_details_ref code
                    
                    do {
                        // Generate a new unique string
                        $shipment_details_ref = uniqid('air_', true);
                        $stmt_code->bindParam(':shipment_details_ref', $shipment_details_ref);
                        $stmt_code->execute();
                        $count = $stmt_code->fetchColumn();
                    } while ($count > 0);

                    $created++;
                    $stmt1 -> bindParam(':shipment_details_ref', $shipment_details_ref);
                    $stmt1 -> bindParam(':forwarder', $forwarder);
                    $stmt1 -> bindParam(':origin', $origin);
                    $stmt1 -> bindParam(':hawb_awb', $hawb_awb);
                    $stmt1 -> bindParam(':eta', $eta);
                    $stmt1 -> bindParam(':gross_weight', $gross_weight);
                    $stmt1 -> bindParam(':chargeable_weight', $chargeable_weight);
                    $stmt1 -> bindParam(':no_packages', $no_packages);
                    $stmt1 -> bindParam(':invoice_no', $invoice_no);
                    $stmt1 -> bindParam(':commodity', $commodity);
                    $stmt1 -> bindParam(':classification', $classification);
                    $stmt1 -> bindParam(':type_of_expense', $type_of_expense);
                    $stmt1 -> bindParam(':incoterm', $incoterm);
                    $stmt1 -> bindParam(':shipment_status', $shipment_status);
                    $stmt1 -> bindParam(':shipment_status_progress', $shipment_status_progress);
                    $stmt1 -> execute();

                    $stmt2 -> bindParam(':shipment_details_ref', $shipment_details_ref);
                    $stmt2 -> bindParam(':tentative_delivery_schedule', $tentative_delivery_schedule);
                    $stmt2 -> bindParam(':required_delivery', $required_delivery);
                    $stmt2 -> bindParam(':actual_date_of_delivery', $actual_date_of_delivery);
                    $stmt2 -> bindParam(':time_received', $time_received);
                    $stmt2 -> bindParam(':received_by', $received_by);
                    $stmt2 -> execute();

                    //and insert to import data and we are done!
                    //now we need to make the invoices, we can use $fixed_list just because


                    foreach ($fixed_list as $invoice) {
                        $stmt_duplicate_invoice -> bindParam(':shipping_invoice', $invoice);
                        $stmt_duplicate_invoice -> execute();
                        $duplicate = $stmt_duplicate_invoice -> fetch(PDO::FETCH_ASSOC);

                        if (!$duplicate) {
                            $stmt_invoice -> bindParam(':shipment_details_ref', $shipment_details_ref);
                            $stmt_invoice -> bindParam(':shipping_invoice', $invoice);
                            $stmt_invoice -> execute();
                        }
                    }
                }
            }

            fclose($csvFile);
            $notification = [
                "icon" => "success",
                "text" => "File Imported Successfully<br>$updated items updated<br>$created items created",
            ];
            $_SESSION['notification'] = json_encode($notification);
            //header('location: ../pages/incoming_sea.php');
            header('location: ../pages/add_shipment_air.php');
            exit();
        } else {
            //i don't think this will ever happen but sure let it live here
            $notification = [
                "icon" => "warning",
                "text" => "No file uploaded",
            ];
            $_SESSION['notification'] = json_encode($notification);
            //header('location: ../pages/incoming_sea.php');
            header('location: ../pages/add_shipment_air.php');
            exit();
        }
    } else {
        $notification = [
            "icon" => "error",
            "text" => "Uploaded file is not a CSV",
        ];
        $_SESSION['notification'] = json_encode($notification);
        //header('location: ../pages/incoming_sea.php');
        header('location: ../pages/add_shipment_air.php');
        exit();
    }