<?php
require 'db_connection.php';

$outgoing_details_ref = $_POST['outgoing_details_ref'];
$mode_of_shipment = $_POST['mode_of_shipment'] == "" ? null : $_POST['mode_of_shipment'];
$vessel_name = $_POST['vessel_name'] == "" ? null : $_POST['vessel_name'];
$shipping_line = $_POST['shipping_line'] == "" ? null : $_POST['shipping_line'];
$etd_mnl = $_POST['etd_mnl'] == "" ? null : $_POST['etd_mnl'];
$eta_destination = $_POST['eta_destination'] == "" ? null : $_POST['eta_destination'];

//outgoing_details_ref can be a string or an array, depending on if its bulk editing or not
// Ensure $outgoing_details_ref is always an array
if (!is_array($outgoing_details_ref)) {
    // It's a string, convert it to an array
    $outgoing_details_ref = [$outgoing_details_ref];
}

//clean outgoing_details_ref, these ids might have "ck-" at the start because they are checklist id
foreach ($outgoing_details_ref as $key => $item) {
    $outgoing_details_ref[$key] = str_replace("ck-", "", $item);
}

$sql = "SELECT * from m_outgoing_vessel_details where outgoing_details_ref = :outgoing_details_ref";
$stmt_find = $conn->prepare($sql);

$sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:outgoing_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
$stmt_history = $conn -> prepare($sql);


foreach ($outgoing_details_ref as $id) {
    $stmt_find -> bindValue(':outgoing_details_ref', $id);
    $stmt_find -> execute();
    $shipment = $stmt_find->fetch(PDO::FETCH_ASSOC);

    $compare_set = array($id, $mode_of_shipment, $vessel_name, $shipping_line, $etd_mnl, $eta_destination);
    if ($shipment) {
        unset($shipment['id']);
        $shipment_keys = array_keys($shipment);
        $shipment_values = array_values($shipment);
        if ($shipment_values[4] != null) {
            $shipment_values[4] = substr($shipment_values[4], 0, 10);
        }
        if ($shipment_values[5] != null) {
            $shipment_values[5] = substr($shipment_values[5], 0, 10);
        }
    }

    $stmt_history -> bindValue(':outgoing_details_ref', $id);
    $stmt_history -> bindValue(':table_name', 'm_outgoing_vessel_details');

    for ($i = 0; $i < count($shipment_keys); $i++) {
        if ($compare_set[$i] !== $shipment_values[$i]) {
            //insert into changes table
            $stmt_history -> bindParam(':column_name', $shipment_keys[$i]);
            $stmt_history -> bindParam(':changed_from', $shipment_values[$i]);
            $stmt_history -> bindParam(':changed_to', $compare_set[$i]);
            $stmt_history -> execute();
        }
    }
}

$placeholders = implode(', ', array_fill(0, count($outgoing_details_ref), '?'));;
$sql = "UPDATE m_outgoing_vessel_details set mode_of_shipment = ?, vessel_name = ?, shipping_line = ?, etd_mnl = ?, eta_destination = ? where outgoing_details_ref IN ($placeholders)";

//$sql = "UPDATE m_outgoing_vessel_details set mode_of_shipment = ?, vessel_name = ?, shipping_line = ?, etd_mnl = ?, eta_destination = ? where outgoing_details_ref = ?";
$stmt = $conn -> prepare($sql);
$stmt -> execute(array_merge([$mode_of_shipment, $vessel_name, $shipping_line, $etd_mnl, $eta_destination], $outgoing_details_ref ));

$conn = null;
//header('location: ../pages/incoming_sea.php');
//exit();
$notification = [
    "icon" => "success",
    "text" => "Details Updated",
];
$return_body = [];
$return_body['notification'] = $notification;

echo json_encode($return_body);