<?php
    require 'db_connection.php';

    $bl_number = $_GET['bl_number'];
    $container = $_GET['container'];
    $commercial_invoice = $_GET['commercial_invoice'];

    // Initialize an array to hold the conditions and parameters
    $conditions = [];
    $params = [];
    // Build the search parameters
    if ($bl_number !== '') {
        $conditions[] = "bl_number LIKE :bl_number";
        $params[':bl_number'] = $bl_number . "%";
    }
    if ($container !== '') {
        $conditions[] = "container LIKE :container";
        $params[':container'] = $container . "%";
    }
    if ($commercial_invoice !== '') {
        $conditions[] = "commercial_invoice LIKE :commercial_invoice";
        $params[':commercial_invoice'] = "%" . $commercial_invoice . "%";
    }
    // Construct the SQL query
    $sql = "SELECT bl_number, container, commercial_invoice, shipment_details_ref FROM m_shipment_sea_details WHERE confirm_departure = 0";
    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }
    $stmt = $conn->prepare($sql);
    // Bind parameters dynamically
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();

    $inner_html = "";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $inner_html .= <<<HTML
            <tr data-value="{$row['container']}" onclick="loaddata(this)" id="{$row['shipment_details_ref']}" class="modal-trigger" data-toggle="modal" data-target="#documentation_view_shipment_sea_modal">
                <td>{$row['bl_number']}</td>
                <td>{$row['container']}</td>
                <td>{$row['commercial_invoice']}</td>
                <td>
                    <form action="../php_api/sea_confirm_departure.php" method="POST">
                        <input type="hidden" readonly value="{$row['shipment_details_ref']}" name="shipment_details_ref">
                        <button type="submit" class="btn bg-primary btn-block">Confirm</button>
                    </form>
                </td>
                <td>
                    <form action="../php_api/sea_delete_departure.php" method="POST">
                        <input type="hidden" readonly value="{$row['shipment_details_ref']}" name="shipment_details_ref">
                        <button type="submit" class="btn bg-danger btn-block">Delete</button>
                    </form>
                </td>
            </tr>
        HTML;
    }
    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);