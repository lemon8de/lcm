<?php
require 'db_connection.php';

$outgoing_details_ref = $_POST['outgoing_details_ref'];
$shipping_terms = $_POST['shipping_terms'] == "" ? null : $_POST['shipping_terms'];
$net_weight = $_POST['net_weight'] == "" ? null : round(floatval($_POST['net_weight']), 4);
$gross_weight = $_POST['gross_weight'] == "" ? null : round(floatval($_POST['gross_weight']), 4);
$cbm = $_POST['cbm'] == "" ? null : round(floatval($_POST['cbm']), 4);

$sql = "SELECT * from m_outgoing_invoice_details where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn->prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> execute();
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

$compare_set = array($outgoing_details_ref, $shipping_terms, $net_weight, $gross_weight, $cbm);
if ($shipment) {
    unset($shipment['id']);
    $shipment_keys = array_keys($shipment);
    $shipment_values = array_values($shipment);
    if (isset($shipment_values[2])) {
        $shipment_values[2] = round($shipment_values[2], 4);
    }
    if (isset($shipment_values[3])) {
        $shipment_values[3] = round($shipment_values[3], 4);
    }
    if (isset($shipment_values[4])) {
        $shipment_values[4] = round($shipment_values[4], 4);
    }
}

$sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:outgoing_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
$stmt = $conn -> prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> bindValue(':table_name', 'm_outgoing_invoice_details');

for ($i = 0; $i < count($shipment_keys); $i++) {
    if ($compare_set[$i] !== $shipment_values[$i]) {
        //insert into changes table
        $stmt -> bindParam(':column_name', $shipment_keys[$i]);
        $stmt -> bindParam(':changed_from', $shipment_values[$i]);
        $stmt -> bindParam(':changed_to', $compare_set[$i]);
        $stmt -> execute();
    }
}

$sql = "UPDATE m_outgoing_invoice_details set shipping_terms = ?, net_weight = ?, gross_weight = ?, cbm = ? where outgoing_details_ref = ?";
$stmt = $conn -> prepare($sql);
$stmt -> execute([$shipping_terms, $net_weight, $gross_weight, $cbm, $outgoing_details_ref]);

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