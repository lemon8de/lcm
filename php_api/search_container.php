<?php
require 'db_connection.php';
$container = $_GET['container'];
$return_body = [];

$sql = "SELECT shipment_details_ref, bl_number, container from m_shipment_sea_details where container like :container";
$stmt = $conn -> prepare($sql);

$container_wc = $container . '%';
$stmt -> bindParam(':container', $container_wc);
$stmt -> execute();

$inner_html = "";
while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    $inner_html .= <<<HTML
    <tr data-value="{$data['shipment_details_ref']}" id="{$data['container']}" onclick="loaddata(this)" style="cursor:pointer;">
        <!--<td>{$data['bl_number']}</td> -->
        <td>{$data['container']}</td>
        <td>{$data['bl_number']}</td>
    </tr>
    HTML;
}

$return_body['inner_html'] = $inner_html;
echo json_encode($return_body);