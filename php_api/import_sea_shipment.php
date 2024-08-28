<?php 
    require 'db_connection.php';
    require '../php_static/session_lookup.php';
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

    if (!empty($_FILES['import_sea_shipment_file']['name']) && in_array($_FILES['import_sea_shipment_file']['type'],$csvMimes)) {
        if (is_uploaded_file($_FILES['import_sea_shipment_file']['tmp_name'])) {
            //READ FILE
            $csvFile = fopen($_FILES['import_sea_shipment_file']['tmp_name'],'r');
            // SKIP FIRST LINE
            fgetcsv($csvFile);
            // PARSE

            $created = 0;
            $updated = 0;

            //TODO move the sql and the prepare out of the loop for optimization, when? never lol i'm bored
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

                //check if this already exists, insert into or update
                //check if the bl_number + container combination exists
                $sql = "SELECT shipment_details_ref from m_shipment_sea_details where bl_number = :bl_number and container = :container";
                $stmt = $conn->prepare($sql);
                $stmt -> bindValue(':bl_number', $bl_number);
                $stmt -> bindValue(':container', $container);
                $stmt -> execute();
                $bl_container_logged = $stmt->fetch(PDO::FETCH_ASSOC);

                //calculate the type of expense using commodity
                $method = 'sea';
                $sql = "SELECT type_of_expense, display_name, classification from list_commodity where display_name = :commodity and method = :method";
                $stmt = $conn->prepare($sql);
                $stmt -> bindValue(':commodity', $commodity_lookup);
                $stmt -> bindValue(':method', $method);
                $stmt -> execute();

                if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) { //get away with the array notice
                    $type_of_expense = $result['type_of_expense'];
                    $commodity = $result['display_name'];
                    $classification = $result['classification'];
                }

                if ($bl_container_logged) {
                    //bl + container is already logged, update code
                    $updated++;
                    $shipment_details_ref = $bl_container_logged['shipment_details_ref'];

                    //update block for updating the main table
                    $sql = "SELECT bl_number, container, container_size, commercial_invoice, commodity, type_of_expense, classification, shipping_lines, forwarder_name, origin_port, shipment_status from m_shipment_sea_details where bl_number = :bl_number and container = :container";

                    $stmt = $conn->prepare($sql);
                    $stmt -> bindValue(':bl_number', $bl_number);
                    $stmt -> bindValue(':container', $container);
                    $stmt -> execute();
                    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

                    $compare_set = array($bl_number, $container, $container_size, $commercial_invoice, $commodity, $type_of_expense, $classification, $shipping_lines, $forwarder_name, $origin_port, $shipment_status);
                    if ($shipment) {
                        $shipment_keys = array_keys($shipment);
                        $shipment_values = array_values($shipment);
                    }

                    // compare_set, shipment_keys and shipment_values all have the same length and data format now, we compare
                    $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
                    $stmt = $conn -> prepare($sql);
                    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
                    $stmt -> bindValue(':table_name', 'm_shipment_sea_details');

                    for ($i = 0; $i < count($shipment_keys); $i++) {
                        if ($compare_set[$i] !== $shipment_values[$i]) {
                            //insert into changes table
                            $stmt -> bindParam(':column_name', $shipment_keys[$i]);
                            $stmt -> bindParam(':changed_from', $shipment_values[$i]);
                            $stmt -> bindParam(':changed_to', $compare_set[$i]);
                            $stmt -> execute();
                        }
                    }
                    //finally update the m_shipment_sea_details table
                    $sql = "UPDATE m_shipment_sea_details set bl_number = :bl_number, container = :container, container_size = :container_size, commercial_invoice = :commercial_invoice, commodity = :commodity, type_of_expense = :type_of_expense, classification = :classification, shipping_lines = :shipping_lines, forwarder_name = :forwarder_name, origin_port = :origin_port, shipment_status = :shipment_status where shipment_details_ref = :shipment_details_ref";
                    $stmt = $conn -> prepare($sql);
                    $stmt -> bindValue(':bl_number', $bl_number);
                    $stmt -> bindValue(':container', $container);
                    $stmt -> bindValue(':container_size', $container_size);
                    $stmt -> bindValue(':commercial_invoice', $commercial_invoice);
                    $stmt -> bindValue(':commodity', $commodity);
                    $stmt -> bindValue(':type_of_expense', $type_of_expense);
                    $stmt -> bindValue(':classification', $classification);
                    $stmt -> bindValue(':shipping_lines', $shipping_lines);
                    $stmt -> bindValue(':forwarder_name', $forwarder_name);
                    $stmt -> bindValue(':origin_port', $origin_port);
                    $stmt -> bindValue(':shipment_status', $shipment_status);
                    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
                    $stmt -> execute();

                    //update code for updating the second table, vessel_details
                    $sql = "SELECT vessel_name, eta_mnl, ata_mnl, atb from m_vessel_details where shipment_details_ref = :shipment_details_ref";
                    $stmt = $conn->prepare($sql);
                    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
                    $stmt -> execute();
                    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

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
                    $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
                    $stmt = $conn -> prepare($sql);
                    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
                    $stmt -> bindValue(':table_name', 'm_vessel_details');

                    for ($i = 0; $i < count($shipment_keys); $i++) {
                        if ($compare_set[$i] !== $shipment_values[$i]) {
                            //insert into changes table
                            $stmt -> bindParam(':column_name', $shipment_keys[$i]);
                            $stmt -> bindParam(':changed_from', $shipment_values[$i]);
                            $stmt -> bindParam(':changed_to', $compare_set[$i]);
                            $stmt -> execute();
                        }
                    }
                    //finally update
                    $sql = "UPDATE m_vessel_details set vessel_name = :vessel_name, eta_mnl = :eta_mnl, ata_mnl = :ata_mnl, atb = :atb where shipment_details_ref = :shipment_details_ref";
                    $stmt = $conn -> prepare($sql);
                    $stmt -> bindValue(':vessel_name', $vessel_name);
                    $stmt -> bindValue(':eta_mnl', $eta_mnl);
                    $stmt -> bindValue(':ata_mnl', $ata_mnl);
                    $stmt -> bindValue(':atb', $atb);
                    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
                    $stmt -> execute();

                } else {
                    //new bl + container, insert into
                    $created++;
                    $sql = "SELECT COUNT(*) FROM m_shipment_sea_details WHERE shipment_details_ref = :shipment_details_ref";
                    $stmt = $conn->prepare($sql);
                    do {
                        // Generate a new unique string
                        $shipment_details_ref = uniqid('sea_', true);
                        $stmt->bindParam(':shipment_details_ref', $shipment_details_ref);
                        $stmt->execute();
                        $count = $stmt->fetchColumn();
                    } while ($count > 0);

                    //insert code in two tables, m_shipment_sea_details and m_vessel_details
                    $confirm_departure = 0;
                    $sql = "INSERT INTO m_shipment_sea_details (shipment_details_ref, bl_number, container, container_size, commercial_invoice, commodity, type_of_expense, classification, shipping_lines, forwarder_name, origin_port, shipment_status, confirm_departure) values (:shipment_details_ref, :bl_number, :container, :container_size, :commercial_invoice, :commodity, :type_of_expense, :classification, :shipping_lines, :forwarder_name, :origin_port, :shipment_status, :confirm_departure)";

                    $stmt = $conn->prepare($sql);
                    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
                    $stmt -> bindValue(':bl_number', $bl_number);
                    $stmt -> bindValue(':container', $container);
                    $stmt -> bindValue(':container_size', $container_size);
                    $stmt -> bindValue(':commercial_invoice', $commercial_invoice);
                    $stmt -> bindValue(':commodity', $commodity);
                    $stmt -> bindValue(':type_of_expense', $type_of_expense);
                    $stmt -> bindValue(':classification', $classification);
                    $stmt -> bindValue(':shipping_lines', $shipping_lines);
                    $stmt -> bindValue(':forwarder_name', $forwarder_name);
                    $stmt -> bindValue(':origin_port', $origin_port);
                    $stmt -> bindValue(':shipment_status', $shipment_status);
                    $stmt -> bindValue(':confirm_departure', $confirm_departure);
                    $stmt -> execute();

                    $sql = "INSERT INTO m_vessel_details (shipment_details_ref, vessel_name, eta_mnl, ata_mnl, atb) values (:shipment_details_ref, :vessel_name, :eta_mnl, :ata_mnl, :atb) ";

                    $stmt = $conn->prepare($sql);
                    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
                    $stmt -> bindValue(':vessel_name', $vessel_name);
                    $stmt -> bindValue(':eta_mnl', $eta_mnl);
                    $stmt -> bindValue(':ata_mnl', $ata_mnl);
                    $stmt -> bindValue(':atb', $atb);

                    $stmt -> execute();
                }
            }
            fclose($csvFile);
            $notification = [
                "icon" => "success",
                "text" => "File Imported Successfully<br>$updated items updated<br>$created items created",
            ];
            $_SESSION['notification'] = json_encode($notification);
            header('location: ../pages/incoming_sea.php');
            exit();
        } else {
            //file not uploaded
        }
    } else {
        //invalid file format
    }