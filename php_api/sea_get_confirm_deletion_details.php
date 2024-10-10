<?php
    require 'db_connection.php';
    $bl_numbers = $_GET['bl_numbers'];

    $placeholders = rtrim(str_repeat('?,', count($bl_numbers)), ',');
    $sql_check = "SELECT * from ConfirmDeletionView where bl_number in ($placeholders)";
    $stmt_check = $conn -> prepare($sql_check);
    $stmt_check -> execute($bl_numbers);

    $sql = "SELECT shipment_status_percentage, color from m_shipment_status";
    $stmt_callout_colors = $conn -> prepare($sql);
    $stmt_callout_colors -> execute();
    $colors = [];

    // Fetch the data and build the associative array
    while ($row = $stmt_callout_colors->fetch(PDO::FETCH_ASSOC)) {
        $colors[$row['shipment_status_percentage']] = $row['color'];
    }

    $inner_html = "";
    while ($data = $stmt_check -> fetch(PDO::FETCH_ASSOC)) {
        $border_color = $colors[$data['shipment_status_percentage']];
        $inner_html .= <<<HTML
            <tr>
                <td>
                    {$data['bl_number']}
                </td>
                <td>
                    {$data['container']}
                </td>
                <td>
                    <span style="font-size:75%;font-weight:700;border-radius:.25rem;padding:.25em .4em;background-color:{$border_color}">{$data['shipment_status']}</span>
                </td>
                <td>
                    {$data['confirm_departure']}
                </td>
            </tr>
        HTML;
    }
    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);