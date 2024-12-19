<?php
require 'db_connection.php';
$year = $_GET['year'];
$month = $_GET['month'];
$car_model = isset($_GET['car_model']) ? $_GET['car_model'] : null;
$vessel_name = $_GET['vessel_name'] ?? '';

$response_body = [];

if ($car_model == null) {
    //generate all available switch invoices this month
    $sql_car_model = "EXEC GetCarModel :StartYear, :StartMonth"; //mssql server stored procedure
    $stmt_car_model = $conn -> prepare($sql_car_model);
    $stmt_car_model -> bindParam(':StartYear', $year);
    $stmt_car_model -> bindParam(':StartMonth', $month);
    $stmt_car_model -> execute();

    $car_model_select = '<option value="">Select Model</option>';
    while ($data = $stmt_car_model -> fetch(PDO::FETCH_ASSOC)) {
        if ($data['car_model'] == null) {
            $data['car_model'] = "None";
        }
        $car_model_select .= <<<HTML
            <option>{$data['car_model']}</option>
        HTML;
    }
    $response_body['car_model'] = $car_model_select;
} else {
    //main table building is here, here we have month, year, and switch invoice available
    if ($car_model == "None") {
        $car_model = "ALL";
    } else {
        $car_model = "%" . $car_model . "%";
    }

    $sql_data = "EXEC GetOutgoingFSI_LA :StartYear, :StartMonth, :CarModel, :VesselName"; //mssql server stored procedure
    $stmt_data = $conn -> prepare($sql_data);
    $stmt_data -> bindParam(':StartYear', $year);
    $stmt_data -> bindParam(':StartMonth', $month);
    $stmt_data -> bindParam(':CarModel', $car_model);
    $stmt_data -> bindParam(':VesselName', $vessel_name);
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
    //the table json is complete, generate rendered tables here
    $inner_html = "";
    foreach ($table as $data) {
        //table header
        $inner_html .= <<<HTML
            <div class="card card-gray-dark card-outline collapsed collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">{$data['data'][0]['vessel_name']}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
            <div class="card-body">
            <div class="container">
        HTML;
        $inner_html .= <<<HTML
            <table class="table table-hover mb-4">
                <thead>
                    <tr>
                        <th>{$data['data'][0]['destination']}</th>
                        <th>{$data['data'][0]['car_model']}</th>
                        <th>{$data['data'][0]['shipping_line']}</th>
                        <th>{$data['data'][0]['etd_mnl']}</th>
                    </tr>
                    <tr>
                        <th>VESSEL NAME</th>
                        <th>INVOICE NO.</th>
                        <th>CONTAINER NO.</th>
                        <th>SHIPPING SEAL</th>
                        <th>SHIP OUT DATE</th>
                        <th>DESTINATION</th>
                        <th>NO. OF PALLETS</th>
                        <th>NO. OF CARTONS</th>
                        <th>GROSS WEIGHT</th>
                        <th>CBM</th>
                    </tr>
                </thead>
                <tbody>
        HTML;
        //table content
        $total_no_pallets = 0;
        $total_no_cartons = 0;
        $total_gross_weight = 0.0;
        $total_cbm = 0.0;
        foreach ($data['data'] as $row) {
            $total_no_pallets += intval($row['no_pallets']);
            $total_no_cartons += intval($row['no_cartons']);
            $total_gross_weight += round(floatval($row['gross_weight']), 4);
            $total_cbm += round(floatval($row['cbm']), 4);
            $row['gross_weight'] = round(floatval($row['gross_weight']), 4);
            $row['cbm'] = round(floatval($row['cbm']), 4);
            $inner_html .= <<<HTML
            <tr>
                <td>{$row['vessel_name']}</td>
                <td>{$row['invoice_no']}</td>
                <td>{$row['container_no']}</td>
                <td>{$row['shipping_seal']}</td>
                <td>{$row['ship_out_date']}</td>
                <td>{$row['destination_service_center']}</td>
                <td>{$row['no_pallets']}</td>
                <td>{$row['no_cartons']}</td>
                <td>{$row['gross_weight']}</td>
                <td>{$row['cbm']}</td>
            </tr>
            HTML;
        }
        //table end
        $total_gross_weight = round($total_gross_weight, 4);
        $total_cbm = round($total_cbm, 4);
        $inner_html .= <<<HTML
                    <tr>
                        <td colspan="2">TOTAL</td>
                        <td>{$data['data'][0]['container_size']}</td>
                        <td colspan="3"></td>
                        <td>{$total_no_pallets}</td>
                        <td>{$total_no_cartons}</td>
                        <td>{$total_gross_weight}</td>
                        <td>{$total_cbm}</td>
                    </tr>
                </tbody>
            </table>
        HTML;

        $inner_html .= <<<HTML
                    </div>
                </div>
            </div>
        HTML;
    }
    $response_body['inner_html'] = $inner_html;
}
echo json_encode($response_body);