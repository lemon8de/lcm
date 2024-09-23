<?php
require 'db_connection.php';

$outgoing_details_ref = $_POST['outgoing_details_ref'];
$falp_out_date = $_POST['falp_out_date'] == "" ? null : $_POST['falp_out_date'];
$falp_out_time = $_POST['falp_out_time'] == "" ? null : $_POST['falp_out_time'];
$truckhead_status = $_POST['truckhead_status'] == "" ? null : $_POST['truckhead_status'];


$sql = "SELECT * from m_outgoing_cont_lineup where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn->prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> execute();
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

$compare_set = array($outgoing_details_ref, $falp_out_date, $falp_out_time, $truckhead_status);
if ($shipment) {
    unset($shipment['id']);
    $shipment_keys = array_keys($shipment);
    $shipment_values = array_values($shipment);
    if ($shipment_values[1] != null) {
        $shipment_values[1] = substr($shipment_values[1], 0, 10);
    }
}

$sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:outgoing_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
$stmt = $conn -> prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> bindValue(':table_name', 'm_outgoing_cont_lineup');

for ($i = 0; $i < count($shipment_keys); $i++) {
    if ($compare_set[$i] !== $shipment_values[$i]) {
        //insert into changes table
        $stmt -> bindParam(':column_name', $shipment_keys[$i]);
        $stmt -> bindParam(':changed_from', $shipment_values[$i]);
        $stmt -> bindParam(':changed_to', $compare_set[$i]);
        $stmt -> execute();
    }
}
$sql = "UPDATE m_outgoing_cont_lineup set falp_out_date = ?, falp_out_time = ?, truckhead_status = ? where outgoing_details_ref = ?";
$stmt = $conn -> prepare($sql);
$stmt -> execute([$falp_out_date, $falp_out_time, $truckhead_status, $outgoing_details_ref]);

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