<?php
    require 'db_connection.php';
    $bl_number = $_GET['bl_number'];


    $sql = "SELECT shipment_details_ref, container from m_shipment_sea_details where bl_number = :bl_number";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(':bl_number', $bl_number);
    $stmt -> execute();

    $inner_html = "";
    while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $inner_html .= <<<HTML
            <tr class="edit_container-tab" id="{$data['shipment_details_ref']}" onclick="edit_container_focus(this)" style="cursor:pointer;">
                <td style="border-radius:.75em;">{$data['container']}</td>
            </tr>
        HTML;
    }
    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);