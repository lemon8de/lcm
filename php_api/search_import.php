<?php
    require 'db_connection.php';

    $bl_number = $_GET['bl_number'];
    $container = $_GET['container'];
    $commercial_invoice = $_GET['commercial_invoice'];

    if ($bl_number == "" && $container == "" && $commercial_invoice == "") {
        echo json_encode(['search_state' => 'aborted']);
        exit();
    }

    // Initialize an array to hold the conditions and parameters, all this does is grab for us all invoice_data
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
    $sql = "SELECT commercial_invoice FROM m_shipment_sea_details WHERE confirm_departure = 1";
    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }
    $stmt = $conn->prepare($sql);
    // Bind parameters dynamically
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();

    $invoices = [];
    $pattern = '/([A-Za-z0-9-_]+)/';
    $empty = true;
    while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $empty = false;
        $raw_invoice = $data['commercial_invoice'];
        if (preg_match_all($pattern, $raw_invoice, $matches)) {
            foreach ($matches[0] as $match) {
                if (!in_array($match,$invoices)) {
                    $invoices[] = $match;
                }
            }
        } 
    }
    $inner_html = "";
    if (!$empty) {
        //we now have an array of invoices, just use placeholders and query import_data
        $placeholders = rtrim(str_repeat('?,', count($invoices)), ',');
        $sql_import = "SELECT shipping_invoice, assessment_date, ip_number from import_data where shipping_invoice IN ($placeholders) order by id desc";
        $stmt_import = $conn -> prepare($sql_import);
        $stmt_import -> execute($invoices);

        $inner_html = "";
        while ($row = $stmt_import -> fetch(PDO::FETCH_ASSOC)) {
            $inner_html .= <<<HTML
                <tr>
                    <td>{$row['shipping_invoice']}</td>
                    <td>{$row['assessment_date']}</td>
                    <td>{$row['ip_number']}</td>
                </tr>
            HTML;
        }
    }
    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);