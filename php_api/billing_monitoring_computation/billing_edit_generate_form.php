<?php
    require '../db_connection.php';

    $type_of_transaction = $_POST['type_of_transaction'];
    $forwarder = $_POST['forwarder'];
    $billing_forwarder_details_ref = $_POST['billing_forwarder_details_ref'];
    $shipping_line = $_POST['shipping_line'];
    $origin_port = $_POST['origin_port'];
    $destination_port = $_POST['destination_port'];
    $billing_details_ref = $_POST['billing_details_ref'];
    $wants_new = $_POST['wants_new'];
    $new_charge = $_POST['new_charge'];
    $new_basis = $_POST['new_basis'];
    $new_charge_group = $_POST['new_charge_group'];
    $new_currency = $_POST['new_currency'];

    //precheck form completion
    if ($forwarder == "" || $type_of_transaction == "" || $shipping_line == "" || $billing_details_ref == "") {
        echo json_encode(['exited' => true]);
        exit();
    }
    if ($origin_port == "" && $destination_port == "") {
        echo json_encode(['exited' => true]);
        exit();
    }
    if ($wants_new == "true" && ($new_charge == "" || $new_basis == "" || $new_charge_group == "" || $new_currency == "")) {
        echo json_encode(['exited' => true]);
        exit();
    }
    $response_body = [];
    $inner_html = "";
    if ($wants_new === 'true') {
        //wants new means we don't have a log in
        //m_billing_information
        //we would like to create that
        //well would you like to create it now or in the next button click? maybe just make it now

        $sql = "INSERT into m_billing_information (type_of_transaction, charge_group, details_of_charge, basis, currency, billing_details_ref) values (:type_of_transaction, :charge_group, :details_of_charge, :basis, :currency, :billing_details_ref)";
        if ($type_of_transaction === "IMPORT SEA") {
            $prefix = "billing_is_";
        } else if ($type_of_transaction === "IMPORT AIR") {
            $prefix = "billing_ia_";
        } else if ($type_of_transaction === "EXPORT SEA") {
            $prefix = "billing_es_";
        } else if ($type_of_transaction === "EXPORT AIR") {
            $prefix = "billing_ea_";
        }
        $billing_details_ref = uniqid($prefix, true);
        $stmt = $conn -> prepare($sql);
        $stmt -> bindParam(":type_of_transaction", $type_of_transaction);
        $stmt -> bindParam(":charge_group", $new_charge_group);
        $stmt -> bindParam(":details_of_charge", $new_charge);
        $stmt -> bindParam(":basis", $new_basis);
        $stmt -> bindParam(":currency", $new_currency);
        $stmt -> bindParam(":billing_details_ref", $billing_details_ref);
        $stmt -> execute();

        $inner_html .= <<<HTML
            <div class="col-12 alert alert-info mt-1" id="HistoricalAlert" onclick="window.location.reload();" style="cursor:pointer;">
                <span style="font-weight:700; font-size:90%;"><i class="icon fas fa-info"></i>&nbsp; Refresh required to make more logs on this new charge. Click to refresh</span>
            </div>
        HTML;
    }
    //now actually proceed on getting that form
    //get the information from the details of charge, and render its form
    $sql = "SELECT * from m_billing_information where billing_details_ref = :billing_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":billing_details_ref", $billing_details_ref);
    $stmt -> execute();

    $info = "";
    if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $info = <<<HTML
            <div class="d-flex flex-column">
                <span><strong>FORWARDER: </strong>{$forwarder}</span>
                <span><strong>TYPE OF TRANSACTION: </strong>{$data['type_of_transaction']}</span>
                <span><strong>DETAIL OF CHARGE: </strong>{$data['details_of_charge']}</span>
                <span><strong>BASIS: </strong>{$data['basis']}</span>
                <span><strong>CURRENCY: </strong>{$data['currency']}</span>
                <span><strong>SHIPPING LINE: </strong>{$shipping_line}</span>
                <span><strong>ORIGIN PORT: </strong>{$origin_port}</span>
                <span><strong>DESTINATION PORT: </strong>{$destination_port}</span>
            </div>
        HTML;
        $current_year = date("Y");
        $end_year = $current_year - 10;
        $year_select = "";
        for ($year = $current_year; $year >= $end_year; $year--) {
            $year_select .= <<<HTML
                <option value="{$year}">{$year}</option>
            HTML;
        }

        //preselected month
        $currentMonthString = date('n'); // This is a string
        $currentMonthInteger = (int)$currentMonthString;
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
        $select_months = "";
        foreach ($months as $value => $name) {
            if ($value === $currentMonthInteger) {
                $select_months .= <<<HTML
                    <option value="{$value}" selected>{$name}</option>
                HTML;
            } else {
                $select_months .= <<<HTML
                    <option value="{$value}">{$name}</option>
                HTML;
            }
        }
        $inner_html .= <<<HTML
            <input type="text" class="form-control" style="display:none;" value="{$billing_forwarder_details_ref}" name="billing_forwarder_details_ref" readonly>
            <input type="text" class="form-control" style="display:none;" value="{$billing_details_ref}" name="billing_details_ref" readonly>
            <input type="text" class="form-control" style="display:none;" value="{$shipping_line}" name="shipping_line" readonly>
            <input type="text" class="form-control" style="display:none;" value="{$origin_port}" name="origin_port" readonly>
            <input type="text" class="form-control" style="display:none;" value="{$destination_port}" name="destination_port" readonly>
            <input type="text" class="form-control" style="display:none;" value="{$data['basis']}" name="basis" readonly>

            <div class="row mt-3">
                <div class="col-6">
                    <label>Apply for Month</label>
                    <select class="form-control" name="month">
                        {$select_months}
                    </select>
                </div>
                <div class="col-6">
                    <label>For year</label>
                    <select class="form-control" name="year">
                        {$year_select}
                    </select>
                </div>
            </div>
        HTML;

        if ($data['basis'] === "BL" || $data['basis'] === "CNTR") {
            $inner_html .= <<<HTML
                <div class="mt-3">
                    <label>EDIT RATE</label>
                    <input type="number" class="form-control" name="rate" value="" required>
                </div>
                <button type="submit" class="btn btn-info mt-3 text-right">Update</button>
            HTML;
        }
    }
    $response_body['inner_html'] = $inner_html;
    $response_body['info'] = $info;

    //extra code for building that chart
    //basically we need two arrays, labels and data from m_billing_compute
    //just need to get the right set of data from the db
    //match billing details ref, shipping line, origin_port, destination port, and forwarder

    $sql = "WITH RankedRows AS (
    SELECT 
        computation_set, 
        FORMAT(for_date, 'yyyy-MM') AS for_date,
        ROW_NUMBER() OVER (PARTITION BY FORMAT(for_date, 'yyyy-MM') ORDER BY id DESC) AS rn
    FROM 
        m_billing_compute 
    WHERE 
        billing_forwarder_details_ref = :billing_forwarder_details_ref 
        AND billing_details_ref = :billing_details_ref 
        AND shipping_line = :shipping_line 
        AND origin_port = :origin_port
)
SELECT 
    computation_set, 
    for_date 
FROM 
    RankedRows 
WHERE 
    rn = 1
ORDER BY 
    for_date
OFFSET 0 ROWS FETCH NEXT 10 ROWS ONLY;";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":billing_forwarder_details_ref", $billing_forwarder_details_ref);
    $stmt -> bindParam(":billing_details_ref", $billing_details_ref);
    $stmt -> bindParam(":shipping_line", $shipping_line);
    $stmt -> bindParam(":origin_port", $origin_port);
    $stmt -> execute();

    $labels = [];
    $dataset = [];
    while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $labels[] = $data['for_date'];
        $json = json_decode($data['computation_set']);
        $dataset[] = $json -> data_set -> rate;
    }
    $response_body['labels'] = $labels;
    $response_body['dataset'] = $dataset;
    echo json_encode($response_body);