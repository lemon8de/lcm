<?php
require 'db_connection.php';
require '../php_static/session_lookup.php';

$outgoing_details_ref = $_POST['outgoing_details_ref'];
$ed_reference = $_POST['ed_reference'] == "" ? null : $_POST['ed_reference'];
$shipping_seal = $_POST['shipping_seal'] == "" ? null : $_POST['shipping_seal'];
$peza_seal = $_POST['peza_seal'] == "" ? null : $_POST['peza_seal'];

$sql = "SELECT * from m_outgoing_dispatching_details where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn->prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> execute();
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

$compare_set = array($outgoing_details_ref, $ed_reference, $shipping_seal, $peza_seal);
if ($shipment) {
    unset($shipment['id']);
    $shipment_keys = array_keys($shipment);
    $shipment_values = array_values($shipment);
}

$sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to, username) values (:shipment_details_ref, :table_name, :column_name, :changed_from, :changed_to, :username)";
$stmt = $conn -> prepare($sql);
$stmt -> bindParam(":username", $_SESSION['username']);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> bindValue(':table_name', 'm_outgoing_dispatch');

for ($i = 0; $i < count($shipment_keys); $i++) {
    if ($compare_set[$i] !== $shipment_values[$i]) {
        //insert into changes table
        $stmt -> bindParam(':column_name', $shipment_keys[$i]);
        $stmt -> bindParam(':changed_from', $shipment_values[$i]);
        $stmt -> bindParam(':changed_to', $compare_set[$i]);
        $stmt -> execute();
    }
}
$sql = "UPDATE m_outgoing_dispatching_details set ed_reference = ?, shipping_seal = ?, peza_seal = ? where outgoing_details_ref = ?";
$stmt = $conn -> prepare($sql);
$stmt -> execute([$ed_reference, $shipping_seal, $peza_seal, $outgoing_details_ref]);

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