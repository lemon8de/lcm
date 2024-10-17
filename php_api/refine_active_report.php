<?php
    require 'db_connection.php';

    $start_year = $_GET['year'];
    $start_month = $_GET['month'] ?? "";
    if ($start_year == "" || $start_month == "") {
        echo json_encode(['exited' => true]);
        exit();
    }

    $sql = "SELECT a.forwarder_name, b.vessel_name, b.eta_mnl, b.ata_mnl, b.atb, a.bl_number, a.container, f.polytainer_size, f.polytainer_quantity, a.commercial_invoice, a.commodity, c.required_delivery_sched, c.deliver_plan, c.tabs, a.shipment_status, a.origin_port, d.no_days_port, a.type_of_expense from m_shipment_sea_details as a left join m_vessel_details as b on a.shipment_details_ref = b.shipment_details_ref left join m_delivery_plan as c on a.shipment_details_ref = c.shipment_details_ref left join m_mmsystem as d on a.shipment_details_ref = d.shipment_details_ref left join m_completion_details as e on a.shipment_details_ref = e.shipment_details_ref left join m_polytainer_details as f on a.shipment_details_ref = f.shipment_details_ref WHERE a.confirm_departure = '1' and actual_received_at_falp BETWEEN CAST(CONCAT(:start_year, '-', :start_month, '-01') AS DATE) AND EOMONTH(CAST(CONCAT(:start_year2, '-', :start_month2, '-01') AS DATE))";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(':start_year', $start_year);
    $stmt -> bindParam(':start_year2', $start_year);
    $stmt -> bindParam(':start_month', $start_month);
    $stmt -> bindParam(':start_month2', $start_month);
    $stmt -> execute();
    $data = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    $return_body = [];

    $count = count($data);
    $inner_html_summation = <<<HTML
        <div class="ml-1">
            <div class="bg-info pl-4 pr-4" style="border-radius:.350rem;padding:0rem .350rem">
                <h4 style="font-weight:700;line-height:1.5;">{$count}<span style="font-size:75%;font-weight:500;">&nbsp;Total</span></h4>
            </div>
        </div>
    HTML;
    $return_body['inner_html_summation'] = $inner_html_summation;
    $inner_html = "";
    foreach ($data as $row) {
        $row['commercial_invoice'] =$row['commercial_invoice'] == null ? null : str_replace(" ", "<br>", $row['commercial_invoice']);
        $row['eta_mnl'] = $row['eta_mnl'] == null ? null : substr($row['eta_mnl'], 0, 10);
        $row['ata_mnl'] = $row['ata_mnl'] == null ? null : substr($row['ata_mnl'], 0, 10);
        $row['atb'] = $row['atb'] == null ? null : substr($row['atb'], 0, 10);
        $row['required_delivery_sched'] = $row['required_delivery_sched'] == null ? null : substr($row['required_delivery_sched'], 0, 10);
        $row['deliver_plan'] = $row['deliver_plan'] == null ? null : substr($row['deliver_plan'], 0, 10);

        $inner_html .= <<<HTML
            <tr style="border-bottom:1px solid black">
                <td>{$row['forwarder_name']}</td>
                <td>{$row['vessel_name']}</td>
                <td>{$row['eta_mnl']}</td>
                <td>{$row['ata_mnl']}</td>
                <td>{$row['atb']}</td>
                <td>{$row['bl_number']}</td>
                <td>{$row['container']}</td>
                <td>{$row['polytainer_size']}</td>
                <td>{$row['polytainer_quantity']}</td>
                <td>{$row['commercial_invoice']}</td>
                <td>{$row['commodity']}</td>
                <td>{$row['required_delivery_sched']}</td>
                <td>{$row['deliver_plan']}</td>
                <td>{$row['tabs']}</td>
                <td>{$row['shipment_status']}</td>
                <td>{$row['origin_port']}</td>
                <td>{$row['no_days_port']}</td>
                <td>{$row['type_of_expense']}</td>
            </tr>
        HTML;
    }
    $return_body['inner_html'] = $inner_html;
    echo json_encode($return_body);