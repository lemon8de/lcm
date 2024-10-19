<?php
require 'db_connection.php';

//on or off, string, whatever i can work with that
$show_active = isset($_GET['show_active']) ? $_GET['show_active'] : 'off';
$month = $_GET['month'];
$year = $_GET['year'];

$sql = "EXEC ActiveAirReport :ActiveOnly, :StartYear, :StartMonth";
$stmt = $conn -> prepare($sql);
$stmt -> bindParam(":ActiveOnly", $show_active);
$stmt -> bindParam(":StartYear", $year);
$stmt -> bindParam(":StartMonth", $month);
$stmt -> execute();

$inner_html = "";
while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    $row['invoice_no'] = str_replace(', ', '<br>', $row['invoice_no']);
    $inner_html .= <<<HTML
        <tr style="border-bottom:1px solid black">
            <td>{$row['forwarder']}</td>
            <td>{$row['origin']}</td>
            <td>{$row['hawb_awb']}</td>
            <td>{$row['eta']}</td>
            <td>{$row['gross_weight']}</td>
            <td>{$row['chargeable_weight']}</td>
            <td>{$row['no_packages']}</td>
            <td class="text-nowrap">{$row['invoice_no']}</td>
            <td>{$row['commodity']}</td>
            <td>{$row['classification']}</td>
            <td>{$row['type_of_expense']}</td>
            <td>{$row['incoterm']}</td>
            <td>{$row['shipment_status']}</td>
            <td>{$row['shipment_status_progress']}</td>
            <td>{$row['tentative_delivery_schedule']}</td>
            <td>{$row['required_delivery']}</td>
            <td>{$row['actual_date_of_delivery']}</td>
            <td>{$row['time_received']}</td>
            <td>{$row['received_by']}</td>
        </tr>
    HTML;
}
if ($data = $stmt -> nextRowset()) {
    $data = $stmt -> fetch(PDO::FETCH_ASSOC);
    if ($show_active == 'on') {
        $inner_html_count = <<<HTML
            <div class="ml-1">
                <div class="bg-warning pl-4 pr-4" style="border-radius:.350rem;padding:0rem .350rem">
                    <h4 style="font-weight:700;line-height:1.5;">{$data['count_total']}<span style="font-size:75%;font-weight:500;">&nbsp;Active</span></h4>
                </div>
            </div>
        HTML;
    } elseif ($show_active == 'off') {
        $inner_html_count = <<<HTML
            <div class="ml-1">
                <div class="bg-info pl-4 pr-4" style="border-radius:.350rem;padding:0rem .350rem">
                    <h4 style="font-weight:700;line-height:1.5;">{$data['count_total']}<span style="font-size:75%;font-weight:500;">&nbsp;Total</span></h4>
                </div>
            </div>
        HTML;
    }
}
$response_body['inner_html_count'] = $inner_html_count;
$response_body['inner_html'] = $inner_html;
echo json_encode($response_body);
exit();