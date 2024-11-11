<?php
    require 'db_connection.php';

    $start_year = $_GET['year'];
    $start_month = $_GET['month'] ?? "";
    $remove_active = isset($_GET['remove_active']) ? $_GET['remove_active'] : 'off';
    if ($start_year == "" || $start_month == "") {
        echo json_encode(['exited' => true]);
        exit();
    }
    $sql = "EXEC AirImportReport :RemoveActive, :StartYear, :StartMonth";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":RemoveActive", $remove_active);
    $stmt -> bindParam(":StartYear", $start_year);
    $stmt -> bindParam(":StartMonth", $start_month);
    $stmt -> execute();

    $return_body = [];
    $inner_html = "";
    $count = 0;
    while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $count++;
        foreach ($row as $key => $value) {
            if (is_numeric($value)) {
                $row[$key] = round((float)$value, 2);
            }
        }
        $inner_html .= <<<HTML
            <tr>
                <td>{$row['forwarder']}</td>
                <td>{$row['origin']}</td>
                <td>{$row['hawb_awb']}</td>
                <td>{$row['eta']}</td>
                <td>{$row['gross_weight']}</td>
                <td>{$row['chargeable_weight']}</td>
                <td>{$row['no_packages']}</td>
                <td>{$row['commodity']}</td>
                <td>{$row['classification']}</td>
                <td>{$row['type_of_expense']}</td>
                <td>{$row['incoterm']}</td>
                <td>{$row['shipment_status']}</td>
                <td>{$row['shipment_status_progress']}</td>
                <td>{$row['tentative_delivery_schedule']}</td>
                <td>{$row['required_delivery']}</td>
                <td>{$row['actual_date_of_delivery']}</td>
                <td>{$row['time_received']}</td>
                <td>{$row['received_by']}</td>
                <td>{$row['shipper']}</td>
                <td>{$row['port']}</td>
                <td>{$row['shipping_invoice']}</td>
                <td>{$row['commodity_quantity']}</td>
                <td>{$row['commodity_uo']}</td>
                <td>{$row['commercial_invoice_currency']}</td>
                <td>{$row['commercial_invoice_amount']}</td>
                <td>{$row['gross_weight']}</td>
                <td>{$row['incoterm']}</td>
                <td>{$row['ip_number']}</td>
                <td>{$row['dr_number']}</td>
                <td>{$row['total_custom_value']}</td>
                <td>{$row['duitable_value']}</td>
                <td>{$row['rate']}</td>
                <td>{$row['customs_duty']}</td>
                <td>{$row['landed_cost']}</td>
                <td>{$row['vat']}</td>
                <td>{$row['bank_charges']}</td>
                <td>{$row['entry_no']}</td>
                <td>{$row['or_number']}</td>
                <td>{$row['assessment_date']}</td>
                <td>{$row['brokerage_fee']}</td>
                <td>{$row['flight_no']}</td>
            </tr>
        HTML;
    }
    $return_body['inner_html'] = $inner_html;
    $return_body['counter'] = <<<HTML
        <div class="ml-1">
            <div class="bg-success pl-4 pr-4" style="border-radius:.350rem;padding:0rem .350rem">
                <h4 style="font-weight:700;line-height:1.5;">{$count}<span style="font-size:75%;font-weight:500;">&nbsp;Invoices</span></h4>
            </div>
        </div>
    HTML;
    echo json_encode($return_body);