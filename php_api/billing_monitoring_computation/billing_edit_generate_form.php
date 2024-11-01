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
    }
    //now actually proceed on getting that form
    //get the information from the details of charge, and render its form
    $sql = "SELECT * from m_billing_information where billing_details_ref = :billing_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":billing_details_ref", $billing_details_ref);
    $stmt -> execute();

    $response_body = [];
    $inner_html = "";
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
        $inner_html .= <<<HTML
            <input type="text" class="form-control" style="display:none;" value="{$billing_forwarder_details_ref}" name="billing_forwarder_details_ref" readonly>
            <input type="text" class="form-control" style="display:none;" value="{$billing_details_ref}" name="billing_details_ref" readonly>
            <input type="text" class="form-control" style="display:none;" value="{$shipping_line}" name="shipping_line" readonly>
            <input type="text" class="form-control" style="display:none;" value="{$origin_port}" name="origin_port" readonly>
            <input type="text" class="form-control" style="display:none;" value="{$destination_port}" name="destination_port" readonly>
            <input type="text" class="form-control" style="display:none;" value="{$data['basis']}" name="basis" readonly>
        HTML;

        if ($data['basis'] === "BL") {
            $inner_html .= <<<HTML
                <div class="mt-3">
                    <label>EDIT RATE</label>
                    <input type="number" class="form-control" name="rate" value="">
                </div>
                <button type="submit" class="btn btn-info mt-3 text-right">Update</button>
            HTML;
        }
    }
    $response_body['inner_html'] = $inner_html;
    $response_body['info'] = $info;
    echo json_encode($response_body);