<?php
    //redev this shitty cluster fuck
    require 'db_connection.php';
    $shipping_invoice = $_GET['shipping_invoice'];

    $sql = "SELECT * from import_data where shipping_invoice = :shipping_invoice";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipping_invoice', $shipping_invoice);
    $stmt -> execute();
    $result = $stmt -> fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $keys = array_keys($result);
        $values = array_values($result);
    }

    $html = "";
    $forbidden = ["id", "shipment_details_ref"];
    $hidden = ["shipping_invoice"];
    $type = [
        'shipper' => 'text',
        'port' => 'text',
        'commodity_quantity' => 'number',
        'commodity_uo' => 'number',
        'commercial_invoice_currency' => 'text',
        'commercial_invoice_amount' => 'number',
        'gross_weight' => 'number',
        'incoterm' => 'text',
        'ip_number' => 'text',
        'dr_number' => 'text',
        'received_by' => 'text',
        'time_received' => 'text',
        'total_custom_value' => 'number',
        'duitable_value' => 'number',
        'rate' => 'number',
        'customs_duty' => 'number',
        'landed_cost' => 'number',
        'vat' => 'number',
        'bank_charges' => 'number',
        'wharfage' => 'number',
        'arrastre_charges' => 'number',
        'entry_no' => 'text',
        'or_number' => 'text',
        'assessment_date' => 'date',
    ];
    for ($i = 0; $i < count($keys); $i++) {
        if (in_array($keys[$i], $forbidden)) {
            continue;
        } 
        if (in_array($keys[$i], $hidden)) {
            $html .= <<<HTML
                <input class="form-control" style="display:none;" readonly  type="text" name="$keys[$i]" value="$values[$i]">
            HTML;
            continue;
        }
        $label = strtoupper(str_replace('_', ' ', $keys[$i]));
        $step = $type[$keys[$i]] == 'number' ? 'step="0.01"' : '';
        if (is_numeric($values[$i])) {
            $values[$i] = round((float)$values[$i], 2);
        }
        if ($keys[$i] == 'assessment_date') {
            $values[$i] = substr($values[$i], 0, 10);
        }
        $html .= <<<HTML
            <div class="row mt-2 justify-content-center">
                <div class="col-5">
                    <label>{$label}</label>
                </div>
                <div class="col-4">
                    <input class="form-control" type="{$type[$keys[$i]]}" {$step} name="$keys[$i]" value="$values[$i]">
                </div>
            </div>
        HTML;
    }
    $html .= <<<HTML
        <div class="row mt-3">
            <div class="col-5 mx-auto">
                <button class="btn btn-block btn-primary" type="submit">Update Import Data Details</button>
            </div>
        </div>
    HTML;

    $result_body = [];
    $result_body['success'] = true;
    $result_body['html'] = $html;
    $result_body['shipping_invoice'] = $shipping_invoice;
    echo json_encode($result_body);

