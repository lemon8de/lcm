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
                    <input type="date" value="{$shipment['date_port_out']}" type="text" class="form-control" name="date_port_out">
                </td>
                <td>
                    <input type="date" value="{$shipment['actual_received_at_falp']}" type="text" class="form-control" name="actual_received_at_falp">
                </td>
                <td>
                    <button type="submit" class="btn bg-primary btn-block">Update Completion Details</button>
                </td>
            </tr>
        HTML;
    }


    $return_body['success'] = true;
    $return_body['shipment_details_ref'] = $shipment_details_ref;
    echo json_encode($return_body);