<?php
    require 'db_connection.php';
    $hawb_awb = $_GET['hawb_awb'];
    $response_body = [];

    //details dump for shipment
    $sql = "SELECT shipment_details_ref, forwarder, origin, hawb_awb, format(eta, 'yyyy-MM-dd') as eta, round(gross_weight, 2) as gross_weight, round(chargeable_weight, 2) as chargeable_weight, no_packages, commodity, classification, incoterm, shipment_status, shipment_status_progress, invoice_no from t_shipment_air_details where hawb_awb = :hawb_awb";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":hawb_awb", $hawb_awb);
    $stmt -> execute();

    //special inputs
    //classification is a dropdown
    $classification_select = <<<HTML
        <select class="form-control" name="classification" required>
    HTML;
    //shipment_status_progress is a dropdown
    $shipment_status_progress_select = <<<HTML
        <select class="form-control" name="shipment_status_progress" required>
    HTML;

    $inner_html_shipment = "";
    if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        switch ($data['classification']) {
            case 'RAW MATERIALS':
                $classification_select .= <<<HTML
                        <option selected>RAW MATERIALS</option>
                        <option>OTHERS</option>
                </select>
                HTML;
                break;
        
            case 'OTHERS':
                $classification_select .= <<<HTML
                        <option>RAW MATERIALS</option>
                        <option selected>OTHERS</option>
                </select>
                HTML;
                break;
        
            default:
                $classification_select .= <<<HTML
                        <option selected disabled value="">Select a Classification</option>
                        <option>RAW MATERIALS</option>
                        <option>OTHERS</option>
                </select>
                HTML;
                break;
        }

        switch ($data['shipment_status_progress']) {
            case 'ACTIVE':
                $shipment_status_progress_select .= <<<HTML
                        <option selected>ACTIVE</option>
                        <option>FOR RELEASE</option>
                        <option>DELIVERED</option>
                    </select>
                HTML;
                break;
            case 'FOR RELEASE':
                $shipment_status_progress_select .= <<<HTML
                        <option>ACTIVE</option>
                        <option selected>FOR RELEASE</option>
                        <option>DELIVERED</option>
                    </select>
                HTML;
                break;
            case 'DELIVERED':
                $shipment_status_progress_select .= <<<HTML
                        <option>ACTIVE</option>
                        <option>FOR RELEASE</option>
                        <option selected>DELIVERED</option>
                    </select>
                HTML;
                break;
            default:
                $shipment_status_progress_select .= <<<HTML
                        <option selected disabled value="">Select a Status Progress</option>
                        <option>ACTIVE</option>
                        <option>FOR RELEASE</option>
                        <option>DELIVERED</option>
                </select>
                HTML;
                break;
        }

        $inner_html_shipment .= <<<HTML
            <input readonly style="display:none;" value="{$data['shipment_details_ref']}" type="text" name="shipment_details_ref">
            <div class="row mb-2">
                <div class="col-6">
                    <label>FORWARDER</label>
                    <input value="{$data['forwarder']}" type="text" class="form-control" name="forwarder">
                </div>
                <div class="col-6">
                    <label>ORIGIN</label>
                    <input value="{$data['origin']}" type="text" class="form-control" name="origin">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>HAWB / AWB</label>
                    <input value="{$data['hawb_awb']}" type="text" class="form-control" name="hawb_awb">
                </div>
                <div class="col-6">
                    <label>ETA</label>
                    <input value="{$data['eta']}" type="date" class="form-control" name="eta">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>GROSS WEIGHT</label>
                    <input value="{$data['gross_weight']}" type="number" step = "0.01" class="form-control" name="gross_weight">
                </div>
                <div class="col-6">
                    <label>CHARGEABLE WEIGHT</label>
                    <input value="{$data['chargeable_weight']}" type="number" step="0.01" class="form-control" name="chargeable_weight">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>NO. OF PACKAGES</label>
                    <input value="{$data['no_packages']}" type="number" step="1" class="form-control" name="no_packages">
                </div>
                <div class="col-6">
                    <label>INVOICE NO.</label>
                    <input value="{$data['invoice_no']}" type="text" class="form-control" name="invoice_no">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>COMMODITY</label>
                    <input value="{$data['commodity']}" type="text" class="form-control" name="commodity">
                </div>
                <div class="col-6">
                    <label>CLASSIFICATION</label>
                    {$classification_select}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>SHIPMENT STATUS</label>
                    <input value="{$data['shipment_status']}" type="text" class="form-control" name="shipment_status">
                </div>
                <div class="col-6">
                    <label>SHIPMENT STATUS PROGRESS</label>
                    {$shipment_status_progress_select}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>INCOTERM</label>
                    <input value="{$data['incoterm']}" type="text" class="form-control" name="incoterm">
                </div>
            </div>
            <div class="row mb-2 d-flex align-items-center">
                <div class="col-3 ml-auto">
                    <button type="submit" class="btn bg-primary btn-block">Update</button>
                </div>
            </div>
        HTML;
    }
    $response_body['inner_html_shipment'] = $inner_html_shipment;

    //details dump for delivery
    $sql = "SELECT a.shipment_details_ref, format(tentative_delivery_schedule, 'yyyy-MM-dd') as tentative_delivery_schedule, format(required_delivery, 'yyyy-MM-dd') as required_delivery, format(actual_date_of_delivery, 'yyyy-MM-dd') as actual_date_of_delivery, time_received, received_by from t_air_delivery_details as a left join t_shipment_air_details as b on a.shipment_details_ref = b.shipment_details_ref where b.hawb_awb = :hawb_awb";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindParam(":hawb_awb", $hawb_awb);
    $stmt -> execute();

    $inner_html_delivery = "";
    if ($data = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $inner_html_delivery .= <<<HTML
            <input readonly style="display:none;" value="{$data['shipment_details_ref']}" type="text" name="shipment_details_ref">
            <div class="row mb-2">
                <div class="col-6">
                    <label>TENTATIVE DELIVERY SCHEDULE</label>
                    <input value="{$data['tentative_delivery_schedule']}" type="date" class="form-control" name="tentative_delivery_schedule">
                </div>
                <div class="col-6">
                    <label>REQUIRED DELIVERY</label>
                    <input value="{$data['required_delivery']}" type="date" class="form-control" name="required_delivery">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>ACTUAL DATE OF DELIVERY</label>
                    <input value="{$data['actual_date_of_delivery']}" type="date" class="form-control" name="actual_date_of_delivery">
                </div>
                <div class="col-6">
                    <label>TIME RECEIVED</label>
                    <input value="{$data['time_received']}" type="text" class="form-control" name="time_received">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label>RECEIVED BY</label>
                    <input value="{$data['received_by']}" type="text" class="form-control" name="received_by">
                </div>
            </div>
            <div class="row mb-2 d-flex align-items-center">
                <div class="col-3 ml-auto">
                    <button type="submit" class="btn bg-primary btn-block">Update</button>
                </div>
            </div>
        HTML;
    }
    $response_body['inner_html_delivery'] = $inner_html_delivery;



    //details dump for history
    //history details
    $sql = "SELECT username, column_name, changed_from, changed_to, format(date_modified, 'yyyy-MM-dd') as date_modified from m_change_history as a left join t_shipment_air_details as b on a.shipment_details_ref = b.shipment_details_ref where b.hawb_awb = :hawb_awb order by date_modified asc";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':hawb_awb', $hawb_awb);
    $stmt -> execute();

    $inner_html_history = "";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['changed_from'] = $row['changed_from'] == null ? "N/A" : $row['changed_from'];
        $row['changed_to'] = $row['changed_to'] == null ? "N/A" : $row['changed_to'];
        $inner_html_history .= <<<HTML
            <tr>
                <td>{$row['date_modified']}</td>
                <td>{$row['username']}</td>
                <td>{$row['column_name']}</td>
                <td style="background-color: #ffcecb;">{$row['changed_from']}</td>
                <td style="background-color: #d1f8d9;">{$row['changed_to']}</td>
            </tr>
        HTML;
    }
    $response_body['inner_html_history'] = $inner_html_history;

    //send
    echo json_encode($response_body);