<?php
    require 'db_connection.php';
    $shipment_details_ref = $_GET['shipment_details_ref'];


    $sql = "SELECT shipping_invoice, shipment_details_ref from import_data where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();

    $inner_html = "";
    while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $inner_html .= <<<HTML
            <tr class="edit_invoice-tab" data-id="{$data['shipment_details_ref']}" id="{$data['shipping_invoice']}" onclick="edit_invoice_focus(this)" style="cursor:pointer;">
                <td style="border-radius:.75em;">{$data['shipping_invoice']}</td>
            </tr>
        HTML;
    }
    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);