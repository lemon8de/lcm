<?php
    require 'db_connection.php';
    if (!isset($_POST['month']) || !isset($_POST['year']) || !isset($_POST['car_model'])) {
        exit();
    }
    $month = $_POST['month'];
    $year = $_POST['year'];
    $car_model = $_POST['car_model'];

    if ($car_model == "None") {
        $car_model = "ALL";
    } else {
        $car_model = "%" . $car_model . "%";
    }

    $sql_data = "EXEC GetOutgoingFSI_LA :StartYear, :StartMonth, :CarModel"; //mssql server stored procedure
    $stmt_data = $conn -> prepare($sql_data);
    $stmt_data -> bindParam(':StartYear', $year);
    $stmt_data -> bindParam(':StartMonth', $month);
    $stmt_data -> bindParam(':CarModel', $car_model);
    $stmt_data -> execute();

    //now we got all relevant data, time to build the json 
    $table = [];
    while ($data = $stmt_data -> fetch(PDO::FETCH_ASSOC)) {
        $found_spot = false;
        foreach ($table as &$spot) {
            //query and put the data inside the right spot
            if ($spot['car_model'] == $data['car_model'] && $spot['vessel_name'] == $data['vessel_name']) {
                //insert this bad boy
                array_push($spot['data'], $data);
                $found_spot = true;
                break;
            }
        }
        unset($spot);
        if (!$found_spot) {
            //no spot found, generate new data point in table
            $new_spot = [
                'car_model' => $data['car_model'],
                'vessel_name' => $data['vessel_name'],
                'data' => [],
            ];
            array_push($new_spot['data'], $data);
            array_push($table, $new_spot);
        }
    }

    header('Content-Type: text/csv');
    $output = fopen('php://output', 'w');

    //file writing here
    foreach ($table as $data) {
        fputcsv($output, [$data['data'][0]['destination'], $data['data'][0]['car_model'], $data['data'][0]['shipping_line'], $data['data'][0]['etd_mnl'], $data['data'][0]['vessel_name']]);
        fputcsv($output, ['VESSEL NAME','INVOICE NO','CONTAINER NO','SHIPPING SEAL','SHIP OUT DATE','DESTINATION','NO OF PALLETS', 'NO OF CARTONS', 'GROSS WEIGHT','CBM']);
        $total_no_pallets = 0;
        $total_gross_weight = 0.0;
        $total_cbm = 0.0;
        $total_no_cartons = 0;
        foreach ($data['data'] as $row) {
            $total_no_pallets += intval($row['no_pallets']);
            $total_no_cartons += intval($row['no_cartons']);
            $total_gross_weight += round(floatval($row['gross_weight']), 4);
            $total_cbm += round(floatval($row['cbm']), 4);

            fputcsv($output, [$row['vessel_name'], $row['invoice_no'], $row['container_no'], $row['shipping_seal'], $row['ship_out_date'], $row['destination_service_center'], $row['no_pallets'], $row['no_cartons'], $row['gross_weight'], $row['cbm']]);
        }
        fputcsv($output, ['total', '', $data['data'][0]['container_size'], '', '', '', $total_no_pallets,$total_no_cartons, $total_gross_weight, $total_cbm]);
        //hopefuly a space
        fputcsv($output, []);
    }
    //end
    fclose($output);