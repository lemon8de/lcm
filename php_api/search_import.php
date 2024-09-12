<?php
    require 'db_connection.php';

    $bl_number = $_GET['bl_number'] ?? '';
    $container = $_GET['container'] ?? '';
    $invoice_searched = false;
    $searched_invoices = [];
    $commercial_invoice = $_GET['commercial_invoice'] ?? '';

    if ($bl_number == "" && $container == "" && $commercial_invoice == "") {
        echo json_encode(['search_state' => 'aborted']);
        exit();
    }

    //notes to myself
    //this code has a quirky way of searching, primarily at the invoice input bar
    //the smart way of doing things is to basically query the import data table for shipping invoices if there is content on invoice input.
    //but no, i had to stick to what is already here, which is basically start from m shipment sea details and working my way to import data
    //retards will probably commend the implemented method because it doesn't make another db query, just using what is already here before
    //if you like this, then you might be a retard
    //could i have made a workaround? yes definitely, though I don't know what the interaction would be with the
    //first way of mshipmentseadetails combining with the invoices query
    //anyway, invoice_searched, searched_invoices, and its code components below all use that
    //written here because I will ultimately forget how this spaghetti is made

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
        $invoice_searched = true;
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

                    //addition of invoice searching, if they search for invoice we lock in on that
                    if ($invoice_searched) {
                        if (strpos($match, $commercial_invoice) !== false) {
                            $searched_invoices[] = $match;
                        }
                    }
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
            if ($invoice_searched) {
                if (in_array($row['shipping_invoice'], $searched_invoices)) {
                    $inner_html .= <<<HTML
                        <tr class="modal-trigger" id="{$row['shipping_invoice']}" onclick="load_import_data.call(this)" data-toggle="modal" data-target="#edit_import_data_sea_modal">
                            <td>{$row['shipping_invoice']}</td>
                            <td>{$row['assessment_date']}</td>
                            <td>{$row['ip_number']}</td>
                        </tr>
                    HTML;
                }
            } else {
                $inner_html .= <<<HTML
                    <tr class="modal-trigger" id="{$row['shipping_invoice']}" onclick="load_import_data.call(this)" data-toggle="modal" data-target="#edit_import_data_sea_modal">
                        <td>{$row['shipping_invoice']}</td>
                        <td>{$row['assessment_date']}</td>
                        <td>{$row['ip_number']}</td>
                    </tr>
                HTML;
            }
        }
    }
    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);