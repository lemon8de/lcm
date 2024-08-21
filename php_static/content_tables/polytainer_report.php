<form action="../php_api/update_polytainer.php" method="POST">
<table id="" class="table table-head-fixed text-nowrap table-hover mb-4">
    <thead>
        <tr>
            <th colspan="6"></th>
            <th colspan="3" style="border: 1px solid black; text-align: center">GREEN POLY</th>
            <th colspan="6" style="border: 1px solid black; text-align: center">GRAY POLY</th>
            <th colspan="4" style="border: 1px solid black; text-align: center">OTHERS</th>
            <th colspan="2"></th>
        </tr>
        <tr>
            <th colspan="6"></th>
            <th style="border: 1px solid black; text-align: center;"></th>
            <th style="border: 1px solid black; text-align: center;">M SIZE POLYTAINER</th>
            <th style="border: 1px solid black; text-align: center;">L SIZE POLYTAINER</th>
            <th style="border: 1px solid black; text-align: center;" colspan="2">M SIZE POLYTAINER</th>
            <th style="border: 1px solid black; text-align: center;" colspan="2">L SIZE POLYTAINER</th>
            <th style="border: 1px solid black; text-align: center;" colspan="2">XL SIZE POLYTAINER</th>
            <th style="border: 1px solid black; text-align: center;" colspan="2">WIRE HARNESS</th>
            <th style="border: 1px solid black; text-align: center;" colspan="2">PLASTIC PALLET</th>
            <th style="border: 1px solid black; text-align: center;" colspan="1">TOTAL</th>
        </tr>
        <tr>
            <th>VESSEL NAME</th>
            <th>SHIPMENT STATUS</th>
            <th>ORIGIN PORT</th>
            <th>ETD</th>
            <th>NO. OF TRANSIT DAYS</th>
            <th>ETA MNL (YYYY/MM/DD)</th>

            <th>GREEN POLYTAINER - NO CTNR</th>
            <th>GREEN POLYTAINER MEDIUM QTY</th>
            <th>GREEN POLYTAINER LARGE QTY</th>

            <th>GRAY POLYTAINER - NO CTNR (MEDIUM)</th>
            <th>GRAY POLYTAINER - MEDIUM QTY</th>

            <th>GRAY POLYTAINER - NO CTNR (LARGE)</th>
            <th>GRAY POLYTAINER - LARGE QTY </th>

            <th>GRAY POLYTAINER - NO CTNR (XL)</th>
            <th>GRAY POLYTAINER - XL QTY</th>

            <th>NO OF CTNR (WIRE HARNESS)</th>
            <th>QTY (WIRE HARNESS)</th>

            <th>NO OF CTNR (PALLET)</th>
            <th>QTY (PALLET)</th>

            <th>TTL NO OF CTNR</th>
            <th>DELIVERY PLAN (YYYY/MM/DD)</th>
        </tr>
    </thead>
    <tbody id="">
        <?php 
            //this might need to be filtered by an etd range soon
            $sql = "SELECT distinct a.vessel_name, b.shipment_status, b.origin_port, a.eta_mnl, c.etd, d.deliver_plan from m_vessel_details as a left join m_shipment_sea_details as b on a.shipment_details_ref = b.shipment_details_ref left join m_polytainer_details as c on a.shipment_details_ref = c.shipment_details_ref left join m_delivery_plan as d on a.shipment_details_ref = d.shipment_details_ref";
            $stmt = $conn -> prepare($sql);
            $stmt -> execute();

            $sql_vessel_names = "SELECT distinct vessel_name from m_vessel_details";
            $stmt_vnames = $conn -> prepare($sql_vessel_names);
            $stmt_vnames -> execute();
            $vessel_names = $stmt_vnames->fetchAll(PDO::FETCH_COLUMN);

            $sql_main = "SELECT a.shipment_details_ref, a.commodity, c.polytainer_size, c.polytainer_quantity from m_shipment_sea_details as a join m_vessel_details as b on a.shipment_details_ref = b.shipment_details_ref join m_polytainer_details as c on a.shipment_details_ref = c.shipment_details_ref where vessel_name = :vessel_name";
            $stmt_main = $conn -> prepare($sql_main);

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $row['etd'] = $row['etd'] == null ? 'TBA' : date('Y/m/d', strtotime($row['etd']));
                $row['eta_mnl'] = $row['eta_mnl'] == null ? 'TBA' : date('Y/m/d', strtotime($row['eta_mnl']));
                $transit_start = strtotime($row['eta_mnl']);
                $transit_end = strtotime($row['etd']);

                echo <<<HTML
                    <tr>
                HTML;

                $days = 'NA';
                if ($row['etd'] != 'TBA' && $row['eta_mnl'] != 'TBA') {
                    $days = ($transit_start - $transit_end) / (60 * 60 * 24); // Convert seconds to days;
                }

                echo <<<HTML
                        <td>{$row['vessel_name']}</td>
                        <td>{$row['shipment_status']}</td>
                        <td>{$row['origin_port']}</td>
                        <td>{$row['etd']}</td>
                        <td>$days</td>
                        <td>{$row['eta_mnl']}</td>
                HTML;

                //god had no hand at this creation
                //this is so bad I love it
                $computation_result = [
                    "GREEN POLYTAINER" => [
                        "count" => 0,
                        "M" => 0,
                        "L" => 0,
                    ],
                    "RETURNABLE POLYTAINER" => [
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
                $stmt_main -> execute();
                while ($computation_q = $stmt_main->fetch(PDO::FETCH_ASSOC)) {
                    $polytainer_size = $computation_q['polytainer_size'];
                    $commodity = $computation_q['commodity'];
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
                $gray_m = $computation_result['RETURNABLE POLYTAINER']['M'] == 0 ? 0 : $computation_result['RETURNABLE POLYTAINER']['M'] / 2754;
                $gray_l = $computation_result['RETURNABLE POLYTAINER']['L'] == 0 ? 0 : $computation_result['RETURNABLE POLYTAINER']['L'] / 2754;
                $gray_xl = $computation_result['RETURNABLE POLYTAINER']['XL'] == 0 ? 0 : $computation_result['RETURNABLE POLYTAINER']['XL'] / 2754;
                $total = $gray_m + $gray_l + $gray_xl + $computation_result['WIREHARNESS']['count'] + $computation_result['PLASTIC PALLET']['count'];
                echo <<<HTML
                    <td>{$computation_result['GREEN POLYTAINER']['count']}</td>
                    <td>{$computation_result['GREEN POLYTAINER']['M']}</td>
                    <td>{$computation_result['GREEN POLYTAINER']['L']}</td>

                    <td>$gray_m</td>
                    <td>{$computation_result['RETURNABLE POLYTAINER']['M']}</td>
                    <td>$gray_l</td>
                    <td>{$computation_result['RETURNABLE POLYTAINER']['L']}</td>
                    <td>$gray_xl</td>
                    <td>{$computation_result['RETURNABLE POLYTAINER']['XL']}</td>

                    <td>{$computation_result['WIREHARNESS']['count']}</td>
                    <td>{$computation_result['WIREHARNESS']['quantity']}</td>

                    <td>{$computation_result['PLASTIC PALLET']['count']}</td>
                    <td>{$computation_result['PLASTIC PALLET']['quantity']}</td>

                    <td>$total</td>
                HTML;

                $row['deliver_plan'] = $row['deliver_plan'] == null ? 'TBA' : date('Y/m/d', strtotime($row['deliver_plan']));
                echo <<<HTML
                    <td>{$row['deliver_plan']}</td>
                HTML;
            }
            echo <<<HTML
                <tr>
            HTML;
        ?>
    </tbody>
</table>
</form>