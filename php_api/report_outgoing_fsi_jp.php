<?php
require 'db_connection.php';
$year = $_GET['year'];
$month = $_GET['month'];
$switch_invoice = isset($_GET['switch_invoice']) ? $_GET['switch_invoice'] : null;

$response_body = [];

if ($switch_invoice == null) {
    //generate all available switch invoices this month
    $sql_switch_invoice = "SELECT DISTINCT SUBSTRING( invoice_no, CHARINDEX('-', invoice_no) + 1, CHARINDEX('-', invoice_no, CHARINDEX('-', invoice_no) + 1) - CHARINDEX('-', invoice_no) - 1) AS switch_invoice FROM m_outgoing_fsib WHERE ship_out_date BETWEEN CAST(CONCAT(:start_year, '-', :start_month, '-01') AS DATE) AND EOMONTH(CAST(CONCAT(:start_year2, '-', :start_month2, '-01') AS DATE))";
    $stmt_switch_invoice = $conn -> prepare($sql_switch_invoice);
    $stmt_switch_invoice -> bindParam(":start_month", $month);
    $stmt_switch_invoice -> bindParam(":start_month2", $month);
    $stmt_switch_invoice -> bindParam(":start_year", $year);
    $stmt_switch_invoice -> bindParam(":start_year2", $year);
    $stmt_switch_invoice -> execute();

    $switch_invoice_select = '<option value="">Select Invoice</option>';
    while ($data = $stmt_switch_invoice -> fetch(PDO::FETCH_ASSOC)) {
        $switch_invoice_select .= <<<HTML
            <option>{$data['switch_invoice']}</option>
        HTML;
    }
    $response_body['switch_invoice'] = $switch_invoice_select;
} else {
    //main table building is here, here we have month, year, and switch invoice available
    $switch_invoice = "%" . $switch_invoice . "%";

    $sql_data = "SELECT * from outgoing_fsi_jp_data where invoice_no like :switch_invoice and ship_out_date BETWEEN CAST(CONCAT(:start_year, '-', :start_month, '-01') AS DATE) AND EOMONTH(CAST(CONCAT(:start_year2, '-', :start_month2, '-01') AS DATE))";
    $stmt_data = $conn -> prepare($sql_data);
    $stmt_data -> bindParam(":switch_invoice", $switch_invoice);
    $stmt_data -> bindParam(":start_month", $month);
    $stmt_data -> bindParam(":start_month2", $month);
    $stmt_data -> bindParam(":start_year", $year);
    $stmt_data -> bindParam(":start_year2", $year);
    $stmt_data -> execute();

    //now we got all relevant data, time to build the json 
    $table = [];
    while ($data = $stmt_data -> fetch(PDO::FETCH_ASSOC)) {
        $found_spot = false;
        foreach ($table as &$spot) {
            //query and put the data inside the right spot
            if ($spot['vessel_name'] == $data['vessel_name'] && $spot['destination'] == $data['destination']) {
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
                'vessel_name' => $data['vessel_name'],
                'destination' => $data['destination'],
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
        if (isset($data['data'][0]['etd_mnl'])) {
            $etd_mnl = substr($data['data'][0]['etd_mnl'], 0, 10);
        } else {
            $etd_mnl = "TBA";
        }
        $inner_html .= <<<HTML
            <div class="card card-gray-dark card-outline collapsed collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">{$data['data'][0]['destination']} | {$data['data'][0]['vessel_name']}</h3>
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
                        <th>CNC</th>
                        <th>{$etd_mnl}</th>
                    </tr>
                    <tr>
                        <th>VESSEL NAME</th>
                        <th>INVOICE NO.</th>
                        <th>CONTAINER NO.</th>
                        <th>SHIPPING SEAL</th>
                        <th>SHIP OUT DATE</th>
                        <th>DESTINATION</th>
                        <th>NO. OF PALLETS</th>
                        <th>GROSS WEIGHT</th>
                        <th>CBM</th>
                    </tr>
                </thead>
                <tbody>
        HTML;
        //table content
        $total_no_pallets = 0;
        $total_gross_weight = 0.0;
        $total_cbm = 0.0;
        foreach ($data['data'] as $row) {
            $row['gross_weight'] = round($row['gross_weight'], 4);
            $row['cbm'] = round($row['cbm'], 4);

            $total_no_pallets += intval($row['no_pallets']);
            $total_gross_weight += floatval($row['gross_weight']);
            $total_cbm += floatval($row['cbm']);

            if (isset($row['ship_out_date'])) {
                $ship_out_date = substr($row['ship_out_date'], 0, 10);
            } else {
                $ship_out_date = "TBA";
            }
            $inner_html .= <<<HTML
            <tr>
                <td>{$row['vessel_name']}</td>
                <td>{$row['invoice_no']}</td>
                <td>{$row['container_no']}</td>
                <td>{$row['shipping_seal']}</td>
                <td>{$ship_out_date}</td>
                <td>{$row['destination_service_center']}</td>
                <td>{$row['no_pallets']}</td>
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