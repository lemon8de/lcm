<?php
require 'db_connection.php';

$outgoing_details_ref = $_POST['outgoing_details_ref'];
$bl_date = $_POST['bl_date'] == "" ? null : $_POST['bl_date'];
$bl_number = $_POST['bl_number'] == "" ? null : $_POST['bl_number'];

$sql = "SELECT * from m_outgoing_bl_details where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn->prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> execute();
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

$compare_set = array($outgoing_details_ref, $bl_date, $bl_number);
if ($shipment) {
    unset($shipment['id']);
    $shipment_keys = array_keys($shipment);
    $shipment_values = array_values($shipment);
    if (isset($shipment_values[1])) {
        $shipment_values[1] = substr($shipment_values[1], 0, 10);
    }
}

$sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:outgoing_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
$stmt = $conn -> prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> bindValue(':table_name', 'm_outgoing_bl_details');

for ($i = 0; $i < count($shipment_keys); $i++) {
    if ($compare_set[$i] !== $shipment_values[$i]) {
        //insert into changes table
        $stmt -> bindParam(':column_name', $shipment_keys[$i]);
        $stmt -> bindParam(':changed_from', $shipment_values[$i]);
        $stmt -> bindParam(':changed_to', $compare_set[$i]);
        $stmt -> execute();
    }
}
$sql = "UPDATE m_outgoing_bl_details set bl_date = ?, bl_number = ? where outgoing_details_ref = ?";
$stmt = $conn -> prepare($sql);
$stmt -> execute([$bl_date, $bl_number, $outgoing_details_ref]);

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