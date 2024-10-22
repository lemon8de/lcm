<?php
require 'db_connection.php';
require '../php_static/session_lookup.php';

$outgoing_details_ref = $_POST['outgoing_details_ref'];
$mode_of_shipment = $_POST['mode_of_shipment'] == "" ? null : $_POST['mode_of_shipment'];
$vessel_name = $_POST['vessel_name'] == "" ? null : $_POST['vessel_name'];
$shipping_line = $_POST['shipping_line'] == "" ? null : $_POST['shipping_line'];
$etd_mnl = $_POST['etd_mnl'] == "" ? null : $_POST['etd_mnl'];
$eta_destination = $_POST['eta_destination'] == "" ? null : $_POST['eta_destination'];

//something has to be edited
if ($mode_of_shipment == "" && $vessel_name == "" && $shipping_line == "" && $etd_mnl == "" && $eta_destination == "") {
    $notification = [
        "icon" => "warning",
        "text" => "No Edits Made",
    ];
    $return_body = [];
    $return_body['notification'] = $notification;
    echo json_encode($return_body);
    exit();
}

//outgoing_details_ref can be a string or an array, depending on if its bulk editing or not
// Ensure $outgoing_details_ref is always an array
if (!is_array($outgoing_details_ref)) {
    // It's a string, convert it to an array
    $outgoing_details_ref = [$outgoing_details_ref];
}

//clean outgoing_details_ref, these ids might have "ck-" at the start because bulk editing gives checklist ids
foreach ($outgoing_details_ref as $key => $item) {
    $outgoing_details_ref[$key] = str_replace("ck-", "", $item);
}

$sql = "SELECT * from m_outgoing_vessel_details where outgoing_details_ref = :outgoing_details_ref";
$stmt_find = $conn->prepare($sql);

$sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to, username) values (:outgoing_details_ref, :table_name, :column_name, :changed_from, :changed_to, :username)";
$stmt_history = $conn -> prepare($sql);
$stmt_history -> bindParam(":username", $_SESSION['username']);
$stmt_history -> bindValue(':table_name', 'm_outgoing_vessel_details');


//this entire block is for history logging
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
    for ($i = 0; $i < count($shipment_keys); $i++) {
        if ($compare_set[$i] !== $shipment_values[$i] && $compare_set[$i] !== null) {
            //insert into changes table
            $stmt_history -> bindParam(':column_name', $shipment_keys[$i]);
            $stmt_history -> bindParam(':changed_from', $shipment_values[$i]);
            $stmt_history -> bindParam(':changed_to', $compare_set[$i]);
            $stmt_history -> execute();
        }
    }
}

$placeholders = implode(', ', array_fill(0, count($outgoing_details_ref), '?'));;
//query build the thing
$sql = "UPDATE m_outgoing_vessel_details set";
$to_update = [];
if ($mode_of_shipment !== null) {
    $sql .= " mode_of_shipment = ?,";
    array_push($to_update, $mode_of_shipment);
}
if ($vessel_name !== null) {
    $sql .= " vessel_name = ?,";
    array_push($to_update, $vessel_name);
}
if ($shipping_line !== null) {
    $sql .= " shipping_line = ?,";
    array_push($to_update, $shipping_line);
}
if ($etd_mnl !== null) {
    $sql .= " etd_mnl = ?,";
    array_push($to_update, $etd_mnl);
}
if ($eta_destination !== null) {
    $sql .= " eta_destination = ?,";
    array_push($to_update, $eta_destination);
}

$sql = substr($sql, 0, -1) . " WHERE outgoing_details_ref IN ($placeholders)";
$stmt = $conn -> prepare($sql);
$stmt -> execute(array_merge($to_update, $outgoing_details_ref));

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