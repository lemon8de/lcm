<?php
    require '../db_connection.php';
    require '../../php_static/session_lookup.php';

    $sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to, username) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to, :username)";
    $stmt_history = $conn -> prepare($sql);
    $stmt_history -> bindValue(":table_name", 'm_outgoing_invoice_details');
    $stmt_history -> bindValue(":username", $_SESSION['username']);

    $sql = "SELECT * from m_outgoing_vessel_details where outgoing_details_ref = :outgoing_details_ref";
    $stmt_get = $conn -> prepare($sql);

    $ids = $_POST['outgoing_details_ref'];
    unset($_POST['outgoing_details_ref']);
    foreach ($ids as $outgoing_details_ref) {
        $id = str_replace("ck-", "", $outgoing_details_ref);
        $stmt_get -> bindParam(":outgoing_details_ref", $id);
        $stmt_get -> execute();
        $data = $stmt_get -> fetch(PDO::FETCH_ASSOC);

        foreach($_POST as $key => $value) {
            if ($value !== "") {
                $stmt_history -> bindParam(":shipment_details_ref", $id);
                $stmt_history -> bindParam(":column_name", $key);
                $stmt_history -> bindParam(":changed_from", $data[$key]);
                $stmt_history -> bindParam(":changed_to", $value);
                $stmt_history -> execute();
            } else {
                //do nothing
            }
        }
    }
    $ids_string = implode(',', $ids);
    $ids_string = str_replace('ck-', '', $ids_string);

    $sql = "EXEC Outgoing_BULK_Invoice :ShippingTerms, :NetWeight, :GrossWeight, :CBM, :OutgoingDetailRefs";
    $stmt_update = $conn -> prepare($sql);
    $stmt_update -> bindParam(":ShippingTerms", $_POST['shipping_terms']);
    $stmt_update -> bindParam(":NetWeight", $_POST['net_weight']);
    $stmt_update -> bindParam(":GrossWeight", $_POST['gross_weight']);
    $stmt_update -> bindParam(":CBM", $_POST['cbm']);
    $stmt_update -> bindParam(":OutgoingDetailRefs", $ids_string);

    $stmt_update -> execute();

    $notification = [
            "icon" => "success",
            "text" => "Details Updated",
    ];
    $return_body = [];
    $return_body['notification'] = $notification;
    echo json_encode($return_body);
    exit();