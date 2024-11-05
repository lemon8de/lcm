<?php
    require 'db_connection.php';
    //$sql = "SELECT a.vessel_name, b.shipment_status, b.origin_port, a.eta_mnl, c.etd, d.deliver_plan from m_vessel_details as a left join m_shipment_sea_details as b on a.shipment_details_ref = b.shipment_details_ref left join m_polytainer_details as c on a.shipment_details_ref = c.shipment_details_ref left join m_delivery_plan as d on a.shipment_details_ref = d.shipment_details_ref order by a.vessel_name desc";

    $start_year = $_GET['year'];
    $start_month = $_GET['month'] ?? "";
    $remove_active = isset($_GET['remove_active']) ? $_GET['remove_active'] : 'off';
    if ($start_year == "" || $start_month == "") {
        echo json_encode(['exited' => true]);
        exit();
    }

    //additions sep5: force the query to only show one copy of the vessel, origin port and shipment status is set to max to resolve having multiple results on that. no idea how this will go. :)
    //this new file has some filtering involved, and should still be similar nonetheless
    //this seriously need a distinct, because if this vessel name appears again in the near future this report is fucked
    //distinct will fix it fine, already tested
    //now distinct, extra code will be added on the for loop if the vessel name would duplicate
    //remove active addition
    

    //revision october 1: why isthere a.id on these? assumed oversight and changed
    if ($remove_active == 'on') {
        $sql = "SELECT distinct a.vessel_name, b.origin_port, a.eta_mnl, c.etd, d.deliver_plan FROM m_vessel_details AS a LEFT JOIN m_shipment_sea_details AS b ON a.shipment_details_ref = b.shipment_details_ref LEFT JOIN m_polytainer_details AS c ON a.shipment_details_ref = c.shipment_details_ref LEFT JOIN m_delivery_plan AS d ON a.shipment_details_ref = d.shipment_details_ref LEFT JOIN m_completion_details as e ON a.shipment_details_ref = e.shipment_details_ref WHERE actual_received_at_falp BETWEEN CAST(CONCAT(:start_year, '-', :start_month, '-01') AS DATE) AND EOMONTH(CAST(CONCAT(:start_year2, '-', :start_month2, '-01') AS DATE)) ORDER BY vessel_name asc";
    } else {
        $sql = "SELECT distinct a.vessel_name, b.origin_port, a.eta_mnl, c.etd, d.deliver_plan FROM m_vessel_details AS a LEFT JOIN m_shipment_sea_details AS b ON a.shipment_details_ref = b.shipment_details_ref LEFT JOIN m_polytainer_details AS c ON a.shipment_details_ref = c.shipment_details_ref LEFT JOIN m_delivery_plan AS d ON a.shipment_details_ref = d.shipment_details_ref LEFT JOIN m_completion_details as e ON a.shipment_details_ref = e.shipment_details_ref WHERE actual_received_at_falp IS NULL OR actual_received_at_falp BETWEEN CAST(CONCAT(:start_year, '-', :start_month, '-01') AS DATE) AND EOMONTH(CAST(CONCAT(:start_year2, '-', :start_month2, '-01') AS DATE)) ORDER BY a.vessel_name asc";
    }
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(':start_year', $start_year);
    $stmt -> bindParam(':start_year2', $start_year);
    $stmt -> bindParam(':start_month', $start_month);
    $stmt -> bindParam(':start_month2', $start_month);
    $stmt -> execute();
    
    $sql_main = "SELECT a.shipment_details_ref, a.commodity, c.polytainer_size, c.polytainer_quantity from m_shipment_sea_details as a join m_vessel_details as b on a.shipment_details_ref = b.shipment_details_ref join m_polytainer_details as c on a.shipment_details_ref = c.shipment_details_ref where b.vessel_name = :vessel_name and (b.eta_mnl = :eta_mnl or b.eta_mnl is null) and (c.etd = :etd or c.etd is null) and origin_port = :origin";
    $stmt_main = $conn -> prepare($sql_main);
    
    $inner_html = "";
    $final_html = "";
    $count = 0;
    $green_total_count = 0;
    $gray_total_count = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $origin = $row['origin_port'];
        $q_eta_mnl = $row['eta_mnl']; //save this for the query below, the TBA change should be done for the displaying only
        $q_etd = $row['etd']; //save this for the query below, the TBA change should be done for the displaying only

        $row['etd'] = $row['etd'] == null ? 'TBA' : date('Y/m/d', strtotime($row['etd']));
        $row['eta_mnl'] = $row['eta_mnl'] == null ? 'TBA' : date('Y/m/d', strtotime($row['eta_mnl']));
        $transit_start = strtotime($row['eta_mnl']);
        $transit_end = strtotime($row['etd']);
    
        $days = 'NA';
        if ($row['etd'] != 'TBA' && $row['eta_mnl'] != 'TBA') {
            $days = ($transit_start - $transit_end) / (60 * 60 * 24); // Convert seconds to days;
        }
    
        $inner_html = <<<HTML
            <tr>
        HTML;

        $inner_html .= <<<HTML
                <td>{$row['vessel_name']}</td>
                <td>{$row['origin_port']}</td>
                <td>{$row['etd']}</td>
                <td>{$days}</td>
                <td>{$row['eta_mnl']}</td>
        HTML;
    
        //god had no hand at this creation
        //this is so bad I love it
        $computation_result = [
            "GREEN POLY" => [
                "count" => 0,
                "M" => 0,
                "L" => 0,
            ],
            "POLYTAINER" => [
                "M" => 0,
                "L" => 0,
                "XL" => 0,
            ],
            "PLASTIC PALLET" => [
                "count" => 0,
                "quantity" => 0,
            ],
            "WIREHARNESS" => [
                "count" => 0,
                "quantity" => 0,
            ],
            "TOTAL CONTAINER" => 0
        ];
    
        $stmt_main -> bindParam(':vessel_name', $row['vessel_name']);
        $stmt_main -> bindParam(':eta_mnl', $q_eta_mnl);
        $stmt_main -> bindParam(':etd', $q_etd);
        $stmt_main -> bindParam(':origin', $origin);
        $stmt_main -> execute();

        while ($computation_q = $stmt_main->fetch(PDO::FETCH_ASSOC)) {
            $polytainer_size = $computation_q['polytainer_size'];
            $commodity = (strpos($computation_q['commodity'], "POLYTAINER") !== false) ? "POLYTAINER" : $computation_q['commodity'];
            $polytainer_quantity = $computation_q['polytainer_quantity'];
    
            if (isset($computation_result[$commodity][$polytainer_size])) {
                $computation_result[$commodity][$polytainer_size] += $polytainer_quantity;
            }
            if (isset($computation_result[$commodity]['count'])) {
                $computation_result[$commodity]['count']++;
            }
            if (isset($computation_result[$commodity]['quantity'])) {
                $computation_result[$commodity]['quantity'] += $polytainer_quantity;
            }
    
            $computation_result["TOTAL CONTAINER"]++;
        }
        $gray_m = $computation_result['POLYTAINER']['M'] == 0 ? 0 : ceil($computation_result['POLYTAINER']['M'] / 2754);
        $gray_l = $computation_result['POLYTAINER']['L'] == 0 ? 0 : ceil($computation_result['POLYTAINER']['L'] / 2754);
        $gray_xl = $computation_result['POLYTAINER']['XL'] == 0 ? 0 : ceil($computation_result['POLYTAINER']['XL'] / 2754);
        $total = $gray_m + $gray_l + $gray_xl + $computation_result['WIREHARNESS']['count'] + $computation_result['PLASTIC PALLET']['count'] + $computation_result['GREEN POLY']['count'];
        $green_total_count += $computation_result['GREEN POLY']['count'];
        $gray_total_count += $gray_m + $gray_l + $gray_xl;
        $inner_html .= <<<HTML
            <td>{$computation_result['GREEN POLY']['count']}</td>
            <td>{$computation_result['GREEN POLY']['M']}</td>
            <td>{$computation_result['GREEN POLY']['L']}</td>
    
            <td>{$gray_m}</td>
            <td>{$computation_result['POLYTAINER']['M']}</td>
            <td>{$gray_l}</td>
            <td>{$computation_result['POLYTAINER']['L']}</td>
            <td>{$gray_xl}</td>
            <td>{$computation_result['POLYTAINER']['XL']}</td>
    
            <td>{$computation_result['WIREHARNESS']['count']}</td>
            <td>{$computation_result['WIREHARNESS']['quantity']}</td>
    
            <td>{$computation_result['PLASTIC PALLET']['count']}</td>
            <td>{$computation_result['PLASTIC PALLET']['quantity']}</td>
            <td>{$total}</td>
        HTML;
    
        $row['deliver_plan'] = $row['deliver_plan'] == null ? 'TBA' : 'TENTATIVE ON ' . date('Y/m/d', strtotime($row['deliver_plan']));
        $inner_html .= <<<HTML
            <td>{$row['deliver_plan']}</td>
        </tr>
        HTML;

        if ($total != 0) {
            $final_html .= $inner_html;
            $count++;
        }
    }

    $response_body = [];
    $response_body['final_html'] = $final_html;
    $response_body['counter'] = <<<HTML
        <div class="bg-success pl-4 pr-4" style="border-radius:.350rem;padding:0rem .350rem">
            <h4 style="font-weight:700;line-height:1.5;">{$green_total_count}<span style="font-size:75%;font-weight:500;">&nbsp;GREEN POLY</span></h4>
        </div>
        <div class="bg-secondary pl-4 pr-4 ml-2" style="border-radius:.350rem;padding:0rem .350rem">
            <h4 style="font-weight:700;line-height:1.5;">{$gray_total_count}<span style="font-size:75%;font-weight:500;">&nbsp;GRAY POLY</span></h4>
        </div>
    HTML;
    echo json_encode($response_body);