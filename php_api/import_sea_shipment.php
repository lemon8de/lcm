<?php 
    require 'db_connection.php';
    require '../php_static/session_lookup.php';
    //why is there so many of these if we just want csv files smh
    //$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    $csvMimes = array('text/csv', 'application/csv');

    if (!empty($_FILES['import_sea_shipment_file']['name']) && in_array($_FILES['import_sea_shipment_file']['type'],$csvMimes)) {
        if (is_uploaded_file($_FILES['import_sea_shipment_file']['tmp_name'])) {
            //READ FILE
            $csvFile = fopen($_FILES['import_sea_shipment_file']['tmp_name'],'r');
            // SKIP FIRST LINE
            $headers = fgets($csvFile);
            // Remove BOM and any non-printable characters
            $headers = preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/', '', $headers);
            $expectedHeaders = "NO,Vessel Name,ETA MNL (YYYY/MM/DD),ATA MNL  (YYYY/MM/DD),ATB  (YYYY/MM/DD),BL NUMBER,CONTAINER,CONTAINER SIZE / CBM,COMMERCIAL INVOICE,COMMODITY,SHIPPING LINES,FORWARDER'S NAME,ORIGIN,SHIPMENT STATUS,DESTINATION PORT,TSAD NUMBER";

            // Trim any whitespace and compare with expected headers
            if (trim($headers) !== $expectedHeaders) {
                $notification = [
                    "icon" => "error",
                    "text" => "Wrong / Outdated Template File",
                ];
                $_SESSION['notification'] = json_encode($notification);
                //header('location: ../pages/incoming_sea.php');
                header('location: ../pages/add_shipment_sea.php');
                exit();
            }

            $created = 0;
            $updated = 0;
            //check if this already exists, insert into or update
            //check if the bl_number + container combination exists
            $sql = "SELECT shipment_details_ref from m_shipment_sea_details where bl_number = :bl_number and container = :container";
            $stmt_duplicate = $conn->prepare($sql);

            //list commodity masterlist
            $sql = "SELECT type_of_expense, display_name, classification from list_commodity where display_name = :commodity and method = :method";
            $stmt_commodity = $conn->prepare($sql);
            $method = 'sea';

            //select query for comparing new and old values for history logging
            $sql = "SELECT bl_number, container, container_size, commercial_invoice, commodity, type_of_expense, classification, shipping_lines, forwarder_name, origin_port, shipment_status, destination_port, tsad_number from m_shipment_sea_details where bl_number = :bl_number and container = :container";
            $stmt_old_log = $conn->prepare($sql);

            //adding to history query
            $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
            $stmt_add_history = $conn -> prepare($sql);

            //updating query
            $sql = "UPDATE m_shipment_sea_details set bl_number = :bl_number, container = :container, container_size = :container_size, commercial_invoice = :commercial_invoice, commodity = :commodity, type_of_expense = :type_of_expense, classification = :classification, shipping_lines = :shipping_lines, forwarder_name = :forwarder_name, origin_port = :origin_port, destination_port = :destination_port, shipment_status = :shipment_status, tsad_number = :tsad_number where shipment_details_ref = :shipment_details_ref";
            $stmt_update_shipment_sea_details = $conn -> prepare($sql);

            //select query for the vessel details table
            $sql = "SELECT vessel_name, eta_mnl, ata_mnl, atb from m_vessel_details where shipment_details_ref = :shipment_details_ref";
            $stmt_old_vessel_log = $conn->prepare($sql);

            //updating query for vessel details
            $sql = "UPDATE m_vessel_details set vessel_name = :vessel_name, eta_mnl = :eta_mnl, ata_mnl = :ata_mnl, atb = :atb where shipment_details_ref = :shipment_details_ref";
            $stmt_update_vessel_details = $conn -> prepare($sql);

            //used to recalculate no days
            $sql = "SELECT date_port_out from m_completion_details where shipment_details_ref = :shipment_details_ref";
            $stmt_check_days = $conn -> prepare($sql);
            $sql = "UPDATE m_mmsystem set no_days_port = :no_days_port where shipment_details_ref = :shipment_details_ref";
            $stmt_update_mm = $conn -> prepare($sql);

            $sql = "SELECT COUNT(*) FROM m_shipment_sea_details WHERE shipment_details_ref = :shipment_details_ref";
            $stmt_check_duplicate_ref = $conn->prepare($sql);

            $confirm_departure = 0;
            $sql = "INSERT INTO m_shipment_sea_details (shipment_details_ref, bl_number, container, container_size, commercial_invoice, commodity, type_of_expense, classification, shipping_lines, forwarder_name, origin_port, destination_port, shipment_status, tsad_number, confirm_departure) values (:shipment_details_ref, :bl_number, :container, :container_size, :commercial_invoice, :commodity, :type_of_expense, :classification, :shipping_lines, :forwarder_name, :origin_port, :destination_port, :shipment_status, :tsad_number, :confirm_departure)";
            $stmt_insert_shipment_details = $conn->prepare($sql);

            $sql = "INSERT INTO m_vessel_details (shipment_details_ref, vessel_name, eta_mnl, ata_mnl, atb) values (:shipment_details_ref, :vessel_name, :eta_mnl, :ata_mnl, :atb) ";
            $stmt_insert_vessel_details = $conn->prepare($sql);

            while (($line = fgetcsv($csvFile)) !== false) {
                // Check if the row is blank or consists only of whitespace
                if (empty(implode('', $line))) {
                    continue; // Skip blank lines
                }
                $type_of_expense = "MASTERLIST FAILURE";
                $commodity = "MASTERLIST FAILURE";
                $classification = "MASTERLIST FAILURE";
                $vessel_name = $line[1];

                //date parsing, and null defaults on some cases
                $eta_mnl = $line[2] == "" || $line[2] == 'TDB' ? null : date("Y-m-d", strtotime($line[2]));
                $ata_mnl = $line[3] == "" || $line[3] == 'TDB' ? null : date("Y-m-d", strtotime($line[3]));
                $atb = $line[4] == "" || $line[4] == 'TDB' ? null : date("Y-m-d", strtotime($line[4]));

                $bl_number = $line[5];
                $container = $line[6];
                $container_size = $line[7];
                $commercial_invoice = $line[8];
                $commodity_lookup = $line[9];
                $shipping_lines = $line[10];
                $forwarder_name = $line[11];
                $origin_port = $line[12];
                $shipment_status = $line[13];
                $destination_port = $line[14];
                $tsad_number = $line[15];

                //check if the bl_number + container combination exists
                $stmt_duplicate -> bindValue(':bl_number', $bl_number);
                $stmt_duplicate -> bindValue(':container', $container);
                $stmt_duplicate -> execute();
                $bl_container_logged = $stmt_duplicate -> fetch(PDO::FETCH_ASSOC);

                //get the commodity details
                $stmt_commodity -> bindValue(':commodity', $commodity_lookup);
                $stmt_commodity -> bindValue(':method', $method);
                $stmt_commodity -> execute();

                if ($result = $stmt_commodity -> fetch(PDO::FETCH_ASSOC)) { //get away with the array notice
                    $type_of_expense = $result['type_of_expense'];
                    $commodity = $result['display_name'];
                    $classification = $result['classification'];
                }

                if ($bl_container_logged) {
                    //bl + container is already logged, update code
                    $updated++;
                    $shipment_details_ref = $bl_container_logged['shipment_details_ref'];

                    //get old logs for history logging
                    $stmt_old_log -> bindValue(':bl_number', $bl_number);
                    $stmt_old_log -> bindValue(':container', $container);
                    $stmt_old_log -> execute();
                    $shipment = $stmt_old_log->fetch(PDO::FETCH_ASSOC);

                    $compare_set = array($bl_number, $container, $container_size, $commercial_invoice, $commodity, $type_of_expense, $classification, $shipping_lines, $forwarder_name, $origin_port, $shipment_status, $destination_port, $tsad_number);
                    if ($shipment) {
                        $shipment_keys = array_keys($shipment);
                        $shipment_values = array_values($shipment);
                    }

                    // compare_set, shipment_keys and shipment_values all have the same length and data format now, we compare
                    $stmt_add_history -> bindValue(':shipment_details_ref', $shipment_details_ref);
                    $stmt_add_history -> bindValue(':table_name', 'm_shipment_sea_details');

                    for ($i = 0; $i < count($shipment_keys); $i++) {
                        if ($compare_set[$i] !== $shipment_values[$i]) {
                            //insert into changes table
                            $stmt_add_history -> bindParam(':column_name', $shipment_keys[$i]);
                            $stmt_add_history -> bindParam(':changed_from', $shipment_values[$i]);
                            $stmt_add_history -> bindParam(':changed_to', $compare_set[$i]);
                            $stmt_add_history -> execute();
                        }
                    }
                    //finally update the m_shipment_sea_details table
                    $stmt_update_shipment_sea_details -> bindValue(':bl_number', $bl_number);
                    $stmt_update_shipment_sea_details -> bindValue(':container', $container);
                    $stmt_update_shipment_sea_details -> bindValue(':container_size', $container_size);
                    $stmt_update_shipment_sea_details -> bindValue(':commercial_invoice', $commercial_invoice);
                    $stmt_update_shipment_sea_details -> bindValue(':commodity', $commodity);
                    $stmt_update_shipment_sea_details -> bindValue(':type_of_expense', $type_of_expense);
                    $stmt_update_shipment_sea_details -> bindValue(':classification', $classification);
                    $stmt_update_shipment_sea_details -> bindValue(':shipping_lines', $shipping_lines);
                    $stmt_update_shipment_sea_details -> bindValue(':forwarder_name', $forwarder_name);
                    $stmt_update_shipment_sea_details -> bindValue(':origin_port', $origin_port);
                    $stmt_update_shipment_sea_details -> bindValue(':destination_port', $destination_port);
                    $stmt_update_shipment_sea_details -> bindValue(':shipment_status', $shipment_status);
                    $stmt_update_shipment_sea_details -> bindValue(':tsad_number', $tsad_number);
                    $stmt_update_shipment_sea_details -> bindValue(':shipment_details_ref', $shipment_details_ref);
                    $stmt_update_shipment_sea_details -> execute();

                    //update code for updating the second table, vessel_details
                    $stmt_old_vessel_log -> bindValue(':shipment_details_ref', $shipment_details_ref);
                    $stmt_old_vessel_log -> execute();
                    $shipment = $stmt_old_vessel_log->fetch(PDO::FETCH_ASSOC);

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
                    $stmt_add_history -> bindValue(':shipment_details_ref', $shipment_details_ref);
                    $stmt_add_history -> bindValue(':table_name', 'm_vessel_details');

                    for ($i = 0; $i < count($shipment_keys); $i++) {
                        if ($compare_set[$i] !== $shipment_values[$i]) {
                            //insert into changes table
                            $stmt_add_history -> bindParam(':column_name', $shipment_keys[$i]);
                            $stmt_add_history -> bindParam(':changed_from', $shipment_values[$i]);
                            $stmt_add_history -> bindParam(':changed_to', $compare_set[$i]);
                            $stmt_add_history -> execute();
                        }
                    }
                    //finally update
                    $stmt_update_vessel_details -> bindValue(':vessel_name', $vessel_name);
                    $stmt_update_vessel_details -> bindValue(':eta_mnl', $eta_mnl);
                    $stmt_update_vessel_details -> bindValue(':ata_mnl', $ata_mnl);
                    $stmt_update_vessel_details -> bindValue(':atb', $atb);
                    $stmt_update_vessel_details -> bindValue(':shipment_details_ref', $shipment_details_ref);
                    $stmt_update_vessel_details -> execute();

                    //no of days updating, calculation of days at port
                    //uses m_completion_details - date_port_out; m_vessel_details - atb
                    $stmt_check_days -> bindParam(':shipment_details_ref', $shipment_details_ref);
                    $stmt_check_days -> execute();
                    if ($mm_detail = $stmt_check_days -> fetch(PDO::FETCH_ASSOC)) {
                        //this if statement actually filters for confirmed shipments lol
                        //notes that $atb might be null, the computation is atb - date port out + 1
                        // Create DateTime objects
                        $dateTime1 = $atb == null ? null : new DateTime($atb);
                        $dateTime2 = $mm_detail['date_port_out'] == null ? null : new DateTime($mm_detail['date_port_out']);

                        // Calculate the difference
                        if ($dateTime1 and $dateTime2) {
                            $interval = $dateTime1->diff($dateTime2);
                            $differenceInDays = $interval->days + 1;
                        } else {
                            $differenceInDays = 0;
                        }
                        //insert this bad boy, fuck history we don't need that shit here
                        $stmt_update_mm -> bindParam(':no_days_port', $differenceInDays);
                        $stmt_update_mm -> bindParam(':shipment_details_ref', $shipment_details_ref);
                        $stmt_update_mm -> execute();
                    }

                } else {
                    //new bl + container, insert into
                    $created++;
                    do {
                        // Generate a new unique string
                        $shipment_details_ref = uniqid('sea_', true);
                        $stmt_check_duplicate_ref->bindParam(':shipment_details_ref', $shipment_details_ref);
                        $stmt_check_duplicate_ref->execute();
                        $count = $stmt_check_duplicate_ref->fetchColumn();
                    } while ($count > 0);

                    //insert code in two tables, m_shipment_sea_details and m_vessel_details
                    $stmt_insert_shipment_details -> bindValue(':shipment_details_ref', $shipment_details_ref);
                    $stmt_insert_shipment_details -> bindValue(':bl_number', $bl_number);
                    $stmt_insert_shipment_details -> bindValue(':container', $container);
                    $stmt_insert_shipment_details -> bindValue(':container_size', $container_size);
                    $stmt_insert_shipment_details -> bindValue(':commercial_invoice', $commercial_invoice);
                    $stmt_insert_shipment_details -> bindValue(':commodity', $commodity);
                    $stmt_insert_shipment_details -> bindValue(':type_of_expense', $type_of_expense);
                    $stmt_insert_shipment_details -> bindValue(':classification', $classification);
                    $stmt_insert_shipment_details -> bindValue(':shipping_lines', $shipping_lines);
                    $stmt_insert_shipment_details -> bindValue(':forwarder_name', $forwarder_name);
                    $stmt_insert_shipment_details -> bindValue(':origin_port', $origin_port);
                    $stmt_insert_shipment_details -> bindValue(':destination_port', $destination_port);
                    $stmt_insert_shipment_details -> bindValue(':shipment_status', $shipment_status);
                    $stmt_insert_shipment_details -> bindValue(':tsad_number', $tsad_number);
                    $stmt_insert_shipment_details -> bindValue(':confirm_departure', $confirm_departure);
                    $stmt_insert_shipment_details -> execute();

                    $stmt_insert_vessel_details -> bindValue(':shipment_details_ref', $shipment_details_ref);
                    $stmt_insert_vessel_details -> bindValue(':vessel_name', $vessel_name);
                    $stmt_insert_vessel_details -> bindValue(':eta_mnl', $eta_mnl);
                    $stmt_insert_vessel_details -> bindValue(':ata_mnl', $ata_mnl);
                    $stmt_insert_vessel_details -> bindValue(':atb', $atb);
                    $stmt_insert_vessel_details -> execute();
                }
            }
            fclose($csvFile);
            $notification = [
                "icon" => "success",
                "text" => "File Imported Successfully<br>$updated items updated<br>$created items created",
            ];
            $_SESSION['notification'] = json_encode($notification);
            //header('location: ../pages/incoming_sea.php');
            header('location: ../pages/add_shipment_sea.php');
            exit();
        } else {
            //i don't think this will ever happen but sure let it live here
            $notification = [
                "icon" => "warning",
                "text" => "No file uploaded",
            ];
            $_SESSION['notification'] = json_encode($notification);
            //header('location: ../pages/incoming_sea.php');
            header('location: ../pages/add_shipment_sea.php');
            exit();
        }
    } else {
        $notification = [
            "icon" => "error",
            "text" => "Uploaded file is not a CSV",
        ];
        $_SESSION['notification'] = json_encode($notification);
        //header('location: ../pages/incoming_sea.php');
        header('location: ../pages/add_shipment_sea.php');
        exit();
    }