<?php
require 'db_connection.php';

$outgoing_details_ref = $_POST['outgoing_details_ref'];
//$mode_of_shipment = $_POST['mode_of_shipment'] == "" ? null : $_POST['mode_of_shipment'];
$vessel_name = $_POST['vessel_name'] == "" ? null : $_POST['vessel_name'];
$shipping_line = $_POST['shipping_line'] == "" ? null : $_POST['shipping_line'];
$etd_mnl = $_POST['etd_mnl'] == "" ? null : $_POST['etd_mnl'];
$eta_destination = $_POST['eta_destination'] == "" ? null : $_POST['eta_destination'];

//clean outgoing_details_ref, these ids have "ck-" at the start because its the checklist id
foreach ($outgoing_details_ref as $key => $item) {
    $outgoing_details_ref[$key] = substr($item, 3);
}

$sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:outgoing_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
$stmt = $conn -> prepare($sql);
$stmt -> bindValue(':table_name', 'm_outgoing_vessel_details');

$columns = [
    "vessel_name" => $vessel_name,
    "shipping_line" => $shipping_line,
    "etd_mnl" => $etd_mnl,
    "eta_destination" => $eta_destination
];
$sql_find = "SELECT vessel_name, shipping_line, etd_mnl, eta_destination from m_outgoing_vessel_details where outgoing_details_ref = ?";
$stmt_find = $conn -> prepare($sql_find);
foreach ($outgoing_details_ref as $id) {
    $stmt_find -> execute([$id]);
    $stmt -> bindParam(":outgoing_details_ref", $id);
    if ($data_old = $stmt_find -> fetch(PDO::FETCH_ASSOC)) {
        foreach ($columns as $key => $value) {
            //insert into changes table
            $stmt -> bindParam(':column_name', $key);
            $stmt -> bindParam(':changed_from', $data_old[$key]);
            $stmt -> bindParam(':changed_to', $value);
            $stmt -> execute();
        }
    }
}

$placeholders = implode(', ', array_fill(0, count($outgoing_details_ref), '?'));;
$sql = "UPDATE m_outgoing_vessel_details set vessel_name = ?, shipping_line = ?, etd_mnl = ?, eta_destination = ? where outgoing_details_ref IN ($placeholders)";

$stmt = $conn -> prepare($sql);
$stmt -> execute(array_merge([$vessel_name, $shipping_line, $etd_mnl, $eta_destination], $outgoing_details_ref));

$conn = null;
//header('location: ../pages/incoming_sea.php');
//exit();
$notification = [
    "icon" => "success",
    "text" => "All Details Updated",
];
$return_body = [];
$return_body['notification'] = $notification;

echo json_encode($return_body);