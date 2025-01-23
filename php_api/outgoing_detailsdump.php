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
                <input value="{$shipment['container_no']}" type="text" class="form-control" name="container_no" pattern=".{11}">
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
                <label>CAR MODEL</label>
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

$sql = "SELECT vessel_name, string_agg(invoice_no, ', ') as invoices from m_outgoing_fsib as a left join m_outgoing_vessel_details as b on a.outgoing_details_ref = b.outgoing_details_ref where vessel_name = :vessel_name group by vessel_name";
$stmt_vessel = $conn -> prepare($sql);

//19 december make dropdown
$sql = "SELECT * from m_shipping_lines";
$stmt = $conn -> query($sql);
$shipping_line_options = '';
while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    $selected = $shipment['shipping_line'] == $data['shipping_lines'] ? 'selected' : '';
    $shipping_line_options .= <<<HTML
        <option {$selected}>{$data['shipping_lines']}</option>
    HTML;
}

//17 january make dropdown, mode of shipment
$modes_shipment = ['SEA', 'AIR', 'TBA'];
$modes_of_shipment_options = "";
foreach ($modes_shipment as $type) {
    $selected = $shipment['mode_of_shipment'] == $type ? 'selected' : '';
    $modes_of_shipment_options .= <<<HTML
        <option {$selected}>{$type}</option>
    HTML;
}

if ($shipment) {
    $vessel_name = $shipment['vessel_name'];
    $stmt_vessel -> bindParam(":vessel_name", $vessel_name);
    $stmt_vessel -> execute();
    if ($data = $stmt_vessel -> fetch(PDO::FETCH_ASSOC)) {
        $list_of_invoices = $data['invoices'];
        $info_html = <<<HTML
            <div class="container-fluid alert alert-info">
                <i class="icon fas fa-info"></i>Changes will apply to invoices: {$list_of_invoices}
            </div>
        HTML;
    } else {
        $list_of_invoices = "";
        $info_html = "";
    }
    if ($shipment['etd_mnl'] != null) {
        $shipment['etd_mnl'] = substr($shipment['etd_mnl'], 0, 10);
    }
    if ($shipment['eta_destination'] != null) {
        $shipment['eta_destination'] = substr($shipment['eta_destination'], 0, 10);
    }
    $return_body['outgoing_vessel'] = <<<HTML
        <input readonly style="display:none;" value="{$outgoing_details_ref}" type="text" name="outgoing_details_ref">
            <div id="VesselDetailsEditToolTipInfo" class="row mb-2">
                {$info_html}
            </div>
        <div class="row mb-2">
            <div class="col-6">
                <label>MODE OF SHIPMENT</label>
                <select id="vessel_mode_of_shipment" class="form-control" name="mode_of_shipment">
                    {$modes_of_shipment_options} 
                </select>
            </div>
            <div class="col-6">
                <label>VESSEL NAME</label>
                <input value="{$shipment['vessel_name']}" type="text" class="form-control" name="vessel_name" onkeyup="find_similar_vessels(this)">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <label>SHIPPING LINE</label>
                <!-- <input value="{$shipment['shipping_line']}" type="text" class="form-control" name="shipping_line" id="vessel_shipping_line"> -->
                <select id="vessel_shipping_line" class="form-control" name="shipping_line">
                    <option value="" disabled selected>Shipping Line</option>
                    {$shipping_line_options} 
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <label>ETD MNL</label>
                <input value="{$shipment['etd_mnl']}" type="date" class="form-control" name="etd_mnl" id="vessel_etd_mnl">
            </div>
            <div class="col-6">
                <label>ETA DESTINATION</label>
                <input value="{$shipment['eta_destination']}" type="date" class="form-control" name="eta_destination" id="vessel_eta_destination">
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

//19 december turn these into select dropdowns
$sql = "SELECT shipping_terms from m_shipping_terms order by id asc";
$stmt = $conn -> query($sql);
$shipping_terms_options = '';
while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    $selected = $shipment['shipping_terms'] == $data['shipping_terms'] ? 'selected' : '';
    $shipping_terms_options .= <<<HTML
        <option {$selected}>{$data['shipping_terms']}</option>
    HTML;
}


if ($shipment) {
    $shipment['gross_weight'] = round(floatval($shipment['gross_weight']), 4);
    $shipment['net_weight'] = round(floatval($shipment['net_weight']), 4);
    $shipment['cbm'] = round(floatval($shipment['cbm']), 4);

    $return_body['outgoing_invoice'] = <<<HTML
        <input readonly style="display:none;" value="{$outgoing_details_ref}" type="text" name="outgoing_details_ref">
        <div class="row mb-2">
            <div class="col-6">
                <label>SHIPPING TERMS</label>
                <!-- <input value="{$shipment['shipping_terms']}" type="text" class="form-control" name="shipping_terms"> -->
                <select class="form-control" name="shipping_terms">
                    <option value="" disabled selected>Shipping Term</option>
                    {$shipping_terms_options} 
                </select>
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

//19 december make dropdown
$sql = "SELECT forwarder_partner from m_billing_forwarder order by id asc";
$stmt = $conn -> query($sql);
$forwarder_options = '';
while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    $selected = $shipment['forwarder'] == $data['forwarder_partner'] ? 'selected' : '';
    $forwarder_options .= <<<HTML
        <option {$selected}>{$data['forwarder_partner']}</option>
    HTML;
}

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
                <!-- <input value="{$shipment['forwarder']}" type="text" class="form-control" name="forwarder"> -->
                <select class="form-control" name="forwarder">
                    <option value="" disabled selected>Forwarder</option>
                    {$forwarder_options} 
                </select>
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

//on load lock of bl_details, though there should be an api that can requery this to recheck
//because the modal won't update / unlock when you are just putting information at the same time
$sql = "EXEC outgoing_LOCK_bl_input :OutgoingDetailsRef";
$should_lock = $conn -> prepare($sql);
$should_lock -> bindParam(":OutgoingDetailsRef", $outgoing_details_ref);
$should_lock -> execute();
if ($lock = $should_lock -> fetch(PDO::FETCH_ASSOC)) {
    if ($lock['lock_bl'] == 'True') {
        $lock_bl = "disabled";
    } else {
        $lock_bl = "";
    }
}


if ($shipment) {
    if ($shipment['bl_date'] != null) {
        $shipment['bl_date'] = substr($shipment['bl_date'], 0, 10);
    }
    $return_body['outgoing_bl'] = <<<HTML
        <input readonly style="display:none;" value="{$outgoing_details_ref}" type="text" name="outgoing_details_ref">
        <div class="row mb-2">
            <div class="col-6">
                <label>BL DATE</label>
                <input id="lock-bl_date" value="{$shipment['bl_date']}" type="date" class="form-control" name="bl_date" {$lock_bl}>
            </div>
            <div class="col-6">
                <label>BL NUMBER</label>
                <input id="lock-bl_number" value="{$shipment['bl_number']}" type="text" class="form-control" name="bl_number" {$lock_bl}>
            </div>
        </div>
        <div class="row mb-2 d-flex align-items-center">
            <div class="col-3">
                <button type="button" class="text-nowrap btn btn-primary mr-3" onclick="check_bl_lock('{$outgoing_details_ref}')">Refresh Lock</button>
            </div>
            <div class="col-3 ml-auto">
                <button id="lock-bl_update" type="submit" class="btn bg-primary btn-block" {$lock_bl}>Update</button>
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
$sql = "SELECT username, column_name, changed_from, changed_to, date_modified from m_change_history where shipment_details_ref = :outgoing_details_ref order by date_modified asc";
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
            <td>{$row['username']}</td>
            <td>{$row['column_name']}</td>
            <td style="background-color: #ffcecb;">{$row['changed_from']}</td>
            <td style="background-color: #d1f8d9;">{$row['changed_to']}</td>
        </tr>
    HTML;
}

$sql = "SELECT status, co_status from m_outgoing_status_details where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn -> prepare($sql);
$stmt -> bindParam(":outgoing_details_ref", $outgoing_details_ref);
$stmt -> execute();

if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    switch ($data['status']) {
        case 'N/A':
            $status_select = <<<HTML
                <option selected>N/A</option>
                <option>FOR REQUEST</option>
                <option>RECEIVED</option>
                <option>ONGOING</option>
            HTML;
            break;
    
        case 'FOR REQUEST':
            $status_select = <<<HTML
                <option>N/A</option>
                <option selected>FOR REQUEST</option>
                <option>RECEIVED</option>
                <option>ONGOING</option>
            HTML;
            break;

        case 'RECEIVED':
            $status_select = <<<HTML
                <option>N/A</option>
                <option>FOR REQUEST</option>
                <option selected>RECEIVED</option>
                <option>ONGOING</option>
            HTML;
            break;

        case 'ONGOING':
            $status_select = <<<HTML
                <option>N/A</option>
                <option>FOR REQUEST</option>
                <option>RECEIVED</option>
                <option selected>ONGOING</option>
            HTML;
            break;
    
        default:
            //call jay if it went down here
            break;
    }
    switch ($data['co_status']) {
        case 'N/A':
            $co_status_select = <<<HTML
                <option selected>N/A</option>
                <option>FOR REQUEST</option>
                <option>COMPLETE</option>
                <option>ONGOING</option>
            HTML;
            break;
    
        case 'FOR REQUEST':
            $co_status_select = <<<HTML
                <option>N/A</option>
                <option selected>FOR REQUEST</option>
                <option>COMPLETE</option>
                <option>ONGOING</option>
            HTML;
            break;

        case 'COMPLETE':
            $co_status_select = <<<HTML
                <option>N/A</option>
                <option>FOR REQUEST</option>
                <option selected>COMPLETE</option>
                <option>ONGOING</option>
            HTML;
            break;

        case 'ONGOING':
            $co_status_select = <<<HTML
                <option>N/A</option>
                <option>FOR REQUEST</option>
                <option>COMPLETE</option>
                <option selected>ONGOING</option>
            HTML;
            break;
    
        default:
            //call jay if it went down here
            break;
    }
    if ($data['status'] !== 'RECEIVED') {
        $disabled = " disabled";
    } else {
        $disabled = "";
    }
    $return_body['outgoing_status'] = <<<HTML
        <input readonly style="display:none;" value="{$outgoing_details_ref}" type="text" name="outgoing_details_ref">
        <div class="row mb-2">
            <div class="col-6">
                <label>STATUS</label>
                <select class="form-control" name="status" onchange="check_co_status(this)">
                    {$status_select}
                </select>
            </div>
            <div class="col-6">
                <label>CO STATUS</label>
                <select class="form-control" id="co_status_select" name="co_status"{$disabled}>
                    {$co_status_select}
                </select>
            </div>
        </div>
        <div class="row mb-2 d-flex align-items-center">
            <div class="col-3 ml-auto">
                <button type="submit" class="btn bg-primary btn-block">Update</button>
            </div>
        </div>
    HTML;
} else {
    $return_body['outgoing_status'] = <<<HTML
        <div class="text-muted text-center">
            <p>Selected Invoice does not support a Status page.</p>
        </div>
    HTML;
}

echo json_encode($return_body);
exit();