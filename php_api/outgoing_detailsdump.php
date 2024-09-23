<?php
require 'db_connection.php';
$outgoing_details_ref = $_GET['outgoing_details_ref'];
$return_body = [];

$sql = "SELECT * from m_outgoing_fsib where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn -> prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> execute();
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($shipment) {
    if ($shipment['ship_out_date'] != null) {
        $shipment['ship_out_date'] = substr($shipment['ship_out_date'], 0, 10);
    }
    $shipment['invoice_amount'] = round(floatval($shipment['invoice_amount']), 2);
    $return_body['outgoing_fsib'] = <<<HTML
        <input readonly style="display:none;" value="{$outgoing_details_ref}" type="text" name="outgoing_details_ref">
        <div class="row mb-2">
            <div class="col-6">
                <label>INVOICE NO.</label>
                <input value="{$shipment['invoice_no']}" type="text" class="form-control" name="invoice_no">
            </div>
            <div class="col-6">
                <label>CONTAINER NO.</label>
                <input value="{$shipment['container_no']}" type="text" class="form-control" name="container_no">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <label>DESTINATION (SERVICE CENTER)</label>
                <input value="{$shipment['destination_service_center']}" type="text" class="form-control" name="destination_service_center">
            </div>
            <div class="col-6">
                <label>DESTINATION</label>
                <input value="{$shipment['destination']}" type="text" class="form-control" name="destination">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <label>CAR_MODEL</label>
                <input value="{$shipment['car_model']}" type="text" class="form-control" name="car_model">
            </div>
            <div class="col-6">
                <label>SHIP OUT DATE</label>
                <input value="{$shipment['ship_out_date']}" type="date" class="form-control" name="ship_out_date">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <label>NO. PALLETS</label>
                <input value="{$shipment['no_pallets']}" type="number" class="form-control" name="no_pallets">
            </div>
            <div class="col-6">
                <label>NO. CARTONS</label>
                <input value="{$shipment['no_cartons']}" type="number" class="form-control" name="no_cartons">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <label>PACK QTY.</label>
                <input value="{$shipment['pack_qty']}" type="number" class="form-control" name="pack_qty">
            </div>
            <div class="col-6">
                <label>INVOICE AMOUNT</label>
                <input value="{$shipment['invoice_amount']}" type="number" class="form-control" name="invoice_amount">
            </div>
        </div>
        <div class="row mb-2 d-flex align-items-center">
            <div class="col-3 ml-auto">
                <button type="submit" class="btn bg-primary btn-block">Update</button>
            </div>
        </div>
    HTML;
}


$sql = "SELECT * from m_outgoing_vessel_details where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn -> prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> execute();
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($shipment) {
    if ($shipment['etd_mnl'] != null) {
        $shipment['etd_mnl'] = substr($shipment['etd_mnl'], 0, 10);
    }
    if ($shipment['eta_destination'] != null) {
        $shipment['eta_destination'] = substr($shipment['eta_destination'], 0, 10);
    }
    $return_body['outgoing_vessel'] = <<<HTML
        <input readonly style="display:none;" value="{$outgoing_details_ref}" type="text" name="outgoing_details_ref">
        <div class="row mb-2">
            <div class="col-6">
                <label>MODE OF SHIPMENT</label>
                <input value="{$shipment['mode_of_shipment']}" type="text" class="form-control" name="mode_of_shipment">
            </div>
            <div class="col-6">
                <label>VESSEL NAME</label>
                <input value="{$shipment['vessel_name']}" type="text" class="form-control" name="vessel_name">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <label>SHIPPING LINE</label>
                <input value="{$shipment['shipping_line']}" type="text" class="form-control" name="shipping_line">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <label>ETD MNL</label>
                <input value="{$shipment['etd_mnl']}" type="date" class="form-control" name="etd_mnl">
            </div>
            <div class="col-6">
                <label>ETA DESTINATION</label>
                <input value="{$shipment['eta_destination']}" type="date" class="form-control" name="eta_destination">
            </div>
        </div>
        <div class="row mb-2 d-flex align-items-center">
            <div class="col-3 ml-auto">
                <button type="submit" class="btn bg-primary btn-block">Update</button>
            </div>
        </div>
    HTML;
}

$sql = "SELECT * from m_outgoing_invoice_details where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn -> prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> execute();
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($shipment) {
    $shipment['gross_weight'] = round(floatval($shipment['gross_weight']), 4);
    $shipment['net_weight'] = round(floatval($shipment['net_weight']), 4);
    $shipment['cbm'] = round(floatval($shipment['cbm']), 4);

    $return_body['outgoing_invoice'] = <<<HTML
        <input readonly style="display:none;" value="{$outgoing_details_ref}" type="text" name="outgoing_details_ref">
        <div class="row mb-2">
            <div class="col-6">
                <label>SHIPPING TERMS</label>
                <input value="{$shipment['shipping_terms']}" type="text" class="form-control" name="shipping_terms">
            </div>
            <div class="col-6">
                <label>NET WEIGHT</label>
                <input value="{$shipment['net_weight']}" type="number" step="0.0001" class="form-control" name="net_weight">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <label>GROSS WEIGHT</label>
                <input value="{$shipment['gross_weight']}" type="number" step="0.0001" class="form-control" name="gross_weight">
            </div>
            <div class="col-6">
                <label>CBM</label>
                <input value="{$shipment['cbm']}" type="number" step="0.0001" class="form-control" name="cbm">
            </div>
        </div>
        <div class="row mb-2 d-flex align-items-center">
            <div class="col-3 ml-auto">
                <button type="submit" class="btn bg-primary btn-block">Update</button>
            </div>
        </div>
    HTML;
}

$sql = "SELECT * from m_outgoing_container_details where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn -> prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> execute();
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($shipment) {
    if ($shipment['falp_in_reuse'] != null) {
        $shipment['falp_in_reuse'] = substr($shipment['falp_in_reuse'], 0, 10);
    }
    $return_body['outgoing_container'] = <<<HTML
        <input readonly style="display:none;" value="{$outgoing_details_ref}" type="text" name="outgoing_details_ref">
        <div class="row mb-2">
            <div class="col-6">
                <label>FALP IN / REUSE</label>
                <input value="{$shipment['falp_in_reuse']}" type="date" class="form-control" name="falp_in_reuse">
            </div>
            <div class="col-6">
                <label>STATUS OF CONTAINER</label>
                <input value="{$shipment['status_of_container']}" type="text" class="form-control" name="status_of_container">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <label>CONTAINER SIZE</label>
                <input value="{$shipment['container_size']}" type="text" class="form-control" name="container_size">
            </div>
            <div class="col-6">
                <label>FORWARDER</label>
                <input value="{$shipment['forwarder']}" type="text" class="form-control" name="forwarder">
            </div>
        </div>
        <div class="row mb-2 d-flex align-items-center">
            <div class="col-3 ml-auto">
                <button type="submit" class="btn bg-primary btn-block">Update</button>
            </div>
        </div>
    HTML;
}

$sql = "SELECT * from m_outgoing_dispatching_details where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn -> prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> execute();
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($shipment) {
    $return_body['outgoing_dispatch'] = <<<HTML
        <input readonly style="display:none;" value="{$outgoing_details_ref}" type="text" name="outgoing_details_ref">
        <div class="row mb-2">
            <div class="col-6">
                <label>ED REFERENCE</label>
                <input value="{$shipment['ed_reference']}" type="text" class="form-control" name="ed_reference">
            </div>
            <div class="col-6">
                <label>SHIPPING SEAL</label>
                <input value="{$shipment['shipping_seal']}" type="text" class="form-control" name="shipping_seal">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <label>PEZA SEAL</label>
                <input value="{$shipment['peza_seal']}" type="text" class="form-control" name="peza_seal">
            </div>
        </div>
        <div class="row mb-2 d-flex align-items-center">
            <div class="col-3 ml-auto">
                <button type="submit" class="btn bg-primary btn-block">Update</button>
            </div>
        </div>
    HTML;
}

$sql = "SELECT * from m_outgoing_cont_lineup where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn -> prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> execute();
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($shipment) {
    if ($shipment['falp_out_date'] != null) {
        $shipment['falp_out_date'] = substr($shipment['falp_out_date'], 0, 10);
    }
    $return_body['outgoing_contlineup'] = <<<HTML
        <input readonly style="display:none;" value="{$outgoing_details_ref}" type="text" name="outgoing_details_ref">
        <div class="row mb-2">
            <div class="col-6">
                <label>FALP OUT DATE</label>
                <input value="{$shipment['falp_out_date']}" type="date" class="form-control" name="falp_out_date">
            </div>
            <div class="col-6">
                <label>FALP OUT TIME</label>
                <input value="{$shipment['falp_out_time']}" type="text" class="form-control" name="falp_out_time">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <label>TRUCK HEAD STATUS</label>
                <input value="{$shipment['truckhead_status']}" type="text" class="form-control" name="truckhead_status">
            </div>
        </div>
        <div class="row mb-2 d-flex align-items-center">
            <div class="col-3 ml-auto">
                <button type="submit" class="btn bg-primary btn-block">Update</button>
            </div>
        </div>
    HTML;
}

$sql = "SELECT * from m_outgoing_bl_details where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn -> prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> execute();
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($shipment) {
    if ($shipment['bl_date'] != null) {
        $shipment['bl_date'] = substr($shipment['bl_date'], 0, 10);
    }
    $return_body['outgoing_bl'] = <<<HTML
        <input readonly style="display:none;" value="{$outgoing_details_ref}" type="text" name="outgoing_details_ref">
        <div class="row mb-2">
            <div class="col-6">
                <label>BL DATE</label>
                <input value="{$shipment['bl_date']}" type="date" class="form-control" name="bl_date">
            </div>
            <div class="col-6">
                <label>BL NUMBER</label>
                <input value="{$shipment['bl_number']}" type="text" class="form-control" name="bl_number">
            </div>
        </div>
        <div class="row mb-2 d-flex align-items-center">
            <div class="col-3 ml-auto">
                <button type="submit" class="btn bg-primary btn-block">Update</button>
            </div>
        </div>
    HTML;
}

$sql = "SELECT * from m_outgoing_rtv where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn -> prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> execute();
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($shipment) {
    $return_body['outgoing_rtv'] = <<<HTML
        <input readonly style="display:none;" value="{$outgoing_details_ref}" type="text" name="outgoing_details_ref">
        <div class="row mb-2">
            <div class="col-6">
                <label>IRREGULAR SHIPMENT (DEPARTMENT)</label>
                <input value="{$shipment['irregular_shipment']}" type="text" class="form-control" name="irregular_shipment">
            </div>
        </div>
        <div class="row mb-2 d-flex align-items-center">
            <div class="col-3 ml-auto">
                <button type="submit" class="btn bg-primary btn-block">Update</button>
            </div>
        </div>
    HTML;
}

//history details
$sql = "SELECT column_name, changed_from, changed_to, date_modified from m_change_history where shipment_details_ref = :outgoing_details_ref order by date_modified asc";
$stmt = $conn -> prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> execute();

$return_body['history'] = null;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $row['date_modified'] = date("Y/m/d", strtotime($row["date_modified"]));
    $row['changed_from'] = $row['changed_from'] == null ? "N/A" : $row['changed_from'];
    $row['changed_to'] = $row['changed_to'] == null ? "N/A" : $row['changed_to'];
    $return_body['history'] .= <<<HTML
        <tr>
            <td>{$row['date_modified']}</td>
            <td>{$row['column_name']}</td>
            <td>{$row['changed_from']}</td>
            <td>{$row['changed_to']}</td>
        </tr>
    HTML;
}

echo json_encode($return_body);
exit();