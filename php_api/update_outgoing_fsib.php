<?php
require 'db_connection.php';

$outgoing_details_ref = $_POST['outgoing_details_ref'];
$invoice_no = $_POST['invoice_no'] == "" ? null : $_POST['invoice_no'];
$container_no = $_POST['container_no'] == "" ? null : $_POST['container_no'];
$destination_service_center = $_POST['destination_service_center'] == "" ? null : $_POST['destination_service_center'];
$destination = $_POST['destination'] == "" ? null : $_POST['destination'];
$car_model = $_POST['car_model'] == "" ? null : $_POST['car_model'];
$ship_out_date = $_POST['ship_out_date'] == "" ? null : $_POST['ship_out_date'];
$no_pallets = $_POST['no_pallets'] == "" ? null : $_POST['no_pallets'];
$no_cartons = $_POST['no_cartons'] == "" ? null : $_POST['no_cartons'];
$pack_qty = $_POST['pack_qty'] == "" ? null : $_POST['pack_qty'];
$invoice_amount = $_POST['invoice_amount'] == "" ? null : $_POST['invoice_amount'];

$sql = "SELECT * from m_outgoing_fsib where outgoing_details_ref = :outgoing_details_ref";
$stmt = $conn->prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> execute();
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

$compare_set = array($outgoing_details_ref, $invoice_no, $container_no, $destination_service_center, $destination, $car_model, $ship_out_date, $no_pallets, $no_cartons, $pack_qty, $invoice_amount);
if ($shipment) {
    unset($shipment['id']);
    $shipment_keys = array_keys($shipment);
    $shipment_values = array_values($shipment);
    if ($shipment_values[6] != null) {
        $shipment_values[6] = substr($shipment_values[6], 0, 10);
    }
}

$sql = "INSERT into m_change_history (shipment_details_ref, table_name, column_name, changed_from, changed_to) values (:outgoing_details_ref, :table_name, :column_name, :changed_from, :changed_to)";
$stmt = $conn -> prepare($sql);
$stmt -> bindValue(':outgoing_details_ref', $outgoing_details_ref);
$stmt -> bindValue(':table_name', 'm_outgoing_fsib');

for ($i = 0; $i < count($shipment_keys); $i++) {
    if ($compare_set[$i] !== $shipment_values[$i]) {
        //insert into changes table
        $stmt -> bindParam(':column_name', $shipment_keys[$i]);
        $stmt -> bindParam(':changed_from', $shipment_values[$i]);
        $stmt -> bindParam(':changed_to', $compare_set[$i]);
        $stmt -> execute();
    }
}
$sql = "UPDATE m_outgoing_fsib set invoice_no = ?, container_no = ?, destination_service_center = ?, destination = ?, car_model = ?, ship_out_date = ?, no_pallets = ?, no_cartons = ?, pack_qty = ?, invoice_amount = ? where outgoing_details_ref = ?";
$stmt = $conn -> prepare($sql);
$stmt -> execute([$invoice_no, $container_no, $destination_service_center, $destination, $car_model, $ship_out_date, $no_pallets, $no_cartons, $pack_qty, $invoice_amount, $outgoing_details_ref]);

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