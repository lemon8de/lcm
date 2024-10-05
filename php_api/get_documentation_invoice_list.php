<?php
    require 'db_connection.php';
    $bl_number = $_GET['bl_number'];


    $sql = "EXEC GetInvoicebyBL :BLNumber";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(':BLNumber', $bl_number);
    $stmt -> execute();

    $inner_html = "";
    while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $inner_html .= <<<HTML
            <tr class="edit_invoice-tab" id="{$data['shipping_invoice']}" onclick="edit_invoice_focus(this)" style="cursor:pointer;">
                <td style="border-radius:.75em;">{$data['shipping_invoice']}</td>
            </tr>
        HTML;
    }
    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);