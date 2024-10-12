<?php
    require 'db_connection.php';

    $start_year = $_GET['year'];
    $start_month = $_GET['month'] ?? "";
    $remove_active = isset($_GET['remove_active']) ? $_GET['remove_active'] : 'off';
    if ($start_year == "" || $start_month == "") {
        echo json_encode(['exited' => true]);
        exit();
    }

    if ($remove_active == 'on') {
        $sql = "SELECT a.*, b.*, c.*, d.*, e.*, f.*, g.* from import_data as a left join m_shipment_sea_details as b on a.shipment_details_ref = b.shipment_details_ref left join m_vessel_details as c on a.shipment_details_ref = c.shipment_details_ref left join m_delivery_plan as d on a.shipment_details_ref = d.shipment_details_ref left join m_completion_details as e on a.shipment_details_ref = e.shipment_details_ref left join m_polytainer_details as f on a.shipment_details_ref = f.shipment_details_ref left join m_mmsystem as g on a.shipment_details_ref = g.shipment_details_ref WHERE actual_received_at_falp BETWEEN CAST(CONCAT(:start_year, '-', :start_month, '-01') AS DATE) AND EOMONTH(CAST(CONCAT(:start_year2, '-', :start_month2, '-01') AS DATE)) ORDER BY shipping_invoice asc";
    } else {
        $sql = "SELECT a.*, b.*, c.*, d.*, e.*, f.*, g.* from import_data as a left join m_shipment_sea_details as b on a.shipment_details_ref = b.shipment_details_ref left join m_vessel_details as c on a.shipment_details_ref = c.shipment_details_ref left join m_delivery_plan as d on a.shipment_details_ref = d.shipment_details_ref left join m_completion_details as e on a.shipment_details_ref = e.shipment_details_ref left join m_polytainer_details as f on a.shipment_details_ref = f.shipment_details_ref left join m_mmsystem as g on a.shipment_details_ref = g.shipment_details_ref WHERE actual_received_at_falp IS NULL OR actual_received_at_falp BETWEEN CAST(CONCAT(:start_year, '-', :start_month, '-01') AS DATE) AND EOMONTH(CAST(CONCAT(:start_year2, '-', :start_month2, '-01') AS DATE)) ORDER BY shipping_invoice asc";
    }
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(':start_year', $start_year);
    $stmt -> bindParam(':start_year2', $start_year);
    $stmt -> bindParam(':start_month', $start_month);
    $stmt -> bindParam(':start_month2', $start_month);
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
        $row['eta_mnl'] = $row['eta_mnl'] != null ? substr($row['eta_mnl'], 0, 10) : 'TBA';
        $row['ata_mnl'] = $row['ata_mnl'] != null ? substr($row['ata_mnl'], 0, 10) : 'TBA';
        $row['atb'] = $row['atb'] != null ? substr($row['atb'], 0, 10) : 'TBA';
        $row['required_delivery_sched'] = $row['required_delivery_sched'] != null ? substr($row['required_delivery_sched'], 0, 10) : 'TBA';
        $row['deliver_plan'] = $row['deliver_plan'] != null ? substr($row['deliver_plan'], 0, 10) : 'TBA';
        $row['date_port_out'] = $row['date_port_out'] != null ? substr($row['date_port_out'], 0, 10) : 'TBA';
        $row['actual_received_at_falp'] = $row['actual_received_at_falp'] != null ? substr($row['actual_received_at_falp'], 0, 10) : 'TBA';
        $row['etd'] = $row['etd'] != null ? substr($row['etd'], 0, 10) : 'TBA';
        $row['date_return_reused'] = $row['date_return_reused'] != null ? substr($row['date_return_reused'], 0, 10) : 'TBA';
        $row['assessment_date'] = $row['assessment_date'] != null ? substr($row['assessment_date'], 0, 10) : 'TBA';
        $inner_html .= <<<HTML
            <tr id = "{$row['shipping_invoice']}" onclick="show_breakdown.call(this)">
                <td>{$row['shipper']}</td>
                <td>{$row['origin_port']}</td>
                <td>{$row['destination_port']}</td>
                <td>{$row['shipping_invoice']}</td>
                <td>{$row['commodity']}</td>
                <td>{$row['classification']}</td>
                <td>{$row['commodity_quantity']}</td>
                <td>{$row['commodity_uo']}</td>
                <td>{$row['commercial_invoice_currency']}</td>
                <td>{$row['commercial_invoice_amount']}</td>
                <td>{$row['incoterm']}</td>
                <td>{$row['gross_weight']}</td>
                <td>{$row['forwarder_name']}</td>
                <td>{$row['bl_number']}</td>
                <td>{$row['vessel_name']}</td>
                <td>{$row['eta_mnl']}</td>
                <td>{$row['ata_mnl']}</td>
                <td>{$row['atb']}</td>
                <td>{$row['container']}</td>
                <td>{$row['container_size']}</td>
                <td>{$row['shipping_lines']}</td>
                <td>{$row['shipment_status']}</td>
                <td>{$row['required_delivery_sched']}</td>
                <td>{$row['deliver_plan']}</td>
                <td>{$row['tabs']}</td>
                <td>{$row['date_port_out']}</td>
                <td>{$row['actual_received_at_falp']}</td>
                <td>{$row['polytainer_size']}</td>
                <td>{$row['polytainer_quantity']}</td>
                <td>{$row['etd']}</td>
                <td>{$row['container_status']}</td>
                <td>{$row['date_return_reused']}</td>
                <td>{$row['no_days_port']}</td>
                <td>{$row['no_days_falp']}</td>
                <td>{$row['type_of_expense']}</td>
                <td>{$row['ip_number']}</td>
                <td>{$row['dr_number']}</td>
                <td>{$row['received_by']}</td>
                <td>{$row['time_received']}</td>
                <td>{$row['total_custom_value']}</td>
                <td>{$row['duitable_value']}</td>
                <td>{$row['rate']}</td>
                <td>{$row['customs_duty']}</td>
                <td>{$row['landed_cost']}</td>
                <td>{$row['vat']}</td>
                <td>{$row['bank_charges']}</td>
                <td>{$row['wharfage']}</td>
                <td>{$row['arrastre_charges']}</td>
                <td>{$row['entry_no']}</td>
                <td>{$row['or_number']}</td>
                <td>{$row['assessment_date']}</td>
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