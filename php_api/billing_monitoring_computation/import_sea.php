<?php
    function import_sea_compute($conn, $shipment_details_refs) {
        $sql_seek_bl = "SELECT bl_number, forwarder_name, origin_port, shipping_lines, actual_received_at_falp from m_shipment_sea_details as a left join m_completion_details as b on a.shipment_details_ref = b.shipment_details_ref where a.shipment_details_ref = :shipment_details_ref;
        
        SELECT container, actual_received_at_falp FROM m_shipment_sea_details as a left join m_completion_details as b on a.shipment_details_ref = b.shipment_details_ref WHERE bl_number IN ( SELECT bl_number FROM m_shipment_sea_details where shipment_details_ref = :shipment_details_ref2)";

        $stmt_seek_bl = $conn -> prepare($sql_seek_bl);

        //GET THE charge_group so we can generate a total
        //TODO this will take in the billing_ref_details in the future to find the computation set
        $sql_details_of_charge = "SELECT charge_group, billing_details_ref, currency from m_billing_information where type_of_transaction = 'IMPORT SEA' and charge_group = 'LOCAL CHARGES';
            SELECT charge_group, billing_details_ref, currency from m_billing_information where type_of_transaction = 'IMPORT SEA' and charge_group = 'ACCESSORIAL';
            SELECT charge_group, billing_details_ref, currency from m_billing_information where type_of_transaction = 'IMPORT SEA' and charge_group = 'REIMBURSEMENT'";

        $sql_get_computation = "SELECT top 1 computation_set, jpy_php, usd_php, jpy_usd from m_billing_compute as a
            left join m_billing_forwarder as b on a.billing_forwarder_details_ref = b.billing_forwarder_details_ref
            left join t_billing_exchange as c on a.for_date = c.for_date
            where billing_details_ref = :billing_details_ref and
            forwarder_partner = :forwarder_partner and
            shipping_line = :shipping_line and
            (origin_port = :origin_port and destination_port is null)
            and :actual_received_at_falp between a.for_date and eomonth(a.for_date)
            order by a.id desc";

        $computed_mega_json = [];
        foreach ($shipment_details_refs as $shipment_detail_ref) {
            $mini_mega_json = [];
            // Get info and bind parameters
            $stmt_seek_bl = $conn -> prepare($sql_seek_bl);
            $stmt_seek_bl ->bindParam(":shipment_details_ref", $shipment_detail_ref);
            $stmt_seek_bl ->bindParam(":shipment_details_ref2", $shipment_detail_ref);
            $stmt_seek_bl ->execute();
            // The header
            if ($data = $stmt_seek_bl ->fetch(PDO::FETCH_ASSOC)) {
                $mini_mega_json['bl_number'] = $data['bl_number'];

                $bl_forwarder = $data['forwarder_name'];
                $bl_origin = $data['origin_port'];
                $bl_shipping_line = $data['shipping_lines'];
                $actual_received_at_falp = $data['actual_received_at_falp'];

                $container_actual_received = [];
                if ($stmt_seek_bl->nextRowset()) { // Move to the next result set
                    while ($data_container = $stmt_seek_bl -> fetch(PDO::FETCH_ASSOC)) {
                        $container_actual_received[] = $data_container['actual_received_at_falp'];
                    }
                }
            }
            
            //i use the nextrowset so, this is required to requery
            $stmt_details_of_charge = $conn -> query($sql_details_of_charge);
            //$array_computation = [];
            $array_computation_usd = [];
            $array_computation_php = [];
            $array_computation_jpy = [];
            while (true) {
                //$charge_group_temp_total = 0;

                $charge_group_temp_total_usd = 0;
                $charge_group_temp_total_php = 0;
                $charge_group_temp_total_jpy = 0;

                while ($data = $stmt_details_of_charge -> fetch(PDO::FETCH_ASSOC)) {
                    //zero unless a computation set is available
                    //$computed_value = 0;

                    $stmt_get_computation = $conn -> prepare($sql_get_computation);
                    $stmt_get_computation -> bindParam(":billing_details_ref", $data['billing_details_ref']);
                    $stmt_get_computation -> bindParam(":forwarder_partner", $bl_forwarder);
                    $stmt_get_computation -> bindParam(":origin_port", $bl_origin);
                    $stmt_get_computation -> bindParam(":shipping_line", $bl_shipping_line);
                    $stmt_get_computation -> bindParam(":actual_received_at_falp", $actual_received_at_falp);
                    $stmt_get_computation -> execute();

                    if ($compute_data = $stmt_get_computation -> fetch(PDO::FETCH_ASSOC)) {
                        $compute_set = json_decode($compute_data['computation_set']);
                        $currency = $data['currency'];

                        $container_logged = 0;
                        $container_total = 0;
                        switch ($compute_set->basis) {
                            case 'BL':
                                $computed_value = $compute_set->data_set->rate;
                                break;
                            case 'CNTR':
                                $computed_value = 0;
                                foreach ($container_actual_received as $container_date) {
                                    $stmt_get_computation -> bindParam(":actual_received_at_falp", $container_date);
                                    $stmt_get_computation -> execute();
                                    if ($compute_data_container = $stmt_get_computation -> fetch(PDO::FETCH_ASSOC)) {
                                        $container_logged++;
                                        $compute_set_container = json_decode($compute_data_container['computation_set']);
                                        $computed_value += $compute_set_container -> data_set -> rate;
                                    } 
                                    $container_total++;
                                }
                                //$computed_value = $container_count * $compute_set->data_set->rate;
                                break;
                            default:
                                // Optionally handle cases where basis is neither 'BL' nor 'CNTR'
                                $computed_value = 0; // or some other default value
                                break;
                        }
                        //nov 8 we need 3 array computation now, for php usd and jpy and we also need three totals fuck me
                        switch ($currency) {
                            case 'USD':
                                $computed_value_usd = $computed_value;
                                $computed_value_php = $computed_value * $compute_data['usd_php'];
                                $computed_value_jpy = $computed_value / $compute_data['jpy_usd'];
                                break;
                            case 'PHP':
                                $computed_value_usd = $computed_value / $compute_data['usd_php'];
                                $computed_value_php = $computed_value;
                                $computed_value_jpy = $computed_value / $compute_data['jpy_php'];
                                break;
                            default:
                                break;
                        }
                    } else {
                        $computed_value_usd = 0;
                        $computed_value_php = 0;
                        $computed_value_jpy = 0;
                    }

                    //$array_computation[] = $computed_value;
                    //$charge_group_temp_total += $computed_value;

                    //nov 8 3 array computation
                    $array_computation_usd[] = $computed_value_usd;
                    $array_computation_php[] = $computed_value_php;
                    $array_computation_jpy[] = $computed_value_jpy;

                    $charge_group_temp_total_usd += $computed_value_usd;
                    $charge_group_temp_total_php += $computed_value_php;
                    $charge_group_temp_total_jpy += $computed_value_jpy;

                }
                //calculate the total here, per charge_group
                //the total per each charge_group will be at its bottom
                //avoid being part of array sum by changing its datatype and failing is_numeric check
                //$array_computation[] = '<strong>' . round($charge_group_temp_total, 2) . '</strong>';

                //nov 8 3 array computaiton
                $array_computation_usd[] = '<strong>' . number_format($charge_group_temp_total_usd, 2) . '</strong>';
                $array_computation_php[] = '<strong>' . number_format($charge_group_temp_total_php, 2) . '</strong>';
                $array_computation_jpy[] = '<strong>' . number_format($charge_group_temp_total_jpy, 2) . '</strong>';

                //breaks the true while loop
                if ($stmt_details_of_charge -> nextRowset()) {
                    continue;
                } else {
                    break;
                }
            }
            //$mini_mega_json['data'] = $array_computation;
            $mini_mega_json['data'] = [$array_computation_usd, $array_computation_php, $array_computation_jpy];
            if (isset($container_logged)) {
                $mini_mega_json['bl_number'] .= " [" . $container_logged . "/" . $container_total . "]";
            } else {
                $mini_mega_json['bl_number'] .= " [?/?]";
            }

            $computed_mega_json[] = $mini_mega_json;
        }
        return $computed_mega_json;
    }

    function import_sea_table_render($conn, $computed_mega_json) {
        //injecting the TOTAL ROW to fit the charge_group total
        $sql = "SELECT charge_group, details_of_charge from m_billing_information where type_of_transaction = 'IMPORT SEA' and charge_group = 'LOCAL CHARGES'
                UNION ALL
            SELECT 'LOCAL CHARGES' as charge_group, '<strong>TOTAL</strong>' as details_of_charge
                UNION ALL
            SELECT charge_group, details_of_charge from m_billing_information where type_of_transaction = 'IMPORT SEA' and charge_group = 'ACCESSORIAL'
                UNION ALL
            SELECT 'ACCESSORIAL' as charge_group, '<strong>TOTAL</strong>' as details_of_charge
                UNION ALL
            SELECT charge_group, details_of_charge from m_billing_information where type_of_transaction = 'IMPORT SEA' and charge_group = 'REIMBURSEMENT'
                UNION ALL
            SELECT 'REIMBURSEMENT' as charge_group, '<strong>TOTAL</strong>' as details_of_charge";
        $stmt = $conn -> query($sql);
        
        $rows = "";
        $pointer = 0;
        $colors = [
            'LOCAL CHARGES' => '#6EB4E4',
            'ACCESSORIAL' => '#A8D8B9',
            'REIMBURSEMENT' => '#E1C6A8'
        ];

        $total = [];
        foreach($computed_mega_json as $bl_data) {
            //$total[] = array_sum(array_filter($bl_data['data'], 'is_numeric'));
            $total[] = [
                number_format(array_sum(array_filter($bl_data['data'][0], 'is_numeric')), 2),
                number_format(array_sum(array_filter($bl_data['data'][1], 'is_numeric')), 2),
                number_format(array_sum(array_filter($bl_data['data'][2], 'is_numeric')), 2)
            ];
        }

        $total_bl_based = "";
        foreach($total as $value) {
            $total_bl_based .= <<<HTML
                <td>{$value[0]}</td>
                <td>{$value[1]}</td>
                <td>{$value[2]}</td>
            HTML;
        }
        $rows .= <<<HTML
            <tr style="font-weight:700;">
                <td>TOTAL</td>
                {$total_bl_based}
            </tr>
        HTML;
        while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
            $amt_bl_based = "";
            $values_list = [];
            foreach($computed_mega_json as $bl_data) {
                //the total count with strong tags gets caught here, fuck
                $usd = is_numeric($bl_data['data'][0][$pointer]) ? number_format($bl_data['data'][0][$pointer], 2) : $bl_data['data'][0][$pointer];
                $php = is_numeric($bl_data['data'][1][$pointer]) ? number_format($bl_data['data'][1][$pointer], 2) : $bl_data['data'][1][$pointer];
                $jpy = is_numeric($bl_data['data'][2][$pointer]) ? number_format($bl_data['data'][2][$pointer], 2) : $bl_data['data'][2][$pointer];
                $amt_bl_based .= <<<HTML
                    <td>{$usd}</td>
                    <td>{$php}</td>
                    <td>{$jpy}</td>
                HTML;
                $values_list[] = $usd;
            }
            $show = False;
            foreach ($values_list as $value) {
                if ($value !== "0.00") {
                    $show = True;
                    break;
                }
            }
            if ($show) {
                $rows .= <<<HTML
                <tr style="background-color:{$colors[$data['charge_group']]}">
                    <td>{$data['details_of_charge']}</td>
                    {$amt_bl_based}
                </tr>
                HTML;
            }
            //advance the pointer
            $pointer++;
        }

        $headers = "";
        $currency_header = <<<HTML
            <th></th>
        HTML;
        foreach ($computed_mega_json as $bl_data) {
            $headers .= <<<HTML
                <th colspan="3" class="text-center">{$bl_data['bl_number']}</th>
            HTML;

            $currency_header .= <<<HTML
                <th>USD</th>
                <th>PHP</th>
                <th>JPY</th>
            HTML;
        }
        return [$currency_header, $headers, $rows];
    }