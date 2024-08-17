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
        $return_body['delivery_plan'] = <<<HTML
            <tr>
                <td style="display: none;">
                    <input readonly value="$shipment_details_ref" type="text" class="form-control" name="shipment_details_ref">
                </td>
                <td>
                    <input value="{$shipment['required_delivery_sched']}" type="text" class="form-control" name="required_delivery_sched">
                </td>
                <td>
                    <input value="{$shipment['deliver_plan']}" type="text" class="form-control" name="deliver_plan">
                </td>
                <td>
                    <input value="{$shipment['tabs']}" type="text" class="form-control" name="tabs">
                </td>
                <td>
                    <button type="submit" class="btn bg-primary btn-block">Update Delivery Details</button>
                </td>
            </tr>
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
            <tr>
                <td style="display: none;">
                    <input readonly value="$shipment_details_ref" type="text" class="form-control" name="shipment_details_ref">
                </td>
                <td>
                    <input type="date" value="{$shipment['date_port_out']}" class="form-control" name="date_port_out">
                </td>
                <td>
                    <input type="date" value="{$shipment['actual_received_at_falp']}" class="form-control" name="actual_received_at_falp">
                </td>
                <td>
                    <button type="submit" class="btn bg-primary btn-block">Update Completion Details</button>
                </td>
            </tr>
        HTML;
    }

    //polytainer details
    $sql = "SELECT polytainer_size, polytainer_quantity, etd from m_polytainer_details where shipment_details_ref = :shipment_details_ref";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(':shipment_details_ref', $shipment_details_ref);
    $stmt -> execute();
    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($shipment) {
        $return_body['polytainer_details'] = <<<HTML
            <tr>
                <td style="display: none;">
                    <input readonly value="$shipment_details_ref" type="text" class="form-control" name="shipment_details_ref">
                </td>
                <td>
                    <input value="{$shipment['polytainer_size']}" type="text" class="form-control" name="polytainer_size">
                </td>
                <td>
                    <input value="{$shipment['polytainer_quantity']}" type="text" class="form-control" name="polytainer_quantity">
                </td>
                <td>
                    <input value="{$shipment['etd']}" type="text" class="form-control" name="etd">
                </td>
                <td>
                    <button type="submit" class="btn bg-primary btn-block">Update Polytainer Details</button>
                </td>
            </tr>
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
            <tr>
                <td style="display: none;">
                    <input readonly value="$shipment_details_ref" type="text" class="form-control" name="shipment_details_ref">
                </td>
                <td>
                    <input value="{$shipment['container_status']}" type="text" class="form-control" name="container_status">
                </td>
                <td>
                    <input value="{$shipment['date_return_reused']}" type="date" class="form-control" name="date_return_reused">
                </td>
                <td>
                    <input readonly value="{$shipment['no_days_port']}" type="text" class="form-control" name="no_days_port">
                </td>
                <td>
                    <input readonly value="{$shipment['no_days_falp']}" type="text" class="form-control" name="no_days_falp">
                </td>
                <td>
                    <button type="submit" class="btn bg-primary btn-block">Update MM System Details</button>
                </td>
            </tr>
        HTML;
    }

    //history details
    $sql = "SELECT column_name, changed_from, changed_to, date_modified from m_change_history where shipment_details_ref = :shipment_details_ref order by date_modified asc";
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
                <td>{$row['column_name']}</td>
                <td>{$row['changed_from']}</td>
                <td>{$row['changed_to']}</td>
            </tr>
        HTML;
    }

    $return_body['success'] = true;
    $return_body['shipment_details_ref'] = $shipment_details_ref;
    echo json_encode($return_body);