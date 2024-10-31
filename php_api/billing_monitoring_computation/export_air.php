<?php
    function export_air_compute($conn, $shipment_details_refs) {
        $sql_seek_bl = "SELECT bl_number from m_outgoing_bl_details where outgoing_details_ref = :shipment_details_ref";
        $stmt_seek_bl = $conn -> prepare($sql_seek_bl);

        //GET THE charge_group so we can generate a total
        //TODO this will take in the billing_ref_details in the future to find the computation set
        $sql_details_of_charge = "SELECT charge_group, billing_details_ref from m_billing_information where type_of_transaction = 'EXPORT AIR' and charge_group = 'LOCAL CHARGES';
            SELECT charge_group from m_billing_information where type_of_transaction = 'EXPORT AIR' and charge_group = 'ACCESSORIAL';
            SELECT charge_group from m_billing_information where type_of_transaction = 'EXPORT AIR' and charge_group = 'REIMBURSEMENT'";

        $computed_mega_json = [];
        foreach ($shipment_details_refs as $shipment_detail_ref) {
            $mini_mega_json = [];
            // Get info and bind parameters
            $stmt_seek_bl ->bindParam(":shipment_details_ref", $shipment_detail_ref);
            $stmt_seek_bl ->execute();
            // The header
            if ($data = $stmt_seek_bl ->fetch(PDO::FETCH_ASSOC)) {
                $mini_mega_json['bl_number'] = $data['bl_number'];
            }
            //might be best to get all the basis details we need here, to not loop over a retarded amount of times
            //stuff like how many containers, inv amount and stuff

            
            //i use the nextrowset so, this is required to requery
            $stmt_details_of_charge = $conn -> query($sql_details_of_charge);
            $array_computation = [];
            while (true) {
                $charge_group_temp_total = 0;
                while ($data = $stmt_details_of_charge -> fetch(PDO::FETCH_ASSOC)) {
                    //zero unless a computation set is available
                    $computed_value = 0;
                    //here you can start querying each charge of what computation set to use
                    //you will use the billing_ref_details or something
                    $array_computation[] = $computed_value;
                    $charge_group_temp_total += $computed_value;
                }
                //calculate the total here, per charge_group
                //the total per each charge_group will be at its bottom
                $array_computation[] = $charge_group_temp_total;
                //breaks the true while loop
                if ($stmt_details_of_charge -> nextRowset()) {
                    continue;
                } else {
                    break;
                }
            }
            $mini_mega_json['data'] = $array_computation;
            $computed_mega_json[] = $mini_mega_json;
        }
        return $computed_mega_json;
    }

    function export_air_table_render($conn, $computed_mega_json) {
        //injecting the TOTAL ROW to fit the charge_group total
        $sql = "SELECT charge_group, details_of_charge from m_billing_information where type_of_transaction = 'EXPORT AIR' and charge_group = 'LOCAL CHARGES'
                UNION ALL
            SELECT 'LOCAL CHARGES' as charge_group, '<strong>TOTAL</strong>' as details_of_charge
                UNION ALL
            SELECT charge_group, details_of_charge from m_billing_information where type_of_transaction = 'EXPORT AIR' and charge_group = 'ACCESSORIAL'
                UNION ALL
            SELECT 'ACCESSORIAL' as charge_group, '<strong>TOTAL</strong>' as details_of_charge
                UNION ALL
            SELECT charge_group, details_of_charge from m_billing_information where type_of_transaction = 'EXPORT AIR' and charge_group = 'REIMBURSEMENT'
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
            $total[] = array_sum($bl_data['data']);
        }

        $total_bl_based = "";
        foreach($total as $value) {
            $total_bl_based .= <<<HTML
                <td>{$value}</td>
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
            foreach($computed_mega_json as $bl_data) {
                $amt_bl_based .= <<<HTML
                    <td>{$bl_data['data'][$pointer]}</td>
                HTML;
            }
            $rows .= <<<HTML
            <tr style="background-color:{$colors[$data['charge_group']]}">
                <td>{$data['details_of_charge']}</td>
                {$amt_bl_based}
            </tr>
            HTML;
            //advance the pointer
            $pointer++;
        }

        $headers = "";
        foreach ($computed_mega_json as $bl_data) {
            $headers .= <<<HTML
                <th>{$bl_data['bl_number']}</th>
            HTML;
        }
        return [$headers, $rows];
    }