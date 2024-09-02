<?php
    require 'db_connection.php';
    $shipping_invoice = $_GET['shipping_invoice'];
    $response_body = [];

    $sql = "SELECT container, actual_received_at_falp from m_shipment_sea_details as a left join m_completion_details as b on a.shipment_details_ref = b.shipment_details_ref where commercial_invoice like :shipping_invoice";
    $stmt = $conn -> prepare($sql);
    $shipping_invoice = "%" . $shipping_invoice . "%";
    $stmt -> bindParam(':shipping_invoice', $shipping_invoice);
    $stmt -> execute();
    $conn = null;

    $html = "";
    while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $row['actual_received_at_falp'] = $row['actual_received_at_falp'] ?? 'TBA';
        $row['actual_received_at_falp'] = substr($row['actual_received_at_falp'], 0, 10);

        $html .= <<<HTML
            <tr>
                <td>{$row['container']}</td>
                <td>{$row['actual_received_at_falp']}</td>
            </tr>
        HTML;
    }
    $response_body['html'] = $html;
    echo json_encode($response_body);