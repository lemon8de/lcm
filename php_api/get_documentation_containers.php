<?php
    require 'db_connection.php';
    $bl_number = $_GET['bl_number'];

    $sql = "SELECT container, actual_received_at_falp, shipment_status_percentage, required_delivery_sched, deliver_plan, tabs from m_shipment_sea_details as a left join m_delivery_plan as b on a.shipment_details_ref = b.shipment_details_ref left join m_completion_details as c on a.shipment_details_ref = c.shipment_details_ref where bl_number = :bl_number";
    $stmt_get_containers = $conn -> prepare($sql);
    $stmt_get_containers -> bindParam(":bl_number", $bl_number);
    $stmt_get_containers -> execute();

    $inner_html = "";
    while ($data = $stmt_get_containers -> fetch(PDO::FETCH_ASSOC)) {
        $required_delivery_sched = isset($data['required_delivery_sched']) ? substr($data['required_delivery_sched'], 0, 10) : "TBA";
        $deliver_plan = isset($data['deliver_plan']) ? substr($data['deliver_plan'], 0, 10) : "TBA";
        $tabs = isset($data['tabs']) ? $data['tabs'] : "TBA";
        $received = isset($data['actual_received_at_falp']) ? '<span class="badge badge-success"><i class="fas fa-check"></i></span>' : null;

        $sql = "SELECT shipment_status_percentage, color from m_shipment_status";
        $stmt_callout_colors = $conn -> prepare($sql);
        $stmt_callout_colors -> execute();
        $colors = [];
        // Fetch the data and build the associative array
        while ($row = $stmt_callout_colors->fetch(PDO::FETCH_ASSOC)) {
            $colors[$row['shipment_status_percentage']] = $row['color'];
        }

        $border_color = $colors[$data['shipment_status_percentage'] ?? '0'];
        $container_badge = <<<HTML
            <span style="font-size:100%;border-radius:.25rem;padding:.25em .4em;background-color:{$border_color}">{$data['container']}</span>
        HTML;
        $inner_html .= <<<HTML
            <tr>
                <td style="font-family: monospace;">{$received}&nbsp;{$container_badge}</td>
                <td>{$required_delivery_sched}</td>
                <td>{$deliver_plan}</td>
                <td>{$tabs}</td>
            </tr>
        HTML;
    }

    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);