<?php
    require 'db_connection.php';
    $shipment_details_ref = $_GET['shipment_details_ref'];
    $return_body = [];

    //delivery_plan
    $sql = "SELECT required_delivery_sched, deliver_plan, tabs from m_delivery_plan where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();
    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($shipment) {
        $shipment['required_delivery_sched'] = $shipment['required_delivery_sched'] == null ? null : substr($shipment['required_delivery_sched'], 0, 10);
        $shipment['deliver_plan'] = $shipment['deliver_plan'] == null ? null : substr($shipment['deliver_plan'], 0, 10);
        $shipment['tabs'] = $shipment['tabs'] == null ? null : substr($shipment['tabs'], 0, 5);
        $return_body['delivery_plan'] = <<<HTML
            <input readonly style="display:none;" value="{$shipment_details_ref}" type="text" name="shipment_details_ref">
            <div class="row mb-2">
                <div class="col-6">
                    <label>REQUIRED DELIVERY SCHEDULE</label>
                    <input value="{$shipment['required_delivery_sched']}" type="date" class="form-control" name="required_delivery_sched">
                </div>
                <div class="col-6">
                    <label>DELIVERY PLAN</label>
                    <input value="{$shipment['deliver_plan']}" type="date" class="form-control" name="deliver_plan">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>TABS</label>
                    <input value="{$shipment['tabs']}" type="text" class="form-control" name="tabs">
                </div>
            </div>
            <div class="row mb-2 d-flex align-items-center">
                <div class="col-3 ml-auto">
                    <button type="submit" class="btn bg-primary btn-block">Update</button>
                </div>
            </div>
        HTML;
    }

    //completion details
    $sql = "SELECT date_port_out, actual_received_at_falp from m_completion_details where shipment_details_ref = :shipment_details_ref"; 
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();
    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($shipment) {
        $shipment['date_port_out'] = $shipment['date_port_out'] == null ? null : substr($shipment['date_port_out'], 0, 10);
        $shipment['actual_received_at_falp'] = $shipment['actual_received_at_falp'] == null ? null : substr($shipment['actual_received_at_falp'], 0, 10);
        $return_body['completion_details'] = <<<HTML
            <input readonly style="display:none;" value="{$shipment_details_ref}" type="text" name="shipment_details_ref">
            <div class="row mb-2">
                <div class="col-6">
                    <label>DATE PORT OUT</label>
                    <input type="date" value="{$shipment['date_port_out']}" class="form-control" name="date_port_out">
                </div>
                <div class="col-6">
                    <label>ACTUAL RECEIVED AT FALP</label>
                    <input type="date" value="{$shipment['actual_received_at_falp']}" class="form-control" name="actual_received_at_falp">
                </div>
            </div>
            <div class="row mb-2 d-flex align-items-center">
                <div class="col-3 ml-auto">
                    <button type="submit" class="btn bg-primary btn-block">Update</button>
                </div>
            </div>
        HTML;
    }

    //polytainer details
    $sql = "SELECT polytainer_size, polytainer_quantity, etd from m_polytainer_details where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();
    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($shipment) {
        $shipment['etd'] = $shipment['etd'] == null ? null : substr($shipment['etd'], 0, 10);
        $return_body['polytainer_details'] = <<<HTML
            <input readonly style="display:none;" value="{$shipment_details_ref}" type="text" name="shipment_details_ref">
            <div class="row mb-2">
                <div class="col-6">
                    <label>POLYTAINER SIZE</label>
                    <input value="{$shipment['polytainer_size']}" type="text" class="form-control" name="polytainer_size">
                </div>
                <div class="col-6">
                    <label>POLYTAINER QUANTITY</label>
                    <input value="{$shipment['polytainer_quantity']}" type="text" class="form-control" name="polytainer_quantity">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>ETD</label>
                    <input value="{$shipment['etd']}" type="date" class="form-control" name="etd">
                </div>
            </div>
            <div class="row mb-2 d-flex align-items-center">
                <div class="col-3 ml-auto">
                    <button type="submit" class="btn bg-primary btn-block">Update</button>
                </div>
            </div>
        HTML;
    }

    //mm_system details
    $sql = "SELECT container_status, date_return_reused, no_days_port, no_days_falp from m_mmsystem where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();
    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($shipment) {
        $shipment['date_return_reused'] = $shipment['date_return_reused'] == null ? null : substr($shipment['date_return_reused'], 0, 10);
        $return_body['mmsystem_details'] = <<<HTML
            <input readonly style="display:none;" value="{$shipment_details_ref}" type="text" name="shipment_details_ref">
            <div class="row mb-2">
                <div class="col-6">
                    <label>CONTAINER STATUS</label>
                    <input value="{$shipment['container_status']}" type="text" class="form-control" name="container_status">
                </div>
                <div class="col-6">
                    <label>DATE RETURN / REUSED</label>
                    <input value="{$shipment['date_return_reused']}" type="date" class="form-control" name="date_return_reused">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>NO. DAYS AT PORT</label>
                    <input readonly value="{$shipment['no_days_port']}" type="number" class="form-control" name="no_days_port">
                </div>
                <div class="col-6">
                    <label>NO. DAYS AT FALP</label>
                    <input readonly value="{$shipment['no_days_falp']}" type="number" class="form-control" name="no_days_falp">
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
    $sql = "SELECT username, column_name, changed_from, changed_to, date_modified from m_change_history where shipment_details_ref = :shipment_details_ref order by date_modified asc";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
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


    //shipment details
    $sql = "SELECT shipment_details_ref, bl_number, container, container_size, commercial_invoice, commodity, shipping_lines, forwarder_name, origin_port, destination_port, tsad_number, shipment_status, shipment_status_percentage from m_shipment_sea_details where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();
    $shipment = $stmt -> fetch(PDO::FETCH_ASSOC);

    if ($shipment) {
        $return_body['shipment'] = <<<HTML
            <input readonly style="display:none;" value="{$shipment['shipment_details_ref']}" type="text" name="shipment_details_ref">
            <div class="row mb-2">
                <div class="col-6">
                    <label>BL NUMBER</label>
                    <input type="text" class="form-control" value="{$shipment['bl_number']}" name="bl_number" required>
                </div>
                <div class="col-6">
                    <label>CONTAINER</label>
                    <input type="text" class="form-control" value="{$shipment['container']}" name="container" required>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>CONTAINER SIZE</label>
                    <input type="text" class="form-control" value="{$shipment['container_size']}" name="container_size" required>
                </div>
                <div class="col-6">
                    <label>COMMERCIAL INVOICE</label>
                    <input type="text" class="form-control" value="{$shipment['commercial_invoice']}" name="commercial_invoice" required>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>COMMODITY</label>
                    <select name="commodity" class="form-control" required>
        HTML;

        $sql = "SELECT method, value, display_name from list_commodity where method = 'sea' order by display_name asc";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $selected = "";
            if ($shipment['commodity'] == $row['display_name']){
                $selected = " selected";
            }
            $return_body['shipment'] .= <<<HTML
                <option value="{$row['value']}"{$selected}>{$row['display_name']}</option>
            HTML;
        }

        //19 december make dropdown
        $sql = "SELECT * from m_shipping_lines";
        $stmt = $conn -> query($sql);
        $shipping_line_options = '';
        while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
            $selected = $shipment['shipping_lines'] == $data['shipping_lines'] ? 'selected' : '';
            $shipping_line_options .= <<<HTML
                <option {$selected}>{$data['shipping_lines']}</option>
            HTML;
        }
        //19 december make dropdown
        $sql = "SELECT forwarder_partner from m_billing_forwarder order by id asc";
        $stmt = $conn -> query($sql);
        $forwarder_options = '';
        while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
            $selected = $shipment['forwarder_name'] == $data['forwarder_partner'] ? 'selected' : '';
            $forwarder_options .= <<<HTML
                <option {$selected}>{$data['forwarder_partner']}</option>
            HTML;
        }

        $return_body['shipment'] .= <<<HTML
                    </select>
                </div>
                <div class="col-6">
                    <label>SHIPPING LINES</label>
                    <!-- <input type="text" class="form-control" value="{$shipment['shipping_lines']}" name="shipping_lines" required> -->
                    <select class="form-control" name="shipping_lines">
                        <option value="" disabled selected>Shipping Line</option>
                        {$shipping_line_options} 
                    </select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>FORWARDER NAME</label>
                    <!-- <input type="text" class="form-control" value="{$shipment['forwarder_name']}" name="forwarder_name" required> -->
                    <select class="form-control" name="forwarder_name">
                        <option value="" disabled selected>Forwarder</option>
                        {$forwarder_options} 
                    </select>
                </div>
                <div class="col-6">
                    <label>ORIGIN PORT</label>
                    <input type="text" class="form-control" value="{$shipment['origin_port']}" name="origin_port" required>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>SHIPMENT STATUS</label>
                    <input type="text" class="form-control" value="{$shipment['shipment_status']}" name="shipment_status" required>
                </div>
                <div class="col-6">
                    <label>SHIPMENT STATUS</label>
                    <input type="number" class="form-control" value="{$shipment['shipment_status_percentage']}" name="shipment_status_percentage" required step="10">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>DESTINATION PORT</label>
                    <input type="text" class="form-control" value="{$shipment['destination_port']}" name="destination_port" required>
                </div>
                <div class="col-6">
                    <label>TSAD NUMBER</label>
                    <input type="text" class="form-control" value="{$shipment['tsad_number']}" name="tsad_number" required>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-3 ml-auto">
                    <button type="submit" class="btn bg-primary btn-block">Update</button>
                </div>
            </div>
        HTML;
    }

    //vessel_details
    $sql = "SELECT shipment_details_ref, vessel_name, eta_mnl, ata_mnl, atb from m_vessel_details where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();
    $shipment = $stmt -> fetch(PDO::FETCH_ASSOC);
    //need to get this quickly for the other query,
    if ($shipment) {
        $vessel_name = $shipment['vessel_name'];
        $eta = !isset($shipment['eta_mnl']) ? null : date('Y-m-d', strtotime($shipment['eta_mnl']));
        $ata = !isset($shipment['ata_mnl']) ? null : date('Y-m-d', strtotime($shipment['ata_mnl']));
        $atb = !isset($shipment['atb']) ? null : date('Y-m-d', strtotime($shipment['atb']));
    }

    //takes all the shipment_details_ref codes that carries the same information
    $sql_ref = "SELECT shipment_details_ref, vessel_name from m_vessel_details where vessel_name = :vessel_name and (eta_mnl = :eta_mnl or eta_mnl is null) and (ata_mnl = :ata_mnl or ata_mnl is null) and (atb = :atb or atb is null) order by id desc";
    $stmt_ref = $conn -> prepare($sql_ref);
    $stmt_ref -> bindParam(':vessel_name', $vessel_name);
    $stmt_ref -> bindParam(':eta_mnl', $eta);
    $stmt_ref -> bindParam(':ata_mnl', $ata);
    $stmt_ref -> bindParam(':atb', $atb);
    $stmt_ref -> execute();

    //we have shipment_details_ref of all matching, all we need to do is to get their name and append to the info html
    $sql_container = "SELECT container from m_shipment_sea_details where shipment_details_ref = :shipment_details_ref";
    $stmt_container = $conn -> prepare($sql_container);
    $info_html = "<i class='icon fas fa-info'></i>Changes will apply on containers: ";
    while ($info = $stmt_ref -> fetch(PDO::FETCH_ASSOC)) {
        $stmt_container -> bindParam(':shipment_details_ref', $info['shipment_details_ref']);
        $stmt_container -> execute();
        if ($shipment_data = $stmt_container -> fetch(PDO::FETCH_ASSOC)) {
            $info_html .= $shipment_data['container'] . ", ";
        }
    }

    if ($shipment) {
        $shipment['eta_mnl'] = $shipment['eta_mnl'] == null ? null : substr($shipment['eta_mnl'], 0, 10);
        $shipment['ata_mnl'] = $shipment['ata_mnl'] == null ? null : substr($shipment['ata_mnl'], 0, 10);

        //additions sep5: making input boxes readonly on condition: if has ata: disable eta
        $eta_disabled = $shipment['ata_mnl'] != null ? ' readonly' : "";

        $shipment['atb'] = $shipment['atb'] == null ? null : substr($shipment['atb'], 0, 10);
        $return_body['vessel'] = <<<HTML
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info" id="VesselDetailsEditToolTipInfo">
                       {$info_html} 
                    </div>
                </div>
            </div>
            <input readonly style="display:none;" value="{$shipment['shipment_details_ref']}" type="text" name="shipment_details_ref">
            <div class="row mb-2">
                <div class="col-6">
                    <label>VESSEL NAME</label>
                    <input type="text" class="form-control" id="VesselDetailsEditName" value="{$shipment['vessel_name']}" name="vessel_name" required onkeyup="debounce(edit_find_similar, 150)">
                </div>
                <div class="col-6">
                    <label>ETA MNL (YYYY/MM/DD)</label>
                    <input type="date" class="form-control" id="VesselDetailsEditETA" value="{$shipment['eta_mnl']}" name="eta_mnl"{$eta_disabled}>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>ATA MNL (YYYY/MM/DD)</label>
                    <input type="date" class="form-control" id="VesselDetailsEditATA" value="{$shipment['ata_mnl']}" name="ata_mnl">
                </div>
                <div class="col-6">
                    <label>ATB</label>
                    <input type="date" class="form-control" id="VesselDetailsEditATB" value="{$shipment['atb']}" name="atb">
                </div>
            </div>
            <div class="row mb-2 d-flex align-items-center">
                <div class="col-3 ml-auto">
                    <button type="submit" class="btn bg-primary btn-block">Update</button>
                </div>
            </div>
        HTML;
    }

    $return_body['success'] = true;
    $return_body['shipment_details_ref'] = $shipment_details_ref;
    echo json_encode($return_body);