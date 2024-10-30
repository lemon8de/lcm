<?php
    require '../db_connection.php';

    $sql = "SELECT charge_group, details_of_charge, billing_details_ref from m_billing_information where type_of_transaction = ? and charge_group = 'LOCAL CHARGES';

    SELECT charge_group, details_of_charge, billing_details_ref from m_billing_information where type_of_transaction = ? and charge_group = 'ACCESSORIAL';
    
    SELECT charge_group, details_of_charge, billing_details_ref from m_billing_information where type_of_transaction = ? and charge_group = 'REIMBURSEMENT';
    ";

    $stmt = $conn -> prepare($sql);
    $stmt -> execute(array_fill(0, 3, $_GET['type_of_transaction']));

    $select_options = "";
    $first = false;
    while (true) {
        while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
            if (!$first) {
                $select_options .= <<<HTML
                    <optgroup label="{$data['charge_group']}">
                HTML;
            }
            $first = true;
            $select_options .= <<<HTML
                <option value="{$data['billing_details_ref']}">{$data['details_of_charge']}</option>
            HTML;
        }
        if ($stmt -> nextRowset()) {
            $select_options .= <<<HTML
                </optgroup>
            HTML;
            $first = false;
            continue;
        } else {
            break;
        }
    }
    $response_body['select'] = $select_options;
    echo json_encode($response_body);