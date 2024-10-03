<?php
    require 'db_connection.php';
    $bl_number = $_GET['bl_number'];

    $sql = "SELECT container, required_delivery_sched, deliver_plan, tabs from m_shipment_sea_details as a left join m_delivery_plan as b on a.shipment_details_ref = b.shipment_details_ref where bl_number = :bl_number";
    $stmt_get_containers = $conn -> prepare($sql);
    $stmt_get_containers -> bindParam(":bl_number", $bl_number);
    $stmt_get_containers -> execute();

    $inner_html = "";
    while ($data = $stmt_get_containers -> fetch(PDO::FETCH_ASSOC)) {
        $required_delivery_sched = isset($data['required_delivery_sched']) ? $data['required_delivery_sched'] : "TBA";
        $deliver_plan = isset($data['deliver_plan']) ? $data['deliver_plan'] : "TBA";
        $tabs = isset($data['tabs']) ? $data['deliver_plan'] : "TBA";
        $inner_html .= <<<HTML
            <tr>
                <td>{$data['container']}</td>
                <td>{$required_delivery_sched}</td>
                <td>{$deliver_plan}</td>
                <td>{$tabs}</td>
            </tr>
        HTML;
    }

    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);