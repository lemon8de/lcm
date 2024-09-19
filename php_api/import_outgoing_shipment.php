<?php 
    require 'db_connection.php';
    require '../php_static/session_lookup.php';
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

    if (!empty($_FILES['outgoing_shipment_file']['name']) && in_array($_FILES['outgoing_shipment_file']['type'],$csvMimes)) {
        if (is_uploaded_file($_FILES['outgoing_shipment_file']['tmp_name'])) {
            //READ FILE
            $csvFile = fopen($_FILES['outgoing_shipment_file']['tmp_name'],'r');
            // SKIP FIRST LINE
            fgetcsv($csvFile);
            // PARSE

            $created = 0;
            $duplicate = 0;
            $last_invoice = "";

            //list buffer arrays, to get the sum, count, or whatever
            $tw_no = [];
            $invoice_amount = [];

            $sql_insert_fsib = "INSERT into m_outgoing_fsib (outgoing_details_ref, invoice_no, container_no, destination_service_center, destination, car_model, ship_out_date, no_pallets, no_cartons, pack_qty, invoice_amount) values (:outgoing_details_ref, :invoice_no, :container_no, :destination_service_center, :destination, :car_model, :ship_out_date, :no_pallets, :no_cartons, :pack_qty, :invoice_amount); INSERT into m_outgoing_vessel_details (outgoing_details_ref, mode_of_shipment) values (:outgoing_details_ref2, :mode_of_shipment)";
            $stmt_insert_fsib = $conn -> prepare($sql_insert_fsib);

            $sql_update_fsib = "UPDATE m_outgoing_fsib set container_no = :container_no, destination_service_center = :destination_service_center, destination = :destination, car_model = :car_model, ship_out_date = :ship_out_date, no_pallets = :no_pallets, no_cartons = :no_cartons, pack_qty = :pack_qty, invoice_amount = :invoice_amount where invoice_no = :invoice_no; UPDATE m_outgoing_vessel_details set mode_of_shipment = :mode_of_shipment where outgoing_details_ref = :outgoing_details_ref";
            $stmt_update_fsib = $conn -> prepare($sql_update_fsib);

            $sql_uniqueid_duplicate = "SELECT COUNT(id) from m_outgoing_fsib where outgoing_details_ref = :outgoing_details_ref";
            $stmt_uniqueid_duplicate = $conn -> prepare($sql_uniqueid_duplicate);

            $sql_entry_duplicate = "SELECT outgoing_details_ref from m_outgoing_fsib where invoice_no = :invoice_no";
            $stmt_entry_duplicate = $conn -> prepare($sql_entry_duplicate);

            while (($line = fgetcsv($csvFile)) !== false) {
                echo 'started reading';
                if (empty(implode('', $line))) {
                    continue; // Skip blank lines
                }

                //main code here
                $invoice_no = $line[0];
                if ($last_invoice !== $invoice_no && $last_invoice !== "") {
                    $no_pallets = count(array_unique($tw_no));
                    $invoice_amount = array_sum($invoice_amount);

                    //check if there is a duplicate
                    $stmt_entry_duplicate -> bindParam(":invoice_no", $last_invoice);
                    $stmt_entry_duplicate -> execute();

                    //update code does not posses change history logging YET
                    if ($has_duplicate = $stmt_entry_duplicate -> fetch(PDO::FETCH_ASSOC)) {
                        $duplicate++;
                        $stmt_update_fsib -> bindParam(":container_no", $container_no);
                        $stmt_update_fsib -> bindParam(":destination_service_center", $destination_service_center);
                        $stmt_update_fsib -> bindParam(":destination", $destination);
                        $stmt_update_fsib -> bindParam(":car_model", $car_model);
                        $stmt_update_fsib -> bindParam(":ship_out_date", $ship_out_date);
                        $stmt_update_fsib -> bindParam(":no_pallets", $no_pallets);
                        $stmt_update_fsib -> bindParam(":no_cartons", $no_cartons);
                        $stmt_update_fsib -> bindParam(":pack_qty", $pack_qty);
                        $stmt_update_fsib -> bindParam(":invoice_amount", $invoice_amount);
                        $stmt_update_fsib -> bindParam(":invoice_no", $last_invoice);
                        $stmt_update_fsib -> bindParam(":mode_of_shipment", $mode_of_shipment);
                        $stmt_update_fsib -> bindParam(":outgoing_details_ref", $has_duplicate['outgoing_details_ref']);
                        $stmt_update_fsib -> execute();
                    } else {
                        $created++;
                        do {
                            // Generate a new unique string
                            $outgoing_details_ref = uniqid('outgoing_', true);
                            $stmt_uniqueid_duplicate->bindParam(':outgoing_details_ref', $outgoing_details_ref);
                            $stmt_uniqueid_duplicate->execute();
                            $count = $stmt_uniqueid_duplicate->fetchColumn();
                        } while ($count > 0);
    
                        $stmt_insert_fsib -> bindParam(":outgoing_details_ref", $outgoing_details_ref);
                        $stmt_insert_fsib -> bindParam(":outgoing_details_ref2", $outgoing_details_ref);
                        $stmt_insert_fsib -> bindParam(":invoice_no", $last_invoice);
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
                    }

                    $tw_no = [];
                    $invoice_amount = [];
                }
                $last_invoice = $invoice_no;
                array_push($tw_no, $line[3]);
                array_push($invoice_amount, ceil(floatval($line[19])));

                //the good flow should not be this. this occurs every line read, even when we do not plan to do an insert into.
                //no action done to optimize this
                $container_no = $line[1];
                if (strlen(trim($container_no)) == 11) {
                    $mode_of_shipment = "SEA";
                } else if (strlen(trim($container_no)) == 6 || in_array($container_no, ['AIRCRAFT', 'LCL'])) {
                    $mode_of_shipment = "AIR";
                } else {
                    $mode_of_shipment = "TBA";
                }
                $destination_service_center = $line[11];
                $destination = $line[11];
                $no_cartons = 0;
                $pack_qty = 0;
                $car_model = "";// change this to null on insert
                if (in_array($destination_service_center, ['LANGELES1W', 'LANGELES1', 'LONGBEACHW'])) {
                    $car_model = "HONDA";
                } else if ($destination_service_center == "LANGELESW") {
                    $car_model = "SUBARU";
                }
                $ship_out_date = $line[12];
                $ship_out_date = DateTime::createFromFormat('Ymd', $ship_out_date) -> format('Y-m-d');
            }
            //insert the last batch, since we act on invoice change on next line, last entry won't get that event, so after the while loop
            //finish up
            //dirty i know but it has to be this way i am so sorry
            $no_pallets = count(array_unique($tw_no));
            $invoice_amount = array_sum($invoice_amount);

            //check if there is a duplicate
            $stmt_entry_duplicate -> bindParam(":invoice_no", $last_invoice);
            $stmt_entry_duplicate -> execute();

            if ($has_duplicate = $stmt_entry_duplicate -> fetch(PDO::FETCH_ASSOC)) {
                $duplicate++;
                $stmt_update_fsib -> bindParam(":container_no", $container_no);
                $stmt_update_fsib -> bindParam(":destination_service_center", $destination_service_center);
                $stmt_update_fsib -> bindParam(":destination", $destination);
                $stmt_update_fsib -> bindParam(":car_model", $car_model);
                $stmt_update_fsib -> bindParam(":ship_out_date", $ship_out_date);
                $stmt_update_fsib -> bindParam(":no_pallets", $no_pallets);
                $stmt_update_fsib -> bindParam(":no_cartons", $no_cartons);
                $stmt_update_fsib -> bindParam(":pack_qty", $pack_qty);
                $stmt_update_fsib -> bindParam(":invoice_amount", $invoice_amount);
                $stmt_update_fsib -> bindParam(":invoice_no", $last_invoice);
                $stmt_update_fsib -> bindParam(":mode_of_shipment", $mode_of_shipment);
                $stmt_update_fsib -> bindParam(":outgoing_details_ref", $has_duplicate['outgoing_details_ref']);
                $stmt_update_fsib -> execute();
            } else {
                $created++;
                do {
                    // Generate a new unique string
                    $outgoing_details_ref = uniqid('outgoing_', true);
                    $stmt_uniqueid_duplicate->bindParam(':outgoing_details_ref', $outgoing_details_ref);
                    $stmt_uniqueid_duplicate->execute();
                    $count = $stmt_uniqueid_duplicate->fetchColumn();
                } while ($count > 0);

                $stmt_insert_fsib -> bindParam(":outgoing_details_ref", $outgoing_details_ref);
                $stmt_insert_fsib -> bindParam(":outgoing_details_ref2", $outgoing_details_ref);
                $stmt_insert_fsib -> bindParam(":invoice_no", $last_invoice);
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
            }

            fclose($csvFile);
            $notification = [
                "icon" => "success",
                "text" => "File Imported Successfully<br>$created new data points<br>updated $duplicate duplicates",
            ];
            $_SESSION['notification'] = json_encode($notification);
            header('location: ../pages/add_outgoing.php');
            exit();
        } else {
            //file not uploaded
        }
    } else {
        //invalid file format
    }