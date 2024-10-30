<?php
    function export_sea_compute($conn, $shipment_details_refs) {
        $sql = "SELECT forwarder as forwarder_name from m_outgoing_container_details where outgoing_details_ref = :shipment_details_ref";
        $stmt = $conn -> prepare($sql);

        $computed_mega_json = [];
        foreach ($shipment_details_refs as $shipment_detail_ref) {
            $mini_mega_json = [];
            // Get info and bind parameters
            $stmt->bindParam(":shipment_details_ref", $shipment_detail_ref);
            $stmt->execute();
    
            // The header
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $mini_mega_json['forwarder_name'] = $data['forwarder_name'];
            }

            //COMPUTATION, EXPORT SEA HAS 38 CHARGES TO COMPUTE
            $array_computation = array_fill(0, 38, 1);
            $mini_mega_json['data'] = $array_computation;
            $computed_mega_json[] = $mini_mega_json;
        }
        //the duck inside the chat generated this fuckall code that merges the mega json
        //so in essence, the code above makes computation based on the shipmentdetailref (more specifically the bl_number), meaning it would naturally have repeating forwarder_name, this merges everything
        $unique_forwarders = array_reduce($computed_mega_json, function($carry, $item) {
            $forwarder_name = $item['forwarder_name'];
            // Check if the forwarder name already exists in the carry
            if (!isset($carry[$forwarder_name])) {
                $carry[$forwarder_name] = [
                    'forwarder_name' => $forwarder_name,
                    'data' => $item['data']
                ];
            } else {
                // If it exists, you can merge the data as needed
                // For example, if you want to sum the data arrays:
                $carry[$forwarder_name]['data'] = array_map(function($a, $b) {
                    return $a + $b; // Adjust this logic based on your needs
                }, $carry[$forwarder_name]['data'], $item['data']);
            }
        return $carry;
        }, []);
        // Reset the array keys to get a simple indexed array
        $final_result = array_values($unique_forwarders);
        return $final_result;
    }

    function export_sea_table_render($conn, $computed_mega_json) {
        $sql = "SELECT charge_group, details_of_charge, currency from m_billing_information where type_of_transaction = 'EXPORT SEA' order by case charge_group when 'LOCAL CHARGES' then 1 when 'ACCESSORIAL' then 2 when 'REIMBURSEMENT' then 3 else 4 end";
        $stmt = $conn -> prepare($sql);
        $stmt -> execute();
        
        $rows = "";
        $pointer = 0;
        $colors = [
            'LOCAL CHARGES' => '#6EB4E4',
            'ACCESSORIAL' => '#A8D8B9',
            'REIMBURSEMENT' => '#E1C6A8'
        ];

        $total = [];
        foreach($computed_mega_json as $forwarder_data) {
            $total[] = array_sum($forwarder_data['data']);
        }

        $total_fwd_based = "";
        foreach($total as $value) {
            $total_fwd_based .= <<<HTML
                <td>{$value}</td>
            HTML;
        }
        $rows .= <<<HTML
            <tr style="font-weight:700;">
                <td>TOTAL</td>
                {$total_fwd_based}
            </tr>
        HTML;
        while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
            //getting the total, per forwarder_name
            $amt_fwd_based = "";
            foreach($computed_mega_json as $forwarder_data) {
                $amt_fwd_based .= <<<HTML
                    <td>{$forwarder_data['data'][$pointer]}</td>
                HTML;
            }
            $rows .= <<<HTML
            <tr style="background-color:{$colors[$data['charge_group']]}">
                <td>{$data['details_of_charge']}</td>
                {$amt_fwd_based}
            </tr>
            HTML;
            //advance the pointer
            $pointer++;
        }

        $headers = "";
        foreach ($computed_mega_json as $forwarder_data) {
            $headers .= <<<HTML
                <th>{$forwarder_data['forwarder_name']}</th>
            HTML;
        }
        return [$headers, $rows];
    }