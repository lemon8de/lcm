<?php
require 'db_connection.php';
$year = $_GET['year'];
$month = $_GET['month'];


$sql = "EXEC GetInvoiceDataOutgoing :StartYear, :StartMonth";
$stmt = $conn -> prepare($sql);
$stmt -> bindParam(":StartYear", $year);
$stmt -> bindParam(":StartMonth", $month);
$stmt -> execute();

$inner_html = "";
$count = 0;
while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    $count += $data['invoice_amount'];
    $inner_html .= <<<HTML
        <tr>
            <td>{$data['invoice_no']}</td>
            <td>{$data['ship_out_date']}</td>
            <td>{$data['container_no']}</td>
            <td>{$data['bl_date']}</td>
            <td>{$data['pack_qty']}</td>
            <td>{$data['invoice_amount']}</td>
            <td>{$data['vessel_name']}</td>
        </tr>
    HTML;
}
$count = number_format($count);
$inner_html_count = <<<HTML
    <div class="ml-1">
        <div class="bg-success pl-4 pr-4" style="border-radius:.350rem;padding:0rem .350rem">
            <h4 style="font-weight:700;line-height:1.5;">{$count}<span style="font-size:75%;font-weight:500;">&nbsp;JYP</span></h4>
        </div>
    </div>
HTML;
$response_body['inner_html'] = $inner_html;
$response_body['count'] = $inner_html_count;
echo json_encode($response_body);
exit();