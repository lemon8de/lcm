<?php
require 'db_connection.php';

$outgoing_details_ref = $_POST['outgoing_details_ref'];
$mode_of_shipment = $_POST['mode_of_shipment'] == "" ? null : $_POST['mode_of_shipment'];
$vessel_name = $_POST['vessel_name'] == "" ? null : $_POST['vessel_name'];
$shipping_line = $_POST['shipping_line'] == "" ? null : $_POST['shipping_line'];
$etd_mnl = $_POST['etd_mnl'] == "" ? null : $_POST['etd_mnl'];
$eta_destination = $_POST['eta_destination'] == "" ? null : $_POST['eta_destination'];

$sql = "SELECT * from m_outgoing_vessel_details where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn->prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> execute();
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

$compare_set = array($outgoing_details_ref, $mode_of_shipment, $vessel_name, $shipping_line, $etd_mnl, $eta_destination);
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

$sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:outgoing_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
$stmt = $conn -> prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> bindValue(':table_name', 'm_outgoing_vessel_details');

for ($i = 0; $i < count($shipment_keys); $i++) {
    if ($compare_set[$i] !== $shipment_values[$i]) {
        //insert into changes table
        $stmt -> bindParam(':column_name', $shipment_keys[$i]);
        $stmt -> bindParam(':changed_from', $shipment_values[$i]);
        $stmt -> bindParam(':changed_to', $compare_set[$i]);
        $stmt -> execute();
    }
}

$sql = "UPDATE m_outgoing_vessel_details set mode_of_shipment = ?, vessel_name = ?, shipping_line = ?, etd_mnl = ?, eta_destination = ? where outgoing_details_ref = ?";
$stmt = $conn -> prepare($sql);
$stmt -> execute([$mode_of_shipment, $vessel_name, $shipping_line, $etd_mnl, $eta_destination, $outgoing_details_ref]);

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