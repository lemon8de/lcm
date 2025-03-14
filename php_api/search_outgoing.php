<?php
    require '../php_static/session_lookup.php';
    if (!isset($_GET['date_from'])) {
        echo json_encode(["exited" => true]);
        exit();
    }

    require 'db_connection.php'; 

    $invoice_no = '%' . $_GET['invoice_no'] . '%';
    //$container_no = $_GET['container_no'];

    if (!isset($_GET['destination_service_center'])) {
        $destination_service_center = "";
    } else {
        $destination_service_center = $_GET['destination_service_center'];
    }

    $status = $_GET['status'];
    $co_status = $_GET['co_status'];

    $datefrom = $_GET['date_from'] . ' 00:00:00';
    $dateto = $_GET['date_to'] == "" ? null : $_GET['date_to'];

    $sql = "EXEC SearchOutgoing :SearchInput, :DestinationServiceCenter, :Status, :CoStatus, :DateFrom, :DateTo";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":SearchInput", $invoice_no);
    $stmt -> bindParam(":DestinationServiceCenter", $destination_service_center);
    $stmt -> bindParam(":Status", $status);
    $stmt -> bindParam(":CoStatus", $co_status);
    $stmt -> bindParam(":DateFrom", $datefrom);
    $stmt -> bindParam(":DateTo", $dateto);
    $stmt -> execute();
    $inner_html = "";

    //nov 12 revision, the onlick is based on the editing privileges
    if ($_SESSION['editing_privileges'] == "OUTGOING" || $_SESSION['editing_privileges'] == "ALL") { 
        $onclick = "onclick='loaddata.call(this)' class='modal-trigger' data-toggle='modal' data-target='#edit_outgoing_modal'";
    } else {
        $onclick = "";
    }
    while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        if ($data['co_status'] !== null) {
            switch ($data['co_status']) {
                case 'N/A':
                    if ($data['bl_date'] == NULL || $data['bl_date'] = '' || $data['bl_number'] == NULL || $data['bl_number'] == "") {
                        $color = 'style="background-color:#ffcccc;"';
                    } else {
                        $color = 'style="background-color:#ccffcc;"';
                    }
                    break;
                case 'FOR REQUEST':
                    $color = 'style="background-color:#ffcccc;"';
                    break;
                case 'COMPLETE':
                    $color = 'style="background-color:#ccffcc;"';
                    break;
                case 'ONGOING':
                    $color = 'style="background-color:#ccccff;"';
                    break;
                default:
                    $color = '';
                    break;
            }
        } else {
            $color = '';
        }
        $inner_html .= <<<HTML
            <tr id="{$data['outgoing_details_ref']}" {$onclick} {$color}>
                <td><input type="checkbox" class="row-checkbox" id="ck-{$data['outgoing_details_ref']}" onclick="event.stopPropagation();"></td>
                <td>{$data['invoice_no']}</td>
                <td>{$data['container_no']}</td>
                <td>{$data['destination_service_center']}</td>
                <td>{$data['destination']}</td>
                <td>{$data['car_model']}</td>
                <td>{$data['ship_out_date']}</td>
                <td>{$data['no_pallets']}</td>
                <td>{$data['no_cartons']}</td>
                <td>{$data['pack_qty']}</td>
                <td>{$data['invoice_amount']}</td>
                <td>{$data['mode_of_shipment']}</td>
                <td>{$data['vessel_name']}</td>
                <td>{$data['shipping_line']}</td>
                <td>{$data['etd_mnl']}</td>
                <td>{$data['eta_destination']}</td>
                <td>{$data['shipping_terms']}</td>
                <td>{$data['net_weight']}</td>
                <td>{$data['gross_weight']}</td>
                <td>{$data['cbm']}</td>
                <td>{$data['falp_in_reuse']}</td>
                <td>{$data['status_of_container']}</td>
                <td>{$data['container_size']}</td>
                <td>{$data['forwarder']}</td>
                <td>{$data['ed_reference']}</td>
                <td>{$data['shipping_seal']}</td>
                <td>{$data['peza_seal']}</td>
                <td>{$data['falp_out_date']}</td>
                <td>{$data['falp_out_time']}</td>
                <td>{$data['truckhead_status']}</td>
                <td>{$data['bl_date']}</td>
                <td>{$data['bl_number']}</td>
                <td>{$data['irregular_shipment']}</td>
                <td>{$data['status']}</td>
                <td>{$data['co_status']}</td>
            </tr>
        HTML;
    }
    
    $selection = '<option value="">Destination</option>';
    $stmt -> nextRowset();
    while ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        if ($destination_service_center == $data['destination_service_center']) {
            $selected = "selected";
        } else {
            $selected = "";
        }
        $selection .= <<<HTML
            <option {$selected}>{$data['destination_service_center']}</option>
        HTML;
    }

    $response_body['selection'] = $selection;
    $response_body['inner_html'] = $inner_html;
    echo json_encode($response_body);